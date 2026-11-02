<?php

namespace Tests\Unit;

use App\Database\Seeds\PusjerseySeeder;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

final class LineiconRenderTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $seed = PusjerseySeeder::class;
    protected $namespace = null;

    public function testHomeRendersLineiconsSvg(): void
    {
        $r = $this->withHeaders(['Accept' => 'text/html', 'User-Agent' => 'Mozilla/5.0 TestAgent'])->get('/');
        $r->assertOK();
        $body = $r->response()->getBody();
        $this->assertStringContainsString('fill="currentColor"', $body);
    }
}
