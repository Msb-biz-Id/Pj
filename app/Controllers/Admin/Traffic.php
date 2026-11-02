<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\SearchConsole;
use App\Models\CtaLogModel;
use App\Models\PageViewModel;

/**
 * Analitik admin: konversi CTA (cta_logs) + traffic internal (page_views)
 * + laporan Google Search Console (opsional, bila service account dikonfigurasi).
 */
class Traffic extends BaseController
{
    /** Rentang hari yang diizinkan. */
    private const RANGES = [7, 30, 90];

    public function index()
    {
        $range = (int) ($this->request->getGet('range') ?? 30);
        if (!in_array($range, self::RANGES, true)) {
            $range = 30;
        }
        $since = date('Y-m-d 00:00:00', strtotime('-' . ($range - 1) . ' days'));

        $ctaModel  = new CtaLogModel();
        $viewModel = new PageViewModel();

        $data = [
            'metaTitle'     => 'Analitik Traffic & Konversi — Admin',
            'range'         => $range,
            'ranges'        => self::RANGES,
            'since'         => $since,

            'ctaTotal'      => $ctaModel->countSince($since),
            'ctaByChannel'  => $ctaModel->byChannel($since),
            'ctaTopSources' => $ctaModel->topSources($since),
            'ctaTopProducts'=> $ctaModel->topProducts($since),
            'ctaDaily'      => $ctaModel->dailySince($since),
            'ctaRecent'     => $ctaModel->recent(20),

            'pageViewsTotal'    => $viewModel->countSince($since),
            'pageViewsVisitors' => $viewModel->uniqueVisitorsSince($since),
            'trafficTopPages'  => $viewModel->topPages($since),
            'trafficSources'   => $viewModel->sources($since),
            'trafficDevices'   => $viewModel->devices($since),
            'trafficDaily'     => $viewModel->dailySince($since),
        ];

        return $this->renderAdminView('admin/traffic/index', $data);
    }

    /**
     * Laporan Search Console: clicks, impressions, CTR, posisi per halaman & per query.
     * Route: /admin/traffic/search-console
     */
    public function searchConsole()
    {
        $analytics = $this->settings['analytics'] ?? [];
        $site     = (string) ($analytics['gsc_site'] ?? '');
        $saPath   = (string) ($analytics['gsc_sa_path'] ?? '');

        $days = (int) ($this->request->getGet('days') ?? 28);
        if (!in_array($days, [7, 28, 90], true)) {
            $days = 28;
        }

        $client = new SearchConsole($site, $saPath);
        $data = [
            'metaTitle'   => 'Laporan Google Search Console — Admin',
            'gscConfigured' => $client->isConfigured(),
            'gscSite'     => $site,
            'days'        => $days,
            'gscPages'    => [],
            'gscQueries'  => [],
            'gscTotals'   => ['clicks' => 0, 'impr' => 0],
            'gscError'    => null,
        ];

        if ($client->isConfigured()) {
            $end   = date('Y-m-d', strtotime('-1 day'));
            $start = date('Y-m-d', strtotime('-' . $days . ' days'));

            $pages = $client->query([
                'startDate'  => $start,
                'endDate'    => $end,
                'dimensions' => ['page'],
                'rowLimit'   => 25,
            ]);
            $queries = $client->query([
                'startDate'  => $start,
                'endDate'    => $end,
                'dimensions' => ['query'],
                'rowLimit'   => 25,
            ]);

            $data['gscPages']   = $pages['rows'];
            $data['gscQueries'] = $queries['rows'];
            $data['gscError']   = $pages['error'] ?? $queries['error'];

            foreach ($pages['rows'] as $r) {
                $data['gscTotals']['clicks'] += $r['clicks'];
                $data['gscTotals']['impr']   += $r['impr'];
            }
        }

        return $this->renderAdminView('admin/traffic/search_console', $data);
    }
}
