<?php

namespace App\Models;

use CodeIgniter\Model;

class CtaLogModel extends Model
{
    protected $table = 'cta_logs';
    protected $primaryKey = 'id';
    protected $allowedFields = ['channel', 'product_slug', 'source_page', 'utm_source', 'user_agent', 'ip_address', 'created_at'];
    protected $useTimestamps = false;
}
