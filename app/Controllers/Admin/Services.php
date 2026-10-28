<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ServiceModel;
use App\Models\LocationModel;
use App\Models\LocationServiceModel;

class Services extends BaseController
{
    public function index()
    {
        $serviceModel = new ServiceModel();
        $services = $serviceModel->orderBy('created_at', 'DESC')->findAll();

        // Jumlah lokasi terafiliasi per layanan (indikator multi-URL SEO lokal)
        $db = \Config\Database::connect();
        $locCounts = $db->table('location_services')
            ->select('service_id, COUNT(*) as total', false)
            ->groupBy('service_id')
            ->get()->getResultArray();
        $locationCountMap = [];
        foreach ($locCounts as $lc) {
            $locationCountMap[(int) $lc['service_id']] = (int) $lc['total'];
        }

        $data = [
            'metaTitle' => 'Manajemen Layanan Konveksi — Admin Pusat Jersey',
            'services'  => $services,
            'locationCountMap' => $locationCountMap,
        ];

        return $this->renderAdminView('admin/services/index', $data);
    }

    public function create()
    {
        $data = [
            'metaTitle' => 'Tambah Layanan Baru',
            'service' => null,
            'allLocations' => (new LocationModel())->orderBy('province', 'ASC')->orderBy('name', 'ASC')->findAll(),
            'attachedLocationIds' => [],
        ];
        return $this->renderAdminView('admin/services/form', $data);
    }

    private const STATUSES = ['published', 'draft', 'archived'];

    public function store()
    {
        $serviceModel = new ServiceModel();
        $title = trim($this->request->getPost('title') ?? '');
        $slug  = unique_slug(url_title($title, '-', true), 'services', 'slug');

        if (!$this->validateService()) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $insertId = $serviceModel->insert([
            'title'            => $title,
            'slug'             => $slug,
            'icon'             => $this->request->getPost('icon') ?: 'shirt',
            'short_desc'       => $this->request->getPost('short_desc') ?: null,
            'content'          => $this->request->getPost('content') ?: '',
            'featured_image'   => $this->request->getPost('featured_image') ?: null,
            'starting_price'   => (float)($this->request->getPost('starting_price') ?? 0),
            'status'           => $this->request->getPost('status') ?: 'published',
            'meta_title'       => $this->request->getPost('meta_title') ?: null,
            'meta_description' => $this->request->getPost('meta_description') ?: null,
        ]);

        // Sinkronisasi multi-lokasi (SEO lokal): 1 layanan → banyak URL kota
        $this->syncLocations((int) $insertId);

        return redirect()->to('/admin/services');
    }

    public function edit($id)
    {
        $serviceModel = new ServiceModel();
        $service = $serviceModel->find($id);

        if (!$service) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Layanan tidak ditemukan');
        }

        $data = [
            'metaTitle' => 'Edit Layanan — ' . $service['title'],
            'service'   => $service,
            'allLocations' => (new LocationModel())->orderBy('province', 'ASC')->orderBy('name', 'ASC')->findAll(),
            'attachedLocationIds' => $this->getAttachedLocationIds((int) $id),
        ];

