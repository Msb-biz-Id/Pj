<?php

namespace App\Models;

use CodeIgniter\Model;

class PageViewModel extends Model
{
    protected $table = 'page_views';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'path', 'source', 'utm_source', 'utm_medium', 'utm_campaign',
        'sid', 'device', 'referer', 'is_ajax', 'created_at',
    ];
    protected $useTimestamps = false;

    /** Total kunjungan (tanpa bot, tanpa AJAX) sejak tanggal tertentu. */
    public function countSince(string $since): int
    {
        return (int) $this->builder()
            ->where('created_at >=', $since)
            ->where('device !=', 'bot')
            ->where('is_ajax', 0)
            ->countAllResults();
    }

    /** Pengunjung unik (SID berbeda) sejak tanggal tertentu. */
    public function uniqueVisitorsSince(string $since): int
    {
        return (int) $this->builder()
            ->select('sid')
            ->where('created_at >=', $since)
            ->where('device !=', 'bot')
            ->where('is_ajax', 0)
            ->where('sid IS NOT NULL')
            ->groupBy('sid')
            ->countAllResults();
    }

    /** Halaman teratas: [path => jumlah]. */
    public function topPages(string $since, int $limit = 10): array
    {
        $rows = $this->builder()
            ->select('path, COUNT(*) AS total')
            ->where('created_at >=', $since)
            ->where('device !=', 'bot')
            ->where('is_ajax', 0)
            ->groupBy('path')
            ->orderBy('total', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();

        $out = [];
        foreach ($rows as $row) {
            $out[] = ['path' => $row['path'], 'total' => (int) $row['total']];
        }
        return $out;
    }

    /** Sumber traffic: [source => jumlah]. */
    public function sources(string $since, int $limit = 10): array
    {
        $rows = $this->builder()
            ->select('source, COUNT(*) AS total')
            ->where('created_at >=', $since)
            ->where('device !=', 'bot')
            ->where('is_ajax', 0)
            ->groupBy('source')
            ->orderBy('total', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();

        $out = [];
        foreach ($rows as $row) {
            $out[] = ['source' => $row['source'] ?: 'langsung', 'total' => (int) $row['total']];
        }
        return $out;
    }

    /** Device breakdown: [device => jumlah]. */
    public function devices(string $since): array
    {
        $rows = $this->builder()
            ->select('device, COUNT(*) AS total')
            ->where('created_at >=', $since)
            ->where('device !=', 'bot')
            ->where('is_ajax', 0)
            ->groupBy('device')
            ->get()->getResultArray();

        $out = [];
        foreach ($rows as $row) {
            $out[$row['device'] ?: 'unknown'] = (int) $row['total'];
        }
        return $out;
    }

    /** Kunjungan per hari: [Y-m-d => jumlah]. */
    public function dailySince(string $since): array
    {
        $rows = $this->builder()
            ->select('substr(created_at, 1, 10) AS day, COUNT(*) AS total')
            ->where('created_at >=', $since)
            ->where('device !=', 'bot')
            ->where('is_ajax', 0)
            ->groupBy('day')
            ->orderBy('day', 'ASC')
            ->get()->getResultArray();

        $out = [];
        foreach ($rows as $row) {
            $out[$row['day']] = (int) $row['total'];
        }
        return $out;
    }

    /** Hapus data lama (privacy + ukuran tabel). */
    public function purgeOlderThan(int $days): int
    {
        return (int) $this->builder()
            ->where('created_at <', date('Y-m-d H:i:s', strtotime('-' . $days . ' days')))
            ->delete();
    }
}
