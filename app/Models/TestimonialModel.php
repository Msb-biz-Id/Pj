<?php

namespace App\Models;

use CodeIgniter\Model;

class TestimonialModel extends Model
{
    protected $table = 'testimonials';
    protected $primaryKey = 'id';
    protected $allowedFields = ['client_name', 'team_name', 'city', 'avatar', 'comment', 'rating', 'status', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
}
