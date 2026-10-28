<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\FaqModel;

class Products extends BaseController
{
    public function index()
    {
        $productModel  = new ProductModel();
        $categoryModel = new CategoryModel();

        $builder = $productModel->select('products.*, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->where('products.status', 'published');

        // Filter by category: /produk?category=jersey-original
        $catSlug = $this->request->getGet('category');
        $activeCat = null;
        if ($catSlug) {
            $activeCat = $categoryModel->where('slug', $catSlug)->where('type', 'product')->first();
            if ($activeCat) {
                $builder->where('products.category_id', $activeCat['id']);
            }
        }

        $products = $builder->orderBy('products.created_at', 'DESC')->findAll();
        $categories = $categoryModel->where('type', 'product')->findAll();

        // Count per kategori via 1 query GROUP BY (anti N+1)
        $countsByCat = $productModel->countPublishedByCategory();
        foreach ($categories as &$cat) {
            $cat['count'] = $countsByCat[(int)$cat['id']] ?? 0;
        }
        unset($cat);

        $totalProducts = $productModel->countPublished();

        // Dynamic SEO title/desc for category archive pages
        $filterName = $activeCat ? $activeCat['name'] : null;
        $seoTitle = $filterName ? 'Produk ' . $filterName . ' — Katalog Pusat Jersey' : 'Katalog Custom Jersey & Produk Authentic — Pusat Jersey';
        $seoDesc  = $filterName
            ? 'Koleksi produk ' . $filterName . ' dari Pusat Jersey Indonesia — jersey custom sublimasi & original authentic dengan harga terbaik.'
            : 'Jelajahi pilihan jersey custom sublimasi berkualitas tinggi dan produk original authentic di Pusat Jersey Indonesia.';

        $seoData = [
            'meta_title' => $seoTitle,
            'meta_description' => $seoDesc,
            'breadcrumbs' => [
                ['name' => 'Beranda', 'url' => '/'],
                ['name' => 'Produk', 'url' => '/produk'],
            ],
        ];
        if ($filterName) {
            $seoData['breadcrumbs'][] = ['name' => $filterName, 'url' => current_url()];
        }

        $data = [
            'metaTitle'     => $seoTitle,
            'metaDesc'      => $seoDesc,
            'products'      => $products,
            'categories'    => $categories,
            'activeCat'     => $catSlug,
            'totalProducts' => $totalProducts,
        ];

        return $this->renderView('products/index', $data, $seoData);
    }

    public function detail($slug)
    {
        $productModel = new ProductModel();
        $product = $productModel->select('products.*, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->where('products.slug', $slug)
            ->where('products.status', 'published')
            ->first();

        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Produk tidak ditemukan');
        }

        // FAQs for this product (post_type = 'product', target_id = this product id)
        $faqModel = new FaqModel();
        $faqs = $faqModel->where('post_type', 'product')
            ->where('target_id', $product['id'])
            ->orderBy('sort_order', 'ASC')
            ->findAll();
        if (empty($faqs)) {
            // Fallback to global FAQs
            $faqs = $faqModel->where('post_type', 'global')->orderBy('sort_order', 'ASC')->findAll(3);
        }

        $prodImage = !empty($product['featured_image'])
            ? (preg_match('#^https?://#i', $product['featured_image']) ? $product['featured_image'] : base_url($product['featured_image']))
            : 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=800&q=80';

        $extraSchemas = [
            [
                '@context' => 'https://schema.org',
                '@type'    => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => base_url()],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Produk', 'item' => base_url('produk')],
                    ['@type' => 'ListItem', 'position' => 3, 'name' => $product['name'], 'item' => current_url()],
                ]
            ],
            [
                '@context' => 'https://schema.org',
                '@type'    => 'Product',
                'name'     => $product['name'],
                'image'    => $prodImage,
                'description' => strip_tags(!empty($product['description']) ? $product['description'] : $product['name']),
                'sku'      => !empty($product['sku']) ? $product['sku'] : ('PJ-' . $product['id']),
                'brand'    => ['@type' => 'Brand', 'name' => $this->settings['brand']['name'] ?? 'Pusat Jersey'],
                'offers'   => [
                    '@type'         => 'Offer',
                    'url'           => current_url(),
                    'price'         => (float)($product['discount_price'] ?? $product['price'] ?? 0),
                    'priceCurrency' => 'IDR',
                    'availability'  => 'https://schema.org/InStock',
                    'seller'        => ['@type' => 'Organization', 'name' => $this->settings['brand']['name'] ?? 'Pusat Jersey']
                ]
            ]
        ];

        $data = [
            'metaTitle'    => !empty($product['meta_title']) ? $product['meta_title'] : ($product['name'] . ' — ' . ($this->settings['brand']['name'] ?? 'Pusat Jersey')),
            'metaDesc'     => !empty($product['meta_description']) ? $product['meta_description'] : (!empty($product['description']) ? mb_substr(strip_tags($product['description']), 0, 160) : ($product['name'] . ' dari ' . ($this->settings['brand']['name'] ?? 'Pusat Jersey'))),
            'product'      => $product,
            'faqs'         => $faqs,
            'extraSchemas' => $extraSchemas,
        ];

        $seoData = ['faqs' => $faqs];

        return $this->renderView('products/detail', $data, $seoData);
    }
}
