<?php

namespace App\Models;

use CodeIgniter\Model;

class LocationServiceModel extends Model
{
    protected $table = 'location_services';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'service_id', 'location_id', 'custom_title', 'custom_content',
        'meta_title', 'meta_description', 'created_at', 'updated_at'
    ];
    protected $useTimestamps = true;

    public function getResolved(string $serviceSlug, string $locationSlug)
    {
        $serviceModel = new ServiceModel();
        $locationModel = new LocationModel();

        $service = $serviceModel->where('slug', $serviceSlug)->first();
        $location = $locationModel->where('slug', $locationSlug)->first();

        if (!$service || !$location) {
            return null;
        }

        $pivot = $this->where('service_id', $service['id'])
                      ->where('location_id', $location['id'])
                      ->first();

        $locName = $location['name'];

        // Intro lokal: pakai custom_content admin bila ada, selain itu intro default
        // yang menyisipkan kata kunci kota (jasa ... {kota}).
        $localIntro = trim((string) ($pivot['custom_content'] ?? ''));
        if ($localIntro === '') {
            $localIntro = "<p>Tempat pembuatan <strong>{$service['title']} di {$locName}</strong> dan sekitarnya. "
                . "Melayani pesan jersey tim &amp; satuan dengan garansi warna tajam, gratis desain, dan pengiriman ke seluruh {$locName}.</p>";
        }

        // Konten dasar layanan selalu disertakan di bawah intro lokal agar halaman
        // geo tetap kaya (bukan doorway page tipis) dan tidak duplikat antar kota.
        $resolvedContent = $localIntro . (string) $service['content'];

        // Deskripsi singkat (lede): potongan awal intro lokal tanpa tag HTML.
        $shortDesc = trim(preg_replace('/\s+/', ' ', strip_tags($localIntro)));

        return [
            'service' => $service,
            'location' => $location,
            'pivot' => $pivot,
            'resolved_title' => $pivot['custom_title'] ?? ($service['title'] . ' ' . $locName),
            'resolved_content' => $resolvedContent,
            'resolved_short_desc' => $shortDesc,
            'resolved_meta_title' => $pivot['meta_title'] ?? ($service['title'] . ' ' . $locName . ' | Pusat Jersey'),
            'resolved_meta_description' => $pivot['meta_description'] ?? ("Cari vendor {$service['title']} di {$locName}? Pesan custom jersey sublimasi terbaik garansi retur 100%. Melayani pengiriman ke seluruh {$locName}."),
        ];
    }

    /**
     * Semua lokasi yang terafiliasi dengan sebuah layanan (untuk link internal
     * antar halaman geo & daftar "kota lain").
     */
    public function getLocationsForService(int $serviceId): array
    {
        return $this->select('locations.name AS name, locations.slug AS slug, locations.province AS province')
            ->join('locations', 'locations.id = location_services.location_id')
            ->where('location_services.service_id', $serviceId)
            ->orderBy('locations.name', 'ASC')
            ->findAll();
    }
}
