<?php

namespace App\Models;

use CodeIgniter\Model;

class PostModel extends Model
{
    protected $table = 'posts';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'category_id', 'title', 'slug', 'excerpt', 'content',
        'featured_image', 'meta_title', 'meta_description',
        'status', 'views_count', 'created_at', 'updated_at'
    ];
    protected $useTimestamps = true;

    public function getPublishedWithCategory($limit = null)
    {
        $builder = $this->select('posts.*, categories.name as category_name, categories.slug as category_slug')
                        ->join('categories', 'categories.id = posts.category_id', 'left')
                        ->where('posts.status', 'published')
                        ->orderBy('posts.created_at', 'DESC');
        if ($limit) {
            $builder->limit($limit);
        }
        return $builder->findAll();
    }

    /**
     * Jumlah post published per kategori — 1 query GROUP BY (anti N+1).
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
     * Total post published — 1 query.
     */
    public function countPublished(): int
    {
        return (int)$this->builder()->where('status', 'published')->countAllResults();
    }
}
