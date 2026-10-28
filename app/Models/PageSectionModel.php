<?php

namespace App\Models;

use CodeIgniter\Model;

class PageSectionModel extends Model
{
    protected $table = 'page_sections';
    protected $primaryKey = 'id';
    protected $allowedFields = ['page_name', 'section_key', 'layout_style', 'sort_order', 'is_active', 'settings_json', 'created_at', 'updated_at'];
    protected $useTimestamps = true;

    public function getActiveSections(string $pageName = 'home')
    {
        return $this->where('page_name', $pageName)
                    ->where('is_active', 1)
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();
    }
}
