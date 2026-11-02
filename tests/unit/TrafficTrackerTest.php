<?php

namespace Tests\Unit;

use App\Database\Seeds\PusjerseySeeder;
use App\Models\CtaLogModel;
use App\Models\PageViewModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

final class TrafficTrackerTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $seed = PusjerseySeeder::class;
    protected $namespace = null;

    public function testTrafficTrackerRecordsBrowserVisit(): void
    {
        $viewModel = new PageViewModel();
        $initialCount = $viewModel->countSince(date('Y-m-d 00:00:00'));

        $response = $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Accept'     => 'text/html',
        ])->get('/');

        $response->assertOK();

        $newCount = $viewModel->countSince(date('Y-m-d 00:00:00'));
        $this->assertSame($initialCount + 1, $newCount);

        $top = $viewModel->topPages(date('Y-m-d 00:00:00'));
        $this->assertNotEmpty($top);
        $this->assertSame('/', $top[0]['path']);

        $devices = $viewModel->devices(date('Y-m-d 00:00:00'));
        $this->assertArrayHasKey('desktop', $devices);
    }

    public function testTrafficTrackerSkipsBotAndAdmin(): void
    {
        $viewModel = new PageViewModel();
        $countBefore = $viewModel->countAllResults();

        // Bot visit
        $this->withHeaders([
            'User-Agent' => 'Googlebot/2.1 (+http://www.google.com/bot.html)',
        ])->get('/');

        $this->assertSame($countBefore, $viewModel->countAllResults());

        // Admin path visit
        $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
        ])->get('/admin/login');

        $this->assertSame($countBefore, $viewModel->countAllResults());
    }

    public function testCtaClickTracking(): void
    {
        $ctaModel = new CtaLogModel();
        $since = date('Y-m-d 00:00:00');

        $response = $this->get('/cta/whatsapp/jersey-original-national-team-2026?source=test');
        $response->assertRedirect();

        $total = $ctaModel->countSince($since);
        $this->assertGreaterThanOrEqual(1, $total);

        $channels = $ctaModel->byChannel($since);
        $this->assertNotEmpty($channels);
        $this->assertSame('whatsapp', $channels[0]['channel']);
    }

    public function testAjaxRequestsRecordedButExcludedFromMetrics(): void
    {
        $viewModel = new PageViewModel();
        $countBefore = $viewModel->countSince(date('Y-m-d 00:00:00'));

        $this->withHeaders([
            'User-Agent'  => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            'HX-Request'  => 'true',
        ])->get('/');

        // Not counted as a visit (filtered by is_ajax=0), but stored.
        $this->assertSame($countBefore, $viewModel->countSince(date('Y-m-d 00:00:00')));

        $ajaxRow = $viewModel->builder()->where('is_ajax', 1)->get()->getRowArray();
        $this->assertNotEmpty($ajaxRow);
    }
}
