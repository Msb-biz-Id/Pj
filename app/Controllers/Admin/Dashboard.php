<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\ServiceModel;
use App\Models\PostModel;
use App\Models\CommentModel;
use App\Models\PageSectionModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $productModel     = new ProductModel();
        $serviceModel     = new ServiceModel();
        $postModel        = new PostModel();
        $commentModel     = new CommentModel();
        $pageSectionModel = new PageSectionModel();

        $stats = [
            'products_count' => $productModel->countAllResults(),
            'services_count' => $serviceModel->countAllResults(),
            'posts_count'    => $postModel->countAllResults(),
            'comments_count' => $commentModel->where('status', 'pending')->countAllResults(),
            'sections_count' => $pageSectionModel->where('is_active', 1)->countAllResults(),
        ];

        $recentComments = $commentModel->select('comments.*, posts.title as post_title')
            ->join('posts', 'posts.id = comments.post_id', 'left')
            ->orderBy('comments.created_at', 'DESC')
            ->findAll(5);

        $sections = $pageSectionModel->orderBy('sort_order', 'ASC')->findAll();

        $data = [
            'metaTitle'      => 'Dashboard Admin — Pusat Jersey SPA',
            'stats'          => $stats,
            'recentComments' => $recentComments,
            'sections'       => $sections,
        ];

        return $this->renderAdminView('admin/dashboard/index', $data);
    }
}
