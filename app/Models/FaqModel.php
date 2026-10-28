<?php

namespace App\Models;

use CodeIgniter\Model;

class FaqModel extends Model
{
    protected $table = 'faqs';
    protected $primaryKey = 'id';
    protected $allowedFields = ['question', 'answer', 'post_type', 'target_id', 'sort_order', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
}
