<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PostModel;
use App\Models\CategoryModel;
use App\Models\TagModel;

class Blog extends BaseController
{
    public function index()
    {
        $postModel = new PostModel();
        $posts = $postModel->select('posts.*, categories.name as category_name')
            ->join('categories', 'categories.id = posts.category_id', 'left')
            ->orderBy('posts.created_at', 'DESC')
            ->findAll();

        $data = [
            'metaTitle' => 'Manajemen Blog & Artikel — Admin Pusat Jersey',
            'posts'     => $posts,
        ];

        return $this->renderAdminView('admin/blog/index', $data);
    }

    public function create()
    {
        $categoryModel = new CategoryModel();
        $data = [
            'metaTitle'  => 'Tulis Artikel Blog Baru',
            'post'       => null,
            'categories' => $categoryModel->where('type', 'blog')->findAll(),
        ];

        return $this->renderAdminView('admin/blog/form', $data);
    }

    private const STATUSES = ['published', 'draft', 'archived'];

    public function store()
    {
        $postModel = new PostModel();
        $title  = trim($this->request->getPost('title') ?? '');
        $slug   = unique_slug(url_title($title, '-', true), 'posts', 'slug');
        $status = $this->request->getPost('status') ?: 'draft';

        if (!$this->validatePost()) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $postId = $postModel->insert([
            'category_id'      => $this->request->getPost('category_id') ?: null,
            'title'            => $title,
            'slug'             => $slug,
            'excerpt'          => $this->request->getPost('excerpt') ?: null,
            'content'          => $this->request->getPost('content') ?: '',
            'featured_image'   => $this->request->getPost('featured_image') ?: null,
            'status'           => $status,
            'meta_title'       => $this->request->getPost('meta_title') ?: null,
            'meta_description' => $this->request->getPost('meta_description') ?: null,
        ], true);

        $this->syncTags((int)$postId, (string)$this->request->getPost('tags'));

        return redirect()->to('/admin/blog');
    }

    public function edit($id)
    {
        $postModel     = new PostModel();
        $categoryModel = new CategoryModel();

        $post = $postModel->find($id);
        if (!$post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Artikel tidak ditemukan');
        }

        $data = [
            'metaTitle'  => 'Edit Artikel — ' . $post['title'],
            'post'       => $post,
            'categories' => $categoryModel->where('type', 'blog')->findAll(),
            'tagList'    => implode(', ', array_column((new TagModel())->getTagsForPost((int)$post['id']), 'name')),
        ];

        return $this->renderAdminView('admin/blog/form', $data);
    }

    public function update($id)
    {
        $postModel = new PostModel();

        if (!$postModel->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Artikel tidak ditemukan');
        }

        $title  = trim($this->request->getPost('title') ?? '');
        $status = $this->request->getPost('status') ?: 'draft';

        if (!$this->validatePost()) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $inputSlug = trim((string)$this->request->getPost('slug'));
        $baseSlug  = $inputSlug !== '' ? $inputSlug : $title;
        $slug      = unique_slug(url_title($baseSlug, '-', true), 'posts', 'slug', (int)$id);

        $postModel->update($id, [
            'category_id'      => $this->request->getPost('category_id') ?: null,
            'title'            => $title,
            'slug'             => $slug,
            'excerpt'          => $this->request->getPost('excerpt') ?: null,
            'content'          => $this->request->getPost('content') ?: '',
            'featured_image'   => $this->request->getPost('featured_image') ?: null,
            'status'           => $status,
            'meta_title'       => $this->request->getPost('meta_title') ?: null,
            'meta_description' => $this->request->getPost('meta_description') ?: null,
        ]);

        $this->syncTags((int)$id, (string)$this->request->getPost('tags'));

        return redirect()->to('/admin/blog');
    }

    /**
     * Sync post ↔ tags: find-or-create each tag, replace post_tags links.
     */
    private function syncTags(int $postId, string $rawTags): void
    {
        $tagModel = new TagModel();
        $db       = \Config\Database::connect();

        $db->table('post_tags')->where('post_id', $postId)->delete();

        $names = array_filter(array_map('trim', explode(',', $rawTags)));
        foreach ($names as $name) {
            $slug = url_title($name, '-', true);
            $tag  = $tagModel->where('slug', $slug)->first();
            $tagId = $tag ? $tag['id'] : $tagModel->insert(['name' => $name, 'slug' => $slug], true);
            if ($tagId) {
                $db->table('post_tags')->insert(['post_id' => $postId, 'tag_id' => $tagId]);
            }
        }
    }

    public function delete($id)
    {
        $postModel = new PostModel();
        $postModel->delete($id);

        return redirect()->to('/admin/blog');
    }

    /**
     * Validasi input form artikel (dipakai store & update).
     */
    private function validatePost(): bool
    {
        return $this->validateData($this->request->getPost() ?? [], [
            'title'            => 'required|string|min_length[3]|max_length[255]',
            'status'           => 'required|in_list[' . implode(',', self::STATUSES) . ']',
            'excerpt'          => 'permit_empty|string|max_length[500]',
            'featured_image'   => 'permit_empty|max_length[255]',
            'meta_title'       => 'permit_empty|max_length[255]',
        ]);
    }
}
