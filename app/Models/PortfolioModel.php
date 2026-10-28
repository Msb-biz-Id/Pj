<?php

namespace App\Models;

use CodeIgniter\Model;

class PortfolioModel extends Model
{
    protected $table = 'portfolios';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'category_id', 'client_name', 'title', 'slug', 'featured_image',
        'mockup_before_image', 'real_after_image', 'gallery_json',
        'description', 'status', 'created_at', 'updated_at'
    ];
    protected $useTimestamps = true;

    /**
     * Jumlah portfolio published per kategori — 1 query GROUP BY (anti N+1).
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
     * Total portofolio published — 1 query.
     */
    public function countPublished(): int
    {
        return (int)$this->builder()->where('status', 'published')->countAllResults();
    }
}
