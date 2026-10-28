<?php

namespace App\Controllers;

use App\Models\PostModel;
use App\Models\CommentModel;
use App\Models\TagModel;
use App\Models\CategoryModel;
use App\Models\FaqModel;

class Blog extends BaseController
{
    public function index()
    {
        $postModel   = new PostModel();
        $tagModel    = new TagModel();
        $categoryModel = new CategoryModel();

        $builder = $postModel->select('posts.*, categories.name as category_name')
            ->join('categories', 'categories.id = posts.category_id', 'left')
            ->where('posts.status', 'published');

        // Filter by tag: /blog?tag=perawatan-jersey
        $tagSlug = $this->request->getGet('tag');
        $activeTag = null;
        if ($tagSlug) {
            $activeTag = $tagModel->where('slug', $tagSlug)->first();
            if ($activeTag) {
                $builder->join('post_tags', 'post_tags.post_id = posts.id', 'inner')
                    ->where('post_tags.tag_id', $activeTag['id']);
            }
        }

        // Filter by category: /blog?category=edukasi-tips
        $catSlug = $this->request->getGet('category');
        $activeCat = null;
        if ($catSlug) {
            $activeCat = $categoryModel->where('slug', $catSlug)->where('type', 'blog')->first();
            if ($activeCat) {
                $builder->where('posts.category_id', $activeCat['id']);
            }
        }

        $posts = $builder->orderBy('posts.created_at', 'DESC')->findAll();
        $categories = $categoryModel->where('type', 'blog')->findAll();

        // Count per kategori via 1 query GROUP BY (anti N+1)
        $countsByCat = $postModel->countPublishedByCategory();
        foreach ($categories as &$cat) {
            $cat['count'] = $countsByCat[(int)$cat['id']] ?? 0;
        }
        unset($cat);

        $totalPosts = $postModel->countPublished();

        // Dynamic SEO title/desc for category & tag archive pages
        $filterName = null;
        if ($activeCat) {
            $filterName = $activeCat['name'];
        } elseif ($activeTag) {
            $filterName = $activeTag['name'];
        }

        $defaultTitle = 'Blog & Tips Edukasi Jersey — Pusat Jersey';
        $defaultDesc  = 'Panduan memilih bahan jersey, tips merawat baju olahraga, rekomendasi tren desain tim futsal dan sepakbola.';
        $seoTitle = $filterName ? 'Artikel ' . $filterName . ' — Blog Pusat Jersey' : $defaultTitle;
        $seoDesc  = $filterName ? 'Kumpulan artikel seputar ' . $filterName . ' dari Pusat Jersey Indonesia. Tips & panduan terbaik untuk tim Anda.' : $defaultDesc;

        $seoData = [
            'meta_title' => $seoTitle,
            'meta_description' => $seoDesc,
            'breadcrumbs' => [
                ['name' => 'Beranda', 'url' => '/'],
                ['name' => 'Blog', 'url' => '/blog'],
            ],
        ];
        if ($filterName) {
            $seoData['breadcrumbs'][] = ['name' => $filterName, 'url' => current_url()];
        }

        $data = [
            'metaTitle'  => $seoTitle,
            'metaDesc'   => $seoDesc,
            'posts'      => $posts,
            'categories' => $categories,
            'activeCat'  => $catSlug,
            'tagSlug'    => $tagSlug,
            'totalPosts' => $totalPosts,
        ];

        return $this->renderView('blog/index', $data, $seoData);
    }

