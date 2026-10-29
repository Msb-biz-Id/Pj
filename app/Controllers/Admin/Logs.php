<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Logs extends BaseController
{
    /** Level log standar PSR-3 (urut dari paling berat). */
    private const LEVELS = ['EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR', 'WARNING', 'NOTICE', 'INFO', 'DEBUG'];

    /** Jumlah entri terbaru yang ditampilkan. */
    private const TAIL_LIMIT = 300;

    public function index()
    {
        $dir = WRITEPATH . 'logs';

        $files = [];
        foreach (glob($dir . DIRECTORY_SEPARATOR . '*.log') ?: [] as $path) {
            $files[] = [
                'name'  => basename($path),
                'size'  => (int) filesize($path),
                'mtime' => (int) filemtime($path),
            ];
        }
        // Urut: log terbaru (nama berbasis tanggal) paling atas.
        usort($files, static fn (array $a, array $b) => strcmp($b['name'], $a['name']));

        // Validasi nama file (tolak path traversal) — hanya *.log di writable/logs.
        $active = (string) $this->request->getGet('file');
        if (!preg_match('/^[A-Za-z0-9._-]+\.log$/', $active) || !is_file($dir . DIRECTORY_SEPARATOR . $active)) {
            $active = $files[0]['name'] ?? '';
        }

        $level = strtoupper((string) $this->request->getGet('level'));
        if (!in_array($level, self::LEVELS, true)) {
            $level = '';
        }

        $allEntries = $active ? $this->parseLog($dir . DIRECTORY_SEPARATOR . $active) : [];

        $stats = array_fill_keys(self::LEVELS, 0);
        foreach ($allEntries as $entry) {
            $stats[$entry['level']] = ($stats[$entry['level']] ?? 0) + 1;
        }

        $entries = $allEntries;
        if ($level !== '') {
            $entries = array_values(array_filter($entries, static fn (array $e) => $e['level'] === $level));
        }
        // Terbaru dulu, batasi tail.
        $entries = array_slice(array_reverse($entries), 0, self::TAIL_LIMIT);

        // Catatan: view data CI4 di-merge antar view() — pakai prefix "log" agar
        // tidak bentrok dengan kunci view lain (mis. $stats di page_head).
        $entriesHtml = view('admin/logs/_entries', [
            'logEntries'     => $entries,
            'logActiveFile'  => $active,
            'logLevelFilter' => $level,
        ]);

        // HTMX non-boosted (polling 15s / tombol refresh) → kirim fragment entri saja.
        if ($this->isHtmx && !$this->isBoosted) {
            return $entriesHtml;
        }

        return $this->renderAdminView('admin/logs/index', [
            'metaTitle'      => 'Log Error Sistem — Admin',
            'logFiles'       => $files,
            'logActiveFile'  => $active,
            'logLevelFilter' => $level,
            'logEntries'     => $entries,
            'logEntriesHtml' => $entriesHtml,
            'logStats'       => $stats,
            'logTotal'       => count($allEntries),
        ]);
    }

    /**
     * Parse file log CI4 (baris: "LEVEL - tanggal --> pesan"; baris lanjutan
     * di-attach ke entri yang sama sampai baris level berikutnya muncul).
     */
    private function parseLog(string $path): array
    {
        $content = @file_get_contents($path);
        if ($content === false || $content === '') {
            return [];
        }

        $pattern = '/^(' . implode('|', self::LEVELS) . ') - (\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}) --> (.*)$/';

        $entries = [];
        $current = null;
        foreach (preg_split('/\r\n|\r|\n/', $content) as $line) {
            if (preg_match($pattern, $line, $m)) {
                if ($current !== null) {
                    $entries[] = $current;
                }
                $current = ['level' => $m[1], 'time' => $m[2], 'message' => $m[3]];
            } elseif ($current !== null && trim($line) !== '') {
                $current['message'] .= "\n" . $line;
            }
        }
        if ($current !== null) {
            $entries[] = $current;
        }

        return $entries;
    }
}
