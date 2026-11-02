<?php

namespace App\Models;

use CodeIgniter\Model;

class CtaLogModel extends Model
{
    protected $table = 'cta_logs';
    protected $primaryKey = 'id';
    protected $allowedFields = ['channel', 'product_slug', 'source_page', 'utm_source', 'user_agent', 'ip_address', 'created_at'];
    protected $useTimestamps = false;

    /** Total klik CTA sejak tanggal tertentu. */
    public function countSince(string $since): int
    {
        return (int) $this->builder()->where('created_at >=', $since)->countAllResults();
    }

    /** Jumlah klik per channel (whatsapp/shopee/tokopedia/tiktok/affiliate). */
    public function byChannel(string $since): array
    {
        return $this->builder()
            ->select('channel, COUNT(*) AS total')
            ->where('created_at >=', $since)
            ->groupBy('channel')
            ->orderBy('total', 'DESC')
            ->get()->getResultArray();
    }

    /** Sumber klik teratas (utm_source / halaman asal). */
    public function topSources(string $since, int $limit = 10): array
    {
        return $this->builder()
            ->select('utm_source, COUNT(*) AS total')
            ->where('created_at >=', $since)
            ->where('utm_source IS NOT NULL')
            ->where('utm_source !=', '')
            ->groupBy('utm_source')
            ->orderBy('total', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }

    /** Produk paling sering diklik CTA-nya. */
    public function topProducts(string $since, int $limit = 10): array
    {
        return $this->builder()
            ->select('product_slug, COUNT(*) AS total')
            ->where('created_at >=', $since)
            ->where('product_slug IS NOT NULL')
            ->groupBy('product_slug')
            ->orderBy('total', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }

    /** Klik terbaru untuk tabel riwayat. */
    public function recent(int $limit = 20): array
    {
        return $this->builder()
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }

    /** Klik per hari: [Y-m-d => jumlah]. */
    public function dailySince(string $since): array
    {
        $rows = $this->builder()
            ->select('substr(created_at, 1, 10) AS day, COUNT(*) AS total')
            ->where('created_at >=', $since)
            ->groupBy('day')
            ->orderBy('day', 'ASC')
            ->get()->getResultArray();

        $out = [];
        foreach ($rows as $row) {
            $out[$row['day']] = (int) $row['total'];
        }
        return $out;
    }
}
