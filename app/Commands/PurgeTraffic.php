<?php

namespace App\Commands;

use App\Models\PageViewModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Purge traffic page_views older than N days (default 90).
 * Usage: php spark traffic:purge [days]
 */
class PurgeTraffic extends BaseCommand
{
    protected $group       = 'traffic';
    protected $name        = 'traffic:purge';
    protected $description = 'Hapus data page_views yang lebih lama dari N hari (default 90).';
    protected $usage       = 'traffic:purge [days]';

    public function run(array $params)
    {
        $days = (int) ($params[0] ?? 90);
        if ($days < 1) {
            $days = 90;
        }

        $deleted = (new PageViewModel())->purgeOlderThan($days);
        CLI::write('Terhapus ' . $deleted . ' baris page_views (lebih tua dari ' . $days . ' hari).', 'green');
    }
}