        return $this->renderAdminView('admin/services/form', $data);
    }

    public function update($id)
    {
        $serviceModel = new ServiceModel();

        if (!$serviceModel->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Layanan tidak ditemukan');
        }

        $title = trim($this->request->getPost('title') ?? '');

        if (!$this->validateService()) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $inputSlug = trim((string)$this->request->getPost('slug'));
        $baseSlug  = $inputSlug !== '' ? $inputSlug : $title;
        $slug      = unique_slug(url_title($baseSlug, '-', true), 'services', 'slug', (int)$id);

        $serviceModel->update($id, [
            'title'            => $title,
            'slug'             => $slug,
            'icon'             => $this->request->getPost('icon') ?: 'shirt',
            'short_desc'       => $this->request->getPost('short_desc') ?: null,
            'content'          => $this->request->getPost('content') ?: '',
            'featured_image'   => $this->request->getPost('featured_image') ?: null,
            'starting_price'   => (float)($this->request->getPost('starting_price') ?? 0),
            'status'           => $this->request->getPost('status') ?: 'published',
            'meta_title'       => $this->request->getPost('meta_title') ?: null,
            'meta_description' => $this->request->getPost('meta_description') ?: null,
        ]);

        // Sinkronisasi multi-lokasi (SEO lokal): 1 layanan → banyak URL kota
        $this->syncLocations((int) $id);

        return redirect()->to('/admin/services');
    }

    public function delete($id)
    {
        $serviceModel = new ServiceModel();
        $serviceModel->delete($id);

        // Hapus juga seluruh halaman geo (pivot) milik layanan ini
        $db = \Config\Database::connect();
        $db->table('location_services')->where('service_id', $id)->delete();

        return redirect()->to('/admin/services');
    }

    // ================================================================
    // MANAJEMEN LOKASI (SEO LOKAL) — 1 layanan → banyak URL per kota
    // ================================================================

    /**
     * GET /admin/services/locations/{serviceId}
     * Halaman kelola afiliasi lokasi: daftar URL geo aktif + form SEO per kota
     * + attach lokasi baru.
     */
    public function locations($serviceId)
    {
        $serviceModel = new ServiceModel();
        $service = $serviceModel->find($serviceId);

        if (!$service) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Layanan tidak ditemukan');
        }

        $locationServiceModel = new LocationServiceModel();
        $attached = $locationServiceModel
            ->select('location_services.*, locations.name AS loc_name, locations.slug AS loc_slug, locations.province AS loc_province, locations.type AS loc_type')
            ->join('locations', 'locations.id = location_services.location_id')
            ->where('location_services.service_id', $serviceId)
            ->orderBy('locations.province', 'ASC')
            ->orderBy('locations.name', 'ASC')
            ->findAll();

        $attachedIds = array_map(static fn ($row) => (int) $row['location_id'], $attached);
        $available = (new LocationModel())
            ->whereNotIn('id', $attachedIds === [] ? [0] : $attachedIds)
            ->orderBy('province', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();

        $data = [
            'metaTitle'  => 'Lokasi SEO — ' . $service['title'],
            'service'    => $service,
            'attached'   => $attached,
            'available'  => $available,
        ];

        return $this->renderAdminView('admin/services/locations', $data);
    }

    /**
     * POST /admin/services/locations/{serviceId}/attach
     * Afiliasikan satu lokasi yang dipilih ke layanan (belum terpakai).
     */
    public function attachLocation($serviceId)
    {
        $serviceModel = new ServiceModel();
        $service = $serviceModel->find($serviceId);
        if (!$service) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Layanan tidak ditemukan');
        }

        $locationId = (int) $this->request->getPost('location_id');
        $location = (new LocationModel())->find($locationId);

        if (!$location) {
            return redirect()->back()->with('error', 'Lokasi tidak ditemukan.');
        }

        $locationServiceModel = new LocationServiceModel();
        $exists = $locationServiceModel
            ->where('service_id', $serviceId)
            ->where('location_id', $locationId)
            ->countAllResults();

        if ($exists > 0) {
            return redirect()->back()->with('error', 'Lokasi "' . $location['name'] . '" sudah terafiliasi.');
        }

        $locationServiceModel->insert([
            'service_id'  => (int) $serviceId,
            'location_id' => $locationId,
        ]);

        return redirect()->back()->with('success', 'Lokasi "' . $location['name'] . '" ditambahkan. URL baru: /layanan/' . $service['slug'] . '-' . $location['slug']);
    }

    /**
     * POST /admin/services/locations/{serviceId}/detach/{locationId}
     * Lepas afiliasi lokasi (halaman geo-nya ikut nonaktif & hilang dari sitemap).
     */
    public function detachLocation($serviceId, $locationId)
    {
        $locationServiceModel = new LocationServiceModel();
        $locationServiceModel
            ->where('service_id', $serviceId)
            ->where('location_id', $locationId)
            ->delete();

        return redirect()->back()->with('success', 'Lokasi dilepas dari layanan ini.');
    }

    /**
     * POST /admin/services/locations/{serviceId}/update/{locationId}
     * Simpan kustomisasi SEO untuk pasangan layanan × lokasi
     * (custom title, konten, meta title, meta description).
     */
    public function updateLocationSeo($serviceId, $locationId)
    {
        $locationServiceModel = new LocationServiceModel();
        $pivot = $locationServiceModel
            ->where('service_id', $serviceId)
            ->where('location_id', $locationId)
            ->first();

        if (!$pivot) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Afiliasi lokasi tidak ditemukan');
        }

        $this->validateData($this->request->getPost() ?? [], [
            'custom_title'      => 'permit_empty|max_length[255]',
            'meta_title'        => 'permit_empty|max_length[255]',
            'meta_description'  => 'permit_empty|max_length[160]',
        ]);

        $locationServiceModel->update($pivot['id'], [
            'custom_title'     => trim((string) $this->request->getPost('custom_title')) ?: null,
            'custom_content'   => trim((string) $this->request->getPost('custom_content')) ?: null,
            'meta_title'       => trim((string) $this->request->getPost('meta_title')) ?: null,
            'meta_description' => trim((string) $this->request->getPost('meta_description')) ?: null,
        ]);

        return redirect()->back()->with('success', 'SEO per lokasi berhasil disimpan.');
    }

    /**
     * Sinkronisasi checkbox "location_ids[]" dari form layanan → tabel pivot.
     * Lokasi yang dicentang dipasang, yang tidak dicentang dilepas.
     */
    private function syncLocations(int $serviceId): void
    {
        $locationServiceModel = new LocationServiceModel();
        $locationModel        = new LocationModel();

        $checkedIds  = array_filter(array_map('intval', (array) ($this->request->getPost('location_ids') ?? [])));
        $existingIds = array_map(
            static fn ($row) => (int) $row['location_id'],
            $locationServiceModel->where('service_id', $serviceId)->findAll()
        );

        // Attach yang baru dicentang
        foreach (array_diff($checkedIds, $existingIds) as $locId) {
            if ($locationModel->find($locId)) {
                $locationServiceModel->insert([
                    'service_id'  => $serviceId,
                    'location_id' => $locId,
                ]);
            }
        }

        // Detach yang tidak lagi dicentang
        foreach (array_diff($existingIds, $checkedIds) as $locId) {
            $locationServiceModel
                ->where('service_id', $serviceId)
                ->where('location_id', $locId)
                ->delete();
        }
    }

    /**
     * ID lokasi yang terafiliasi dengan layanan.
     */
    private function getAttachedLocationIds(int $serviceId): array
    {
        $rows = (new LocationServiceModel())->where('service_id', $serviceId)->findAll();

        return array_map(static fn ($row) => (int) $row['location_id'], $rows);
    }

    /**
     * Validasi input form layanan (dipakai store & update).
     */
    private function validateService(): bool
    {
        return $this->validateData($this->request->getPost() ?? [], [
            'title'            => 'required|string|min_length[3]|max_length[255]',
            'status'           => 'required|in_list[' . implode(',', self::STATUSES) . ']',
            'icon'             => 'permit_empty|max_length[50]',
            'starting_price'   => 'permit_empty|decimal[12,2]|greater_than_equal_to[0]',
            'featured_image'   => 'permit_empty|max_length[255]',
            'meta_title'       => 'permit_empty|max_length[255]',
        ]);
    }
}
