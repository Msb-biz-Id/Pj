<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PageSectionModel;

class Sections extends BaseController
{
    public function index()
    {
        $pageSectionModel = new PageSectionModel();
        $sections = $pageSectionModel->orderBy('sort_order', 'ASC')->findAll();

        $data = [
            'metaTitle' => 'Manajemen Page Builder & Komponen Homepage',
            'sections'  => $sections,
        ];

        return $this->renderAdminView('admin/sections/index', $data);
    }

    public function toggle($id)
    {
        $pageSectionModel = new PageSectionModel();
        $section = $pageSectionModel->find($id);

        if ($section) {
            $newStatus = $section['is_active'] ? 0 : 1;
            $pageSectionModel->update($id, ['is_active' => $newStatus]);
        }

        $sections = $pageSectionModel->orderBy('sort_order', 'ASC')->findAll();
        return view('admin/sections/_table', ['sections' => $sections]);
    }

    public function reorder()
    {
        $pageSectionModel = new PageSectionModel();
        $orders = $this->request->getPost('order') ?? [];

        foreach ($orders as $index => $id) {
            $pageSectionModel->update($id, ['sort_order' => $index + 1]);
        }

        $sections = $pageSectionModel->orderBy('sort_order', 'ASC')->findAll();
        return view('admin/sections/_table', ['sections' => $sections]);
    }

    public function update($id)
    {
        $pageSectionModel = new PageSectionModel();
        $section = $pageSectionModel->find($id);

        if ($section) {
            $settingsJson = $this->request->getPost('settings_json');
            $pageSectionModel->update($id, [
                'layout_style'  => $this->request->getPost('layout_style') ?: $section['layout_style'],
                'settings_json' => $settingsJson ?: $section['settings_json']
            ]);
        }

        $sections = $pageSectionModel->orderBy('sort_order', 'ASC')->findAll();
        return view('admin/sections/_table', ['sections' => $sections]);
    }
}
