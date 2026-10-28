<?php

namespace App\Controllers;

use App\Models\PageSectionModel;
use App\Models\ServiceModel;
use App\Models\ProductModel;
use App\Models\PortfolioModel;
use App\Models\PostModel;
use App\Models\TestimonialModel;
use App\Models\FaqModel;
use App\Models\LocationModel;

class Home extends BaseController
{
    public function index(): string
    {
        $sectionModel = new PageSectionModel();
        $serviceModel = new ServiceModel();
        $productModel = new ProductModel();
        $portfolioModel = new PortfolioModel();
        $postModel = new PostModel();
        $testimonialModel = new TestimonialModel();
        $faqModel = new FaqModel();
        $locationModel = new LocationModel();

        $sections = $sectionModel->getActiveSections('home');
        $activeKeys = array_column($sections, 'section_key');

        $services     = in_array('services', $activeKeys, true) ? $serviceModel->where('status', 'published')->findAll(6) : [];
        $products     = in_array('products', $activeKeys, true) ? $productModel->getPublishedWithCategory(6) : [];
        $portfolios   = in_array('portfolio_gallery', $activeKeys, true) ? $portfolioModel->where('status', 'published')->findAll(6) : [];
        $posts        = in_array('blog_latest', $activeKeys, true) ? $postModel->getPublishedWithCategory(3) : [];
        $testimonials = in_array('testimonials', $activeKeys, true) ? $testimonialModel->where('status', 'approved')->findAll(6) : [];
        $locations    = in_array('locations_grid', $activeKeys, true) ? $locationModel->orderBy('name', 'ASC')->findAll(12) : [];
        // FAQs selalu dibutuhkan untuk JSON-LD Schema FAQPage di homepage
        $faqs         = $faqModel->where('post_type', 'global')->orderBy('sort_order', 'ASC')->findAll();

        $data = [
            'sections'     => $sections,
            'services'     => $services,
            'products'     => $products,
            'portfolios'   => $portfolios,
            'posts'        => $posts,
            'testimonials' => $testimonials,
            'faqs'         => $faqs,
            'locations'    => $locations,
        ];

        $seo = [
            'meta_title' => $this->settings['brand']['name'] . ' — ' . $this->settings['brand']['tagline'],
            'meta_description' => $this->settings['about']['short_desc'],
            'faqs' => $faqs
        ];

        return $this->renderView('home/index', $data, $seo);
    }
}
