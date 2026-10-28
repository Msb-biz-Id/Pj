<?php

namespace App\Models;

use CodeIgniter\Model;

class CommentModel extends Model
{
    protected $table = 'comments';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'post_id', 'parent_id', 'author_name', 'author_email',
        'author_website', 'content', 'status', 'ip_address',
        'user_agent', 'created_at', 'updated_at'
    ];
    protected $useTimestamps = true;

    public function getApprovedForPost(int $postId)
    {
        $all = $this->where('post_id', $postId)
                    ->where('status', 'approved')
                    ->orderBy('created_at', 'ASC')
                    ->findAll();

        $byParent = [];
        foreach ($all as $c) {
            $parentId = $c['parent_id'] ?? 0;
            if (!isset($byParent[$parentId])) {
                $byParent[$parentId] = [];
            }
            $byParent[$parentId][] = $c;
        }

        return $byParent;
    }
}
