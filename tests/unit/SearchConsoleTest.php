<?php

namespace Tests\Unit;

use App\Libraries\SearchConsole;
use CodeIgniter\Test\CIUnitTestCase;

final class SearchConsoleTest extends CIUnitTestCase
{
    public function testNotConfiguredReturnsErrorGracefully(): void
    {
        $client = new SearchConsole('', '');
        $this->assertFalse($client->isConfigured());

        $res = $client->query();
        $this->assertEmpty($res['rows']);
        $this->assertStringContainsString('belum dikonfigurasi', $res['error']);
    }

    public function testMissingServiceAccountFileReturnsErrorGracefully(): void
    {
        $client = new SearchConsole('sc-domain:example.com', '/path/to/nonexistent-sa.json');
        $this->assertTrue($client->isConfigured());

        $res = $client->query();
        $this->assertEmpty($res['rows']);
        $this->assertStringContainsString('tidak ditemukan', $res['error']);
    }
}
