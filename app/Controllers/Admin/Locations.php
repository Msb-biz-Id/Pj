<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LocationModel;

class Locations extends BaseController
{
    private const TYPES = ['kota', 'kabupaten'];

    /**
     * Daftar semua lokasi (kota/kabupaten) + jumlah layanan yang terafiliasi.
     */
    public function index()
    {
        $locationModel = new LocationModel();
        $locations = $locationModel->orderBy('province', 'ASC')->orderBy('name', 'ASC')->findAll();

        // Hitung jumlah layanan per lokasi (untuk indikator "terpakai")
        $db = \Config\Database::connect();
        $counts = $db->table('location_services')
            ->select('location_id, COUNT(*) as total', false)
            ->groupBy('location_id')
            ->get()->getResultArray();
        $usageMap = [];
        foreach ($counts as $c) {
            $usageMap[(int) $c['location_id']] = (int) $c['total'];
        }

        $data = [
            'metaTitle' => 'Manajemen Lokasi (SEO Lokal) — Admin Pusat Jersey',
            'locations' => $locations,
            'usageMap'  => $usageMap,
        ];

        return $this->renderAdminView('admin/locations/index', $data);
    }

    /**
     * Tambah lokasi baru.
     */
    public function store()
    {
        $name = trim((string) $this->request->getPost('name'));
        $type = $this->request->getPost('type');

        if ($name === '' || !in_array($type, self::TYPES, true)) {
            return redirect()->back()->with('error', 'Nama lokasi wajib diisi dan tipe harus kota/kabupaten.');
        }

        $locationModel = new LocationModel();

        $slug = url_title($name, '-', true);
        if ($locationModel->where('slug', $slug)->countAllResults() > 0) {
            return redirect()->back()->with('error', 'Lokasi "' . $name . '" sudah terdaftar.');
        }

        $locationModel->insert([
            'name'     => $name,
            'slug'     => $slug,
            'province' => trim((string) $this->request->getPost('province')) ?: null,
            'type'     => $type,
        ]);

        return redirect()->back()->with('success', 'Lokasi "' . $name . '" berhasil ditambahkan.');
    }

    /**
     * Update lokasi (nama, slug, provinsi, tipe).
     */
    public function update($id)
    {
        $locationModel = new LocationModel();
        $location = $locationModel->find($id);

        if (!$location) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Lokasi tidak ditemukan');
        }

        $name = trim((string) $this->request->getPost('name'));
        $type = $this->request->getPost('type');

        if ($name === '' || !in_array($type, self::TYPES, true)) {
            return redirect()->back()->with('error', 'Nama lokasi wajib diisi dan tipe harus kota/kabupaten.');
        }

        $inputSlug = trim((string) $this->request->getPost('slug'));
        $baseSlug  = $inputSlug !== '' ? $inputSlug : $name;
        $slug      = unique_slug(url_title($baseSlug, '-', true), 'locations', 'slug', (int) $id);

        $locationModel->update($id, [
            'name'     => $name,
            'slug'     => $slug,
            'province' => trim((string) $this->request->getPost('province')) ?: null,
            'type'     => $type,
        ]);

        return redirect()->back()->with('success', 'Perubahan lokasi "' . $name . '" disimpan.');
    }

    /**
     * Hapus lokasi + semua baris pivot (halaman geo otomatis ikut hilang dari sitemap).
     */
    public function delete($id)
    {
        $db = \Config\Database::connect();
        $db->table('location_services')->where('location_id', $id)->delete();

        $locationModel = new LocationModel();
        $locationModel->delete($id);

        return redirect()->back()->with('success', 'Lokasi dan seluruh halaman geo-nya berhasil dihapus.');
    }
}