    public function detail($slug)
    {
        $postModel    = new PostModel();
        $commentModel = new CommentModel();
        $tagModel     = new TagModel();

        $post = $postModel->select('posts.*, categories.name as category_name')
            ->join('categories', 'categories.id = posts.category_id', 'left')
            ->where('posts.slug', $slug)
            ->where('status', 'published')
            ->first();

        if (!$post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Artikel blog tidak ditemukan');
        }

        $tags = $tagModel->getTagsForPost((int)$post['id']);

        // FAQs for this post (post_type = 'blog', target_id = this post id)
        $faqModel = new FaqModel();
        $faqs = $faqModel->where('post_type', 'blog')
            ->where('target_id', $post['id'])
            ->orderBy('sort_order', 'ASC')
            ->findAll();
        if (empty($faqs)) {
            // Fallback to global FAQs
            $faqs = $faqModel->where('post_type', 'global')->orderBy('sort_order', 'ASC')->findAll(3);
        }

        // Increment view count secara atomik (hindari race condition read-modify-write).
        // Bot/crawler di-skip agar count tetap realistis.
        // Catatan: BaseBuilder::increment($column, $value) — WHERE di-set via where() dulu.
        if (!$this->isBotRequest()) {
            $postModel->where('id', (int)$post['id'])->increment('views_count', 1);
            $post['views_count']++;
        }

        $comments = $commentModel->where('post_id', $post['id'])
            ->where('status', 'approved')
            ->orderBy('created_at', 'ASC')
            ->findAll();

        $extraSchemas = [
            [
                '@context' => 'https://schema.org',
                '@type'    => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => base_url()],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => base_url('blog')],
                    ['@type' => 'ListItem', 'position' => 3, 'name' => $post['title'], 'item' => current_url()],
                ]
            ],
            [
                '@context' => 'https://schema.org',
                '@type'    => 'BlogPosting',
                'headline' => $post['title'],
                'image'    => $post['featured_image'],
                'datePublished' => $post['created_at'],
                'author'   => [
                    '@type' => 'Organization',
                    'name'  => 'Pusat Jersey Indonesia'
                ]
            ]
        ];

        $data = [
            'metaTitle'    => $post['meta_title'] ?: ($post['title'] . ' — Blog Pusat Jersey'),
            'metaDesc'     => $post['meta_description'] ?: $post['excerpt'],
            'post'         => $post,
            'tags'         => $tags,
            'comments'     => $comments,
            'faqs'         => $faqs,
            'extraSchemas' => $extraSchemas,
        ];

        return $this->renderView('blog/detail', $data, ['faqs' => $faqs]);
    }

    /**
     * Deteksi bot/crawler sederhana dari user agent untuk skip view counter.
     */
    private function isBotRequest(): bool
    {
        $ua = (string)($this->request->getUserAgent()->getAgentString() ?? '');
        if ($ua === '') {
            return true;
        }
        return (bool)preg_match('/bot|crawl|slurp|spider|facebookexternalhit|linkedin|twitterbot|whatsapp|curl|wget|python|headless/i', $ua);
    }

    public function addComment($slug)
    {
        $postModel    = new PostModel();
        $commentModel = new CommentModel();

        $post = $postModel->where('slug', $slug)->first();
        if (!$post) {
            return '<div class="p-4 bg-rose-500/10 text-rose-400 rounded-xl text-xs">Artikel tidak ditemukan.</div>';
        }

        // --- Rate limit anti-spam: maks 3 komentar per 10 menit per IP ---
        $ip    = $this->request->getIPAddress();
        $key   = 'comment_rl_' . md5($ip);
        $hits  = (int)(cache()->get($key) ?? 0);
        if ($hits >= 3) {
            return '<div class="p-4 bg-amber-500/10 border border-amber-500/30 text-amber-500 rounded-xl text-xs">Terlalu banyak komentar terkirim. Coba lagi dalam beberapa menit.</div>';
        }

        // --- Validasi input ---
        $rules = [
            'author_name'  => 'required|string|min_length[2]|max_length[100]',
            'author_email' => 'required|valid_email|max_length[190]',
            'content'      => 'required|string|min_length[3]|max_length[2000]',
            'parent_id'    => 'permit_empty|is_natural_no_zero',
            'author_website' => 'permit_empty|valid_url_strict|max_length[255]',
        ];
        if (!$this->validateData($this->request->getPost() ?? [], $rules)) {
            $firstError = $this->validator->getErrors()[array_key_first($this->validator->getErrors())] ?? 'Input tidak valid.';
            return '<div class="p-4 bg-rose-500/10 border border-rose-500/30 text-rose-400 rounded-xl text-xs">' . esc($firstError) . '</div>';
        }

        $name    = trim($this->request->getPost('author_name'));
        $email   = trim($this->request->getPost('author_email'));
        $content = trim($this->request->getPost('content'));

        // parent_id harus benar-benar milik post yang sama (cegah manipulasi threading)
        $parentId   = $this->request->getPost('parent_id');
        $parentId   = ($parentId !== null && $parentId !== '' && (int)$parentId > 0) ? (int)$parentId : null;
        if ($parentId !== null) {
            $parent = $commentModel->where('id', $parentId)->where('post_id', $post['id'])->first();
            if (!$parent) {
                $parentId = null;
            }
        }

        $commentModel->insert([
            'post_id'        => (int)$post['id'],
            'parent_id'      => $parentId,
            'author_name'    => $name,
            'author_email'   => $email,
            'author_website' => $this->request->getPost('author_website') ?: null,
            'content'        => $content,
            'status'         => 'pending', // menunggu moderasi admin
            'ip_address'     => $ip,
            'user_agent'     => substr((string)$this->request->getUserAgent()->getAgentString(), 0, 255),
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        cache()->save($key, $hits + 1, 600);

        // Tampilkan pesan sukses (komentar pending tidak langsung tampil)
        return '<div class="p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-500 rounded-xl text-xs">
            Komentar Anda terkirim dan menunggu moderasi admin. Terima kasih!
        </div>';
    }
}
