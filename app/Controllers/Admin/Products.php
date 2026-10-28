<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\CategoryModel;

class Products extends BaseController
{
    public function index()
    {
        $productModel = new ProductModel();
        $products = $productModel->select('products.*, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->orderBy('products.created_at', 'DESC')
            ->findAll();

        $data = [
            'metaTitle' => 'Manajemen Produk — Admin Pusat Jersey',
            'products'  => $products,
        ];

        return $this->renderAdminView('admin/products/index', $data);
    }

    public function create()
    {
        $categoryModel = new CategoryModel();

        $data = [
            'metaTitle'  => 'Tambah Produk Baru',
            'product'    => null,
            'categories' => $categoryModel->where('type', 'product')->findAll(),
        ];

        return $this->renderAdminView('admin/products/form', $data);
    }

    private const STATUSES = ['published', 'draft', 'archived'];

    public function store()
    {
        $productModel = new ProductModel();

        $name = trim($this->request->getPost('name') ?? '');
        $slug = unique_slug(url_title($name, '-', true), 'products', 'slug');

        if (!$this->validateProduct()) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $productModel->insert([
            'category_id'      => $this->request->getPost('category_id') ?: null,
            'name'             => $name,
            'slug'             => $slug,
            'sku'              => $this->request->getPost('sku') ?: null,
            'price'            => (float)($this->request->getPost('price') ?? 0),
            'discount_price'   => $this->request->getPost('discount_price') ? (float)$this->request->getPost('discount_price') : null,
            'is_custom'        => $this->request->getPost('is_custom') ? 1 : 0,
            'is_affiliate'     => $this->request->getPost('is_affiliate') ? 1 : 0,
            'affiliate_link'   => $this->request->getPost('affiliate_link') ?: null,
            'shopee_link'      => $this->request->getPost('shopee_link') ?: null,
            'tokopedia_link'   => $this->request->getPost('tokopedia_link') ?: null,
            'tiktok_link'      => $this->request->getPost('tiktok_link') ?: null,
            'featured_image'   => $this->request->getPost('featured_image') ?: null,
            'gallery_json'     => $this->parseGallery($this->request->getPost('gallery')),
            'specs_json'       => $this->parseSpecs($this->request->getPost('specs')),
            'description'      => $this->request->getPost('description') ?: null,
            'meta_title'       => $this->request->getPost('meta_title') ?: null,
            'meta_description' => $this->request->getPost('meta_description') ?: null,
            'status'           => $this->request->getPost('status') ?: 'published',
        ]);

        return redirect()->to('/admin/products');
    }

    public function edit($id)
    {
        $productModel  = new ProductModel();
        $categoryModel = new CategoryModel();

        $product = $productModel->find($id);
        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Produk tidak ditemukan');
        }

        $data = [
            'metaTitle'  => 'Edit Produk — ' . $product['name'],
            'product'    => $product,
            'categories' => $categoryModel->where('type', 'product')->findAll(),
        ];

        return $this->renderAdminView('admin/products/form', $data);
    }

    public function update($id)
    {
        $productModel = new ProductModel();

        if (!$productModel->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Produk tidak ditemukan');
        }

        $name = trim($this->request->getPost('name') ?? '');

        if (!$this->validateProduct()) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $inputSlug = trim((string)$this->request->getPost('slug'));
        $baseSlug  = $inputSlug !== '' ? $inputSlug : $name;
        $slug      = unique_slug(url_title($baseSlug, '-', true), 'products', 'slug', (int)$id);

        $productModel->update($id, [
            'category_id'      => $this->request->getPost('category_id') ?: null,
            'name'             => $name,
            'slug'             => $slug,
            'sku'              => $this->request->getPost('sku') ?: null,
            'price'            => (float)($this->request->getPost('price') ?? 0),
            'discount_price'   => $this->request->getPost('discount_price') ? (float)$this->request->getPost('discount_price') : null,
            'is_custom'        => $this->request->getPost('is_custom') ? 1 : 0,
            'is_affiliate'     => $this->request->getPost('is_affiliate') ? 1 : 0,
            'affiliate_link'   => $this->request->getPost('affiliate_link') ?: null,
            'shopee_link'      => $this->request->getPost('shopee_link') ?: null,
            'tokopedia_link'   => $this->request->getPost('tokopedia_link') ?: null,
            'tiktok_link'      => $this->request->getPost('tiktok_link') ?: null,
            'featured_image'   => $this->request->getPost('featured_image') ?: null,
            'gallery_json'     => $this->parseGallery($this->request->getPost('gallery')),
            'specs_json'       => $this->parseSpecs($this->request->getPost('specs')),
            'description'      => $this->request->getPost('description') ?: null,
            'meta_title'       => $this->request->getPost('meta_title') ?: null,
            'meta_description' => $this->request->getPost('meta_description') ?: null,
            'status'           => $this->request->getPost('status') ?: 'published',
        ]);

        return redirect()->to('/admin/products');
    }

    /**
     * Gallery textarea (one URL per line) → JSON array string, atau null bila kosong.
     */
    private function parseGallery(?string $raw): ?string
    {
        $lines = array_filter(array_map('trim', explode("\n", (string)$raw)));
        $lines = array_values(array_filter($lines, static fn ($l) => $l !== ''));
        return $lines ? json_encode($lines) : null;
    }

    /**
     * Specs textarea ("Label = Value" per line) → JSON object string, atau null bila kosong.
     */
    private function parseSpecs(?string $raw): ?string
    {
        $out = [];
        foreach (explode("\n", (string)$raw) as $line) {
            $line = trim($line);
            if ($line === '' || strpos($line, '=') === false) continue;
            [$k, $v] = explode('=', $line, 2);
            $k = trim($k);
            $v = trim($v);
            if ($k !== '' && $v !== '') $out[$k] = $v;
        }
        return $out ? json_encode($out, JSON_UNESCAPED_UNICODE) : null;
    }

    public function delete($id)
    {
        $productModel = new ProductModel();
        $productModel->delete($id);

        return redirect()->to('/admin/products');
    }

    /**
     * Validasi input form produk (dipakai store & update).
     */
    private function validateProduct(): bool
    {
        return $this->validateData($this->request->getPost() ?? [], [
            'name'             => 'required|string|min_length[3]|max_length[255]',
            'price'            => 'required|decimal[12,2]|greater_than_equal_to[0]',
            'discount_price'   => 'permit_empty|decimal[12,2]|greater_than_equal_to[0]',
            'status'           => 'required|in_list[' . implode(',', self::STATUSES) . ']',
            'sku'              => 'permit_empty|max_length[100]',
            'featured_image'   => 'permit_empty|max_length[255]',
            'affiliate_link'   => 'permit_empty|valid_url_strict|max_length[255]',
            'shopee_link'      => 'permit_empty|valid_url_strict|max_length[255]',
            'tokopedia_link'   => 'permit_empty|valid_url_strict|max_length[255]',
            'tiktok_link'      => 'permit_empty|valid_url_strict|max_length[255]',
            'meta_title'       => 'permit_empty|max_length[255]',
        ]);
    }
}
