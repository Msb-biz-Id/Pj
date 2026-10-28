<?php

namespace App\Models;

use CodeIgniter\Model;

class LocationModel extends Model
{
    protected $table = 'locations';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'slug', 'province', 'type', 'latitude', 'longitude', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
}
