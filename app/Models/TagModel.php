<?php

namespace App\Models;

use CodeIgniter\Model;

class TagModel extends Model
{
    protected $table = 'tags';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'slug', 'created_at', 'updated_at'];
    protected $useTimestamps = true;

    public function getTagsForPost(int $postId): array
    {
        return $this->select('tags.*')
            ->join('post_tags', 'post_tags.tag_id = tags.id')
            ->where('post_tags.post_id', $postId)
            ->orderBy('tags.name', 'ASC')
            ->findAll();
    }
}
