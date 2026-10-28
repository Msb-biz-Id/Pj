<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'category_id', 'name', 'slug', 'sku', 'price', 'discount_price',
        'is_custom', 'is_affiliate', 'affiliate_link', 'shopee_link',
        'tokopedia_link', 'tiktok_link', 'featured_image', 'gallery_json',
        'description', 'specs_json', 'meta_title', 'meta_description',
        'status', 'created_at', 'updated_at'
    ];
    protected $useTimestamps = true;

    public function getPublishedWithCategory($limit = null)
    {
        $builder = $this->select('products.*, categories.name as category_name')
                        ->join('categories', 'categories.id = products.category_id', 'left')
                        ->where('products.status', 'published')
                        ->orderBy('products.created_at', 'DESC');
        if ($limit) {
            $builder->limit($limit);
        }
        return $builder->findAll();
    }

    /**
     * Jumlah produk published per kategori — 1 query GROUP BY (anti N+1).
     * Return: [category_id => count]
     */
    public function countPublishedByCategory(): array
    {
        $rows = $this->builder()
            ->select('category_id, COUNT(*) AS c')
            ->where('status', 'published')
            ->where('category_id IS NOT NULL')
            ->groupBy('category_id')
            ->get()
            ->getResultArray();

        $counts = [];
        foreach ($rows as $row) {
            $counts[(int)$row['category_id']] = (int)$row['c'];
        }
        return $counts;
    }

    /**
     * Total produk published — 1 query.
     */
    public function countPublished(): int
    {
        return (int)$this->builder()->where('status', 'published')->countAllResults();
    }
}
