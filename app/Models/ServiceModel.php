<?php

namespace App\Models;

use CodeIgniter\Model;

class ServiceModel extends Model
{
    protected $table = 'services';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'title', 'slug', 'short_desc', 'content', 'icon',
        'featured_image', 'starting_price', 'meta_title',
        'meta_description', 'status', 'created_at', 'updated_at'
    ];
    protected $useTimestamps = true;
}
