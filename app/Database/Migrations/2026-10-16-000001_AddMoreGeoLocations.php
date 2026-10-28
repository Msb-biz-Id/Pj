<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Menambah lokasi geo-targeting baru (fokus Jawa Timur + kota nasional)
 * beserta baris pivot location_services untuk layanan "pusat-jersey-custom",
 * sehingga 1 layanan otomatis memiliki banyak URL lokal:
 *   /layanan/pusat-jersey-custom-lamongan, -sidoarjo, -gresik, dst.
 */
class AddMoreGeoLocations extends Migration
{
    /**
     * Lokasi baru: [name, slug, province, type]
     * Urutan: Jawa Timur dulu (fokus), lalu kota nasional.
     */
    private array $newLocations = [
        ['Lamongan', 'lamongan', 'Jawa Timur', 'kota'],
        ['Sidoarjo', 'sidoarjo', 'Jawa Timur', 'kota'],
        ['Gresik', 'gresik', 'Jawa Timur', 'kota'],
        ['Kediri', 'kediri', 'Jawa Timur', 'kota'],
        ['Blitar', 'blitar', 'Jawa Timur', 'kota'],
        ['Pasuruan', 'pasuruan', 'Jawa Timur', 'kota'],
        ['Probolinggo', 'probolinggo', 'Jawa Timur', 'kota'],
        ['Jombang', 'jombang', 'Jawa Timur', 'kabupaten'],
        ['Madiun', 'madiun', 'Jawa Timur', 'kota'],
        ['Tuban', 'tuban', 'Jawa Timur', 'kabupaten'],
        ['Situbondo', 'situbondo', 'Jawa Timur', 'kabupaten'],
        ['Bangkalan', 'bangkalan', 'Jawa Timur', 'kabupaten'],
        ['Bekasi', 'bekasi', 'Jawa Barat', 'kota'],
        ['Bogor', 'bogor', 'Jawa Barat', 'kota'],
        ['Depok', 'depok', 'Jawa Barat', 'kota'],
        ['Tangerang', 'tangerang', 'Banten', 'kota'],
        ['Serang', 'serang', 'Banten', 'kota'],
        ['Solo', 'solo', 'Jawa Tengah', 'kota'],
        ['Purwokerto', 'purwokerto', 'Jawa Tengah', 'kabupaten'],
        ['Magelang', 'magelang', 'Jawa Tengah', 'kota'],
        ['Balikpapan', 'balikpapan', 'Kalimantan Timur', 'kota'],
        ['Samarinda', 'samarinda', 'Kalimantan Timur', 'kota'],
        ['Banjarmasin', 'banjarmasin', 'Kalimantan Selatan', 'kota'],
        ['Pontianak', 'pontianak', 'Kalimantan Barat', 'kota'],
        ['Batam', 'batam', 'Kepulauan Riau', 'kota'],
        ['Padang', 'padang', 'Sumatera Barat', 'kota'],
        ['Pekanbaru', 'pekanbaru', 'Riau', 'kota'],
        ['Manado', 'manado', 'Sulawesi Utara', 'kota'],
        ['Kendari', 'kendari', 'Sulawesi Tenggara', 'kota'],
        ['Ambon', 'ambon', 'Maluku', 'kota'],
    ];

    public function up()
    {
        $db     = $this->db;
        $now    = date('Y-m-d H:i:s');
        $nowIds = [];

        // 1. Insert lokasi baru (skip jika slug sudah ada — idempotent)
        foreach ($this->newLocations as [$name, $slug, $province, $type]) {
            $exists = $db->table('locations')->where('slug', $slug)->countAllResults();
            if ($exists > 0) {
                continue;
            }
            $db->table('locations')->insert([
                'name'       => $name,
                'slug'       => $slug,
                'province'   => $province,
                'type'       => $type,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $nowIds[$slug] = (int) $db->insertID();
        }

        // 2. Seed pivot location_services untuk layanan "pusat-jersey-custom"
        //    (hanya kota Jawa Timur yang baru ditambahkan — sesuai fokus SEO lokal)
        $service = $db->table('services')->where('slug', 'pusat-jersey-custom')->get()->getRowArray();
        if (!$service) {
            return; // Seeder layanan belum dijalankan — biarkan tanpa error
        }
        $serviceId = (int) $service['id'];
        $srvTitle  = $service['title'];

        foreach ($this->newLocations as [$name, $slug, $province, $type]) {
            if ($province !== 'Jawa Timur' || !isset($nowIds[$slug])) {
                continue; // hanya Jatim untuk seed otomatis; sisanya tinggal centang di admin
            }
            $db->table('location_services')->insert([
                'service_id'        => $serviceId,
                'location_id'       => $nowIds[$slug],
                'custom_title'      => $srvTitle . ' ' . $name . ' — Pesan Jersey Tim & Satuan',
                'custom_content'    => '<p>Layanan konveksi dan tempat pembuatan <strong>' . $srvTitle . ' di ' . $name . '</strong>. Kirim cepat, gratis desain, dan garansi kualitas hasil cetak sublimasi terbaik untuk wilayah ' . $name . ' dan sekitarnya.</p>',
                'meta_title'        => $srvTitle . ' ' . $name . ' Terbaik | Pusat Jersey',
                'meta_description'  => 'Cari tempat pembuatan ' . $srvTitle . ' di ' . $name . '? Dapatkan garansi warna tajam, bahan drifit berkualitas, dan promo gratis ongkir ke ' . $name . '.',
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);
        }
    }

    public function down()
    {
        $db = $this->db;

        // Hapus pivot yang menunjuk ke lokasi-lokasi baru ini
        $slugs = array_column($this->newLocations, 'slug');
        $ids   = [];
        foreach ($slugs as $slug) {
            $row = $db->table('locations')->where('slug', $slug)->get()->getRowArray();
            if ($row) {
                $ids[] = (int) $row['id'];
            }
        }
        if ($ids !== []) {
            $db->table('location_services')->whereIn('location_id', $ids)->delete();
            $db->table('locations')->whereIn('id', $ids)->delete();
        }
    }
}