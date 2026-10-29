<?php

namespace Tests\Smoke;

use App\Database\Seeds\PusjerseySeeder;
use App\Models\CommentModel;
use App\Models\ProductModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\App;

/**
 * Smoke test alur kritis Pusat Jersey.
 *
 * Lingkungan test: SQLite :memory:, migrasi + seed PusjerseySeeder
 * dijalankan otomatis oleh DatabaseTestTrait.
 *
 * Catatan CSRF: config CSRF memakai mode 'cookie' + regenerate=false.
 * Pada setiap request POST, controller CI4 memanggil generateHash() baru
 * (service security di-reset per request), sehingga test memanggil
 * service('security')->generateHash() tepat sebelum POST untuk
 * menyamakan hash dengan yang diharapkan filter.
 */
final class SmokeTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $seed = PusjerseySeeder::class;

    /**
     * null = jalankan migrasi SEMUA namespace (App + Tests\Support).
     * Default trait ('Tests\Support') hanya menjalankan migrasi test-support,
     * sehingga tabel aplikasi tidak pernah terbentuk.
     */
    protected $namespace = null;

    /** Slug produk dari seeder. */
    private const PRODUCT_SLUG = 'jersey-original-national-team-2026';
    /** Slug post blog dari seeder. */
    private const POST_SLUG = 'cara-merawat-jersey-sublimasi-awet';

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    /**
     * Request GET halaman publik.
     */
    private function getPage(string $uri)
    {
        return $this->withHeaders(['Accept' => 'text/html'])->get($uri);
    }

    /**
     * Request POST dengan token CSRF yang valid.
     */
    private function postValid(string $uri, array $params, array $headers = [])
    {
        // Samakan hash security untuk request yang akan datang.
        service('security')->generateHash();
        $token = service('security')->getHash();

        return $this->withHeaders($headers)->post($uri, array_merge($params, [
            'csrf_test_name' => $token,
        ]));
    }

    /**
     * Simulasikan login admin (isi session is_admin via withSession).
     */
    private function actingAsAdmin()
    {
        return $this->withSession([
            'is_admin'       => true,
            'admin_username' => 'admin',
        ]);
    }

    // ------------------------------------------------------------------
    // 1. URL bersih: canonical & URL bantu TANPA index.php (bug fatal)
    // ------------------------------------------------------------------

    public function testBaseUrlDoesNotContainIndexPhp(): void
    {
        $this->assertSame('', config(App::class)->indexPage, 'Config indexPage harus string kosong');

        $generated = (string) site_url('produk');
        $this->assertStringNotContainsString('index.php', $generated, 'site_url() masih menghasilkan index.php');
    }

    public function testHomepageCanonicalHasNoIndexPhp(): void
    {
        $result = $this->getPage('/');

        $result->assertOK();
        $body = $result->response()->getBody();

        // Canonical harus ada dan bersih
        $this->assertMatchesRegularExpression(
            '/<link rel="canonical" id="page-canonical" href="[^"]*"/',
            $body,
            'Canonical link tidak ditemukan di homepage'
        );
        if (preg_match('/<link rel="canonical" id="page-canonical" href="([^"]*)"/', $body, $m)) {
            $this->assertStringNotContainsString(
                'index.php',
                $m[1],
                "Canonical homepage masih mengandung index.php: {$m[1]}"
            );
        }
    }

    public function testAllPublicPagesReturn200(): void
    {
        foreach (['/', '/produk', '/layanan', '/blog', '/portofolio', '/tentang-kami', '/kontak'] as $uri) {
            $this->getPage($uri)->assertOK();
        }
    }

    public function testDetailPagesReturn200(): void
    {
        $this->getPage('/produk/' . self::PRODUCT_SLUG)->assertOK();
        $this->getPage('/blog/' . self::POST_SLUG)->assertOK();
    }

    /** View error 404/400/403/500/production wajib memakai shell branded (bukan template bawaan CI). */
    public function testCustomErrorViewsAreBranded(): void
    {
        $views = [
            'errors/html/error_404'     => 'Halaman tidak ditemukan',
            'errors/html/error_400'     => 'Permintaan tidak valid',
            'errors/html/error_403'     => 'Akses ditolak',
            'errors/html/error_500'     => 'Terjadi kesalahan server',
            'errors/html/production'    => 'Terjadi kesalahan server',
        ];

        foreach ($views as $view => $expectedTitle) {
            $html = view($view, ['code' => 404, 'message' => 'x']);
            $this->assertStringContainsString($expectedTitle, $html, "Judul error custom hilang di {$view}");
            $this->assertStringContainsString('btn-outline', $html, "Shell branded tidak dipakai di {$view}");
            $this->assertStringContainsString('noindex', $html, "Halaman error wajib noindex ({$view})");
        }
    }

    /** Halaman /bio: UI khusus mobile-only, data dinamis, plus list layanan & slider produk. */
    public function testBioPageShowsDynamicData(): void
    {
        $result = $this->getPage('/bio');
        $result->assertOK();

        $body = $result->response()->getBody();
        // Data dinamis dari settings (about_short_desc + visi/misi dari seeder, bukan hardcode view).
        $this->assertStringContainsString('Pusat konveksi &amp; cetak custom jersey', $body, 'Data settings about tidak tampil di /bio');
        $this->assertStringContainsString('produsen jersey custom', $body, 'Visi dari settings tidak tampil di /bio');
        $this->assertStringContainsString('Visi', $body);
        $this->assertStringContainsString('Misi', $body);
        // UI mobile-only: sticky action bar + slider snap.
        $this->assertStringContainsString('fixed inset-x-0 bottom-0', $body, 'Sticky bar mobile tidak ada');
        $this->assertStringContainsString('snap-x snap-mandatory', $body, 'Slider produk bio tidak ada');
        // List layanan & produk dari DB.
        $this->assertStringContainsString('/layanan/', $body, 'List layanan bio tidak ada');
        $this->assertStringContainsString('/produk/', $body, 'Slider produk bio tidak ada');
    }

    /** Viewer log backend: wajib login & merender daftar entri log. */
    public function testAdminLogViewerRequiresAuthAndRenders(): void
    {
        $this->get('/admin/logs')->assertRedirect();

        $result = $this->actingAsAdmin()->get('/admin/logs');
        $result->assertOK();
        $body = $result->response()->getBody();
        $this->assertStringContainsString('Log Error Sistem', $body, 'Judul halaman log tidak muncul');
        $this->assertStringContainsString('log-entries', $body, 'Kontainer entri log tidak ada');
    }

    public function testPartnerSliderShowsOnHomeAndAbout(): void
    {
        $home = $this->getPage('/');
        $home->assertOK();
        $homeBody = $home->response()->getBody();
        $this->assertStringContainsString('swiper-container-init', $homeBody, 'Slider mitra tidak muncul di beranda');
        $this->assertStringContainsString('Futsal Community Jakarta', $homeBody, 'Data mitra tidak muncul di beranda');

        $about = $this->getPage('/tentang-kami');
        $about->assertOK();
        $this->assertStringContainsString(
            'Futsal Community Jakarta',
            $about->response()->getBody(),
            'Slider mitra tidak muncul di halaman tentang kami'
        );
    }

    public function testSitemapDoesNotContainIndexPhp(): void
    {
        $result = $this->getPage('/sitemap.xml');

        $result->assertOK();
        $body = $result->response()->getBody();
        $this->assertStringNotContainsString('index.php', $body, 'Sitemap masih mengandung index.php');
        $this->assertStringContainsString('<urlset', $body);
    }

    // ------------------------------------------------------------------
    // 2. Auth admin
    // ------------------------------------------------------------------

    public function testGuestIsRedirectedFromAdmin(): void
    {
        $result = $this->get('/admin/dashboard');

        $result->assertRedirect();
        $this->assertStringContainsString('admin/login', (string) $result->response()->getHeaderLine('Location'));
    }

    public function testLoginFailsWithWrongPassword(): void
    {
        $result = $this->postValid('/admin/login', [
            'username' => 'admin',
            'password' => 'password-salah-sekali',
        ]);

        $result->assertRedirect();
        $result->assertSessionMissing('is_admin');
    }

    public function testLoginSucceedsWithCorrectCredentials(): void
    {
        // Seeder menyimpan hash dari 'admin123'
        $result = $this->postValid('/admin/login', [
            'username' => 'admin',
            'password' => 'admin123',
        ]);

        $result->assertRedirect();
        $result->assertSessionHas('is_admin', true);
    }

    public function testAdminDashboardAccessibleWhenLoggedIn(): void
    {
        $result = $this->actingAsAdmin()->get('/admin/dashboard');

        $result->assertOK();
        $result->assertSee('Dashboard');
    }

    public function testLogoutRequiresPost(): void
    {
        // GET kini tidak lagi diizinkan (route logout hanya POST) —
        // di feature test, route yang tidak ada melempar PageNotFoundException.
        $this->expectException(PageNotFoundException::class);
        $this->actingAsAdmin()->get('/admin/logout');
    }

    public function testLoginRateLimitedAfterMaxAttempts(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postValid('/admin/login', [
                'username' => 'admin',
                'password' => 'salah-' . $i,
            ]);
        }

        $result = $this->postValid('/admin/login', [
            'username' => 'admin',
            'password' => 'admin123', // kredensial benar pun harus ditolak saat terblokir
        ]);

        $result->assertRedirect();
        $result->assertSessionMissing('is_admin');
    }

    // ------------------------------------------------------------------
    // 3. Validasi input endpoint POST
    // ------------------------------------------------------------------

    public function testCommentValidationRejectsBadEmail(): void
    {
        $result = $this->postValid('/blog/' . self::POST_SLUG . '/comment', [
            'author_name'  => 'Tester',
            'author_email' => 'bukan-email',
            'content'      => 'Komentar tes otomatis',
        ], ['HX-Request' => 'true']);

        $body = $result->response()->getBody();
        $this->assertStringContainsString('valid email', $body);

        // Tidak ada komentar baru tersimpan
        $count = (new CommentModel())->where('author_email', 'bukan-email')->countAllResults();
        $this->assertSame(0, $count);
    }

    public function testCommentValidIsStoredAsPending(): void
    {
        $result = $this->postValid('/blog/' . self::POST_SLUG . '/comment', [
            'author_name'  => 'Tester Automatis',
            'author_email' => 'tester@example.com',
            'content'      => 'Komentar valid untuk smoke test.',
        ], ['HX-Request' => 'true']);

        $body = $result->response()->getBody();
        $this->assertStringContainsStringIgnoringCase('moderasi', $body);

        $this->seeInDatabase('comments', [
            'author_email' => 'tester@example.com',
            'status'       => 'pending',
        ]);
    }

    public function testContactFormRejectsInvalidPhone(): void
    {
        $result = $this->postValid('/kontak/send', [
            'name'    => 'Bo',
            'phone'   => 'abc<>def',
            'message' => 'halo',
        ], ['HX-Request' => 'true']);

        $this->assertStringContainsStringIgnoringCase('phone', $result->response()->getBody());
    }

    public function testAdminProductStoreRejectsInvalidData(): void
    {
        $before = (new ProductModel())->countAllResults();

        $this->actingAsAdmin()->postValid('/admin/products/store', [
            'name'   => 'A',              // terlalu pendek
            'price'  => '-5000',          // harga negatif
            'status' => 'hacked',         // status di luar whitelist
        ]);

        $after = (new ProductModel())->countAllResults();
        $this->assertSame($before, $after, 'Produk invalid tidak boleh tersimpan');
    }

    public function testAdminProductStoreAcceptsValidData(): void
    {
        $before = (new ProductModel())->countAllResults();

        $result = $this->actingAsAdmin()->postValid('/admin/products/store', [
            'name'   => 'Produk Uji Smoke Test',
            'price'  => '150000',
            'status' => 'published',
        ]);

        $result->assertRedirect();
        $after = (new ProductModel())->countAllResults();
        $this->assertSame($before + 1, $after, 'Produk valid harus tersimpan');

        // Bersihkan
        (new ProductModel())->where('name', 'Produk Uji Smoke Test')->delete();
    }

    // ------------------------------------------------------------------
    // 4. HTMX partial & halaman 404
    // ------------------------------------------------------------------

    public function testHtmxRequestGetsPartialNotFullDocument(): void
    {
        $result = $this->getPage('/blog/' . self::POST_SLUG);

        $result->assertOK();
        $full = $result->response()->getBody();
        $this->assertStringContainsString('<!DOCTYPE html>', $full);

        // Request dengan header HX-Request tanpa HX-Boosted → partial
        $partial = $this->withHeaders(['HX-Request' => 'true'])->get('/blog/' . self::POST_SLUG);
        $partialBody = $partial->response()->getBody();
        $this->assertStringNotContainsString('<!DOCTYPE html>', $partialBody, 'HTMX request harus menerima partial, bukan dokumen penuh');
    }

    public function testUnknownPageReturns404(): void
    {
        // Di feature test, route yang tidak dikenal melempar PageNotFoundException
        $this->expectException(PageNotFoundException::class);
        $this->getPage('/halaman-tidak-ada-xyz');
    }
}
