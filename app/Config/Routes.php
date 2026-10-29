<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// =============================================================
// ROUTE PUBLIK — SPA HTMX Dual-Mode (SSR first-load + HTMX)
// =============================================================

$routes->get('/', 'Home::index');

// Halaman Statis Informasi (sesuai BRD: tentang-kami & kontak)
$routes->get('tentang-kami', 'Page::about');
$routes->get('bio', 'Page::bio');
$routes->get('kontak', 'Page::contact');
$routes->post('kontak/send', 'Page::sendContactForm');

// Katalog Produk
$routes->get('produk', 'Products::index');
$routes->get('produk/(:segment)', 'Products::detail/$1');

// Layanan Konveksi & Geo-Targeting (service-kota)
$routes->get('layanan', 'Services::index');
$routes->get('layanan/(:segment)', 'Services::detail/$1');

// Portofolio Desain
$routes->get('portofolio', 'Portfolio::index');
$routes->get('portofolio/(:segment)', 'Portfolio::detail/$1');

// Blog & Komentar HTMX
$routes->get('blog', 'Blog::index');
$routes->get('blog/(:segment)', 'Blog::detail/$1');
$routes->post('blog/(:segment)/comment', 'Blog::addComment/$1');

// Live Instant Search (HTMX suggest endpoint)
$routes->get('search/suggest', 'Search::suggest');

// Sitemap XML (Google Search Console)
$routes->get('sitemap.xml', 'Sitemap::index');
$routes->get('sitemap', 'Sitemap::index');

// CTA Multi-Channel Redirect (302 + log + UTM)
$routes->get('cta/(:segment)/(:segment)', 'Cta::redirect/$1/$2');

// =============================================================
// ROUTE ADMIN PANEL — Login & Auth
// =============================================================

$routes->get('admin/login', 'Admin\Login::index');
$routes->post('admin/login', 'Admin\Login::attempt');
// Logout harus POST (state-changing), bukan GET yang bisa dipicu lintas situs
$routes->post('admin/logout', 'Admin\Login::logout');

// =============================================================
// ROUTE ADMIN PANEL — HTMX Dashboard (dengan Auth filter)
// =============================================================

$routes->group('admin', ['filter' => 'auth'], function ($routes) {

    $routes->get('/', 'Admin\Dashboard::index');
    $routes->get('dashboard', 'Admin\Dashboard::index');

    // --- Settings Engine (Brand, Theme, Contact) ---
    $routes->get('settings', 'Admin\Settings::index');
    $routes->post('settings/update', 'Admin\Settings::update');

    // --- Log Error Sistem (viewer dinamis writable/logs/*.log) ---
    $routes->get('logs', 'Admin\Logs::index');

    // --- Page Builder Sections ---
    $routes->get('sections', 'Admin\Sections::index');
    $routes->post('sections/reorder', 'Admin\Sections::reorder');
    $routes->post('sections/toggle/(:num)', 'Admin\Sections::toggle/$1');
    $routes->post('sections/update/(:num)', 'Admin\Sections::update/$1');

    // --- Katalog Produk CRUD ---
    $routes->get('products', 'Admin\Products::index');
    $routes->get('products/create', 'Admin\Products::create');
    $routes->post('products/store', 'Admin\Products::store');
    $routes->get('products/edit/(:num)', 'Admin\Products::edit/$1');
    $routes->post('products/update/(:num)', 'Admin\Products::update/$1');
    $routes->post('products/delete/(:num)', 'Admin\Products::delete/$1');

    // --- Layanan Konveksi CRUD ---
    $routes->get('services', 'Admin\Services::index');
    $routes->get('services/create', 'Admin\Services::create');
    $routes->post('services/store', 'Admin\Services::store');
    $routes->get('services/edit/(:num)', 'Admin\Services::edit/$1');
    $routes->post('services/update/(:num)', 'Admin\Services::update/$1');
    $routes->post('services/delete/(:num)', 'Admin\Services::delete/$1');

    // --- Layanan × Lokasi (SEO Lokal Multi-URL) ---
    // Segmen literal "locations" tidak bentrok dengan "edit/(:num)",
    // tetap didaftarkan eksplisit agar terdokumentasi.
    $routes->get('services/locations/(:num)', 'Admin\Services::locations/$1');
    $routes->post('services/locations/(:num)/attach', 'Admin\Services::attachLocation/$1');
    $routes->post('services/locations/(:num)/update/(:num)', 'Admin\Services::updateLocationSeo/$1/$2');
    $routes->post('services/locations/(:num)/detach/(:num)', 'Admin\Services::detachLocation/$1/$2');

    // --- Master Lokasi (Kota/Kabupaten) ---
    $routes->get('locations', 'Admin\Locations::index');
    $routes->post('locations/store', 'Admin\Locations::store');
    $routes->post('locations/update/(:num)', 'Admin\Locations::update/$1');
    $routes->post('locations/delete/(:num)', 'Admin\Locations::delete/$1');

    // --- Blog CRUD ---
    $routes->get('blog', 'Admin\Blog::index');
    $routes->get('blog/create', 'Admin\Blog::create');
    $routes->post('blog/store', 'Admin\Blog::store');
    $routes->get('blog/edit/(:num)', 'Admin\Blog::edit/$1');
    $routes->post('blog/update/(:num)', 'Admin\Blog::update/$1');
    $routes->post('blog/delete/(:num)', 'Admin\Blog::delete/$1');

    // --- Moderasi Komentar ---
    $routes->get('comments', 'Admin\Comments::index');
    $routes->post('comments/approve/(:num)', 'Admin\Comments::approve/$1');
    $routes->post('comments/spam/(:num)', 'Admin\Comments::spam/$1');
    $routes->post('comments/delete/(:num)', 'Admin\Comments::delete/$1');
});
