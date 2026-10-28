<?php

namespace App\Controllers;

use App\Models\PortfolioModel;
use App\Models\CategoryModel;
use App\Models\FaqModel;

class Portfolio extends BaseController
{
    public function index()
    {
        $portfolioModel = new PortfolioModel();
        $categoryModel = new CategoryModel();

        $builder = $portfolioModel->select('portfolios.*, categories.name as category_name')
            ->join('categories', 'categories.id = portfolios.category_id', 'left')
            ->where('portfolios.status', 'published')
            ->orderBy('portfolios.created_at', 'DESC');

        // Filter by category: /portofolio?category=portofolio-tim
        $catSlug = $this->request->getGet('category');
        $activeCat = null;
        if ($catSlug) {
            $activeCat = $categoryModel->where('slug', $catSlug)->where('type', 'portfolio')->first();
            if ($activeCat) {
                $builder->where('portfolios.category_id', $activeCat['id']);
            }
        }

        $portfolios = $builder->findAll();
        $categories = $categoryModel->where('type', 'portfolio')->findAll();

        // Count per kategori via 1 query GROUP BY (anti N+1)
        $countsByCat = $portfolioModel->countPublishedByCategory();
        foreach ($categories as &$cat) {
            $cat['count'] = $countsByCat[(int)$cat['id']] ?? 0;
        }
        unset($cat);

        $totalProjects = $portfolioModel->countPublished();

        // Dynamic SEO title/desc for category archive pages
        $filterName = $activeCat ? $activeCat['name'] : null;
        $seoTitle = $filterName ? 'Galeri ' . $filterName . ' — Hasil Cetak Pusat Jersey' : 'Galeri Portofolio & Bukti Hasil Cetak — Pusat Jersey';
        $seoDesc  = $filterName
            ? 'Dokumentasi hasil cetak ' . $filterName . ' dari Pusat Jersey Indonesia — bukti kualitas jersey tim di seluruh nusantara.'
            : 'Dokumentasi hasil jersey buatan Pusat Jersey Indonesia yang telah dipesan oleh ribuan tim di seluruh Indonesia.';

        $seoData = [
            'meta_title' => $seoTitle,
            'meta_description' => $seoDesc,
            'breadcrumbs' => [
                ['name' => 'Beranda', 'url' => '/'],
                ['name' => 'Portofolio', 'url' => '/portofolio'],
            ],
        ];
        if ($filterName) {
            $seoData['breadcrumbs'][] = ['name' => $filterName, 'url' => current_url()];
        }

        $data = [
            'metaTitle'   => $seoTitle,
            'metaDesc'    => $seoDesc,
            'portfolios'  => $portfolios,
            'categories'  => $categories,
            'activeCat'   => $catSlug,
            'totalProjects' => $totalProjects,
        ];

        return $this->renderView('portfolio/index', $data, $seoData);
    }

    public function detail($slug)
    {
        $portfolioModel = new PortfolioModel();
        $faqModel = new FaqModel();
        $portfolio = $portfolioModel->where('slug', $slug)->first();

        if (!$portfolio) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Portofolio tidak ditemukan');
        }

        // Related portfolios (same category, exclude current)
        $related = $portfolioModel->where('category_id', $portfolio['category_id'])
            ->where('id !=', $portfolio['id'])
            ->where('status', 'published')
            ->orderBy('created_at', 'DESC')
            ->findAll(3);

        // FAQs for this portfolio (post_type = 'portfolio', target_id = this portfolio id)
        $faqs = $faqModel->where('post_type', 'portfolio')
            ->where('target_id', $portfolio['id'])
            ->orderBy('sort_order', 'ASC')
            ->findAll();
        if (empty($faqs)) {
            // Fallback to global FAQs
            $faqs = $faqModel->where('post_type', 'global')->orderBy('sort_order', 'ASC')->findAll(3);
        }

        $seoData = [
            'meta_title' => $portfolio['title'] . ' — Portofolio Pusat Jersey',
            'meta_description' => mb_substr(strip_tags($portfolio['description'] ?? ''), 0, 160),
            'breadcrumbs' => [
                ['name' => 'Beranda', 'url' => '/'],
                ['name' => 'Portofolio', 'url' => '/portofolio'],
                ['name' => $portfolio['title'], 'url' => current_url()],
            ],
        ];

        $data = [
            'metaTitle' => $portfolio['title'] . ' — Portofolio Pusat Jersey',
            'metaDesc'  => mb_substr(strip_tags($portfolio['description'] ?? ''), 0, 160),
            'portfolio' => $portfolio,
            'related'   => $related,
            'faqs'      => $faqs,
        ];

        return $this->renderView('portfolio/detail', $data, $seoData);
    }
}
