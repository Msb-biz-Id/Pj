<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\ServiceModel;
use App\Models\PostModel;
use App\Models\PortfolioModel;
use App\Models\LocationModel;

class Search extends BaseController
{
    public function suggest()
    {
        $q = trim($this->request->getGet('q') ?? '');

        if (mb_strlen($q) < 2) {
            return '<div class="py-8 text-center text-sm text-slate-400">
                <p>Ketik minimal 2 karakter untuk mencari...</p>
            </div>';
        }

        $productModel  = new ProductModel();
        $serviceModel  = new ServiceModel();
        $postModel     = new PostModel();
        $portfolioModel = new PortfolioModel();
        $locationModel = new LocationModel();

        $products   = $productModel->like('name', $q)->where('status', 'published')->findAll(5);
        $services   = $serviceModel->like('title', $q)->where('status', 'published')->findAll(5);
        $posts      = $postModel->like('title', $q)->where('status', 'published')->findAll(5);
        $portfolios = $portfolioModel->like('title', $q)->where('status', 'published')->findAll(5);
        $locations  = $locationModel->like('name', $q)->findAll(6);

        $isEmpty = empty($products) && empty($services) && empty($posts) && empty($portfolios) && empty($locations);

        $data = [
            'q'          => $q,
            'products'   => $products,
            'services'   => $services,
            'posts'      => $posts,
            'portfolios' => $portfolios,
            'locations'  => $locations,
            'isEmpty'    => $isEmpty
        ];

        return view('partials/search_results', $data);
    }
}
