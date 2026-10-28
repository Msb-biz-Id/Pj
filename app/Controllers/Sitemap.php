<?php

namespace App\Controllers;

use App\Models\PostModel;
use App\Models\ProductModel;
use App\Models\ServiceModel;
use App\Models\PortfolioModel;
use App\Models\LocationServiceModel;

class Sitemap extends BaseController
{
    /**
     * Generate sitemap.xml for Google Search Console.
     * Includes: home, static pages, services, geo-location pages, products, blog, portfolio.
     */
    public function index()
    {
        $urls = [];

        // Home & Static Pages
        $urls[] = ['loc' => base_url(), 'changefreq' => 'daily', 'priority' => '1.0'];
        $urls[] = ['loc' => base_url('produk'), 'changefreq' => 'daily', 'priority' => '0.9'];
        $urls[] = ['loc' => base_url('layanan'), 'changefreq' => 'weekly', 'priority' => '0.9'];
        $urls[] = ['loc' => base_url('portofolio'), 'changefreq' => 'weekly', 'priority' => '0.8'];
        $urls[] = ['loc' => base_url('blog'), 'changefreq' => 'weekly', 'priority' => '0.8'];
        $urls[] = ['loc' => base_url('tentang-kami'), 'changefreq' => 'yearly', 'priority' => '0.5'];
        $urls[] = ['loc' => base_url('kontak'), 'changefreq' => 'yearly', 'priority' => '0.5'];

        // Services + Geo-Location Pages
        $serviceModel = new ServiceModel();
        $locServiceModel = new LocationServiceModel();
        foreach ($serviceModel->where('status', 'published')->findAll() as $service) {
            $urls[] = ['loc' => base_url('layanan/' . $service['slug']), 'changefreq' => 'monthly', 'priority' => '0.8'];
        }
        // Query location_services with service+location slugs (hanya layanan published)
        $db = \Config\Database::connect();
        $geoRows = $db->table('location_services')
            ->select('services.slug as service_slug, locations.slug as location_slug')
            ->join('services', 'services.id = location_services.service_id')
            ->join('locations', 'locations.id = location_services.location_id')
            ->where('services.status', 'published')
            ->get()->getResultArray();
        foreach ($geoRows as $g) {
            $urls[] = ['loc' => base_url('layanan/' . $g['service_slug'] . '-' . $g['location_slug']), 'changefreq' => 'monthly', 'priority' => '0.7'];
        }

        // Products
        foreach ((new ProductModel())->where('status', 'published')->findAll() as $p) {
            $urls[] = ['loc' => base_url('produk/' . $p['slug']), 'changefreq' => 'weekly', 'priority' => '0.9'];
        }

        // Blog Posts
        foreach ((new PostModel())->where('status', 'published')->findAll() as $post) {
            $urls[] = ['loc' => base_url('blog/' . $post['slug']), 'changefreq' => 'monthly', 'priority' => '0.7'];
        }

        // Portfolio
        foreach ((new PortfolioModel())->where('status', 'published')->findAll() as $pf) {
            $urls[] = ['loc' => base_url('portofolio/' . $pf['slug']), 'changefreq' => 'monthly', 'priority' => '0.6'];
        }

        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:image=\"http://www.google.com/schemas/sitemap-image/1.1\">\n";
        foreach ($urls as $u) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . esc($u['loc']) . "</loc>\n";
            $xml .= "    <changefreq>" . $u['changefreq'] . "</changefreq>\n";
            $xml .= "    <priority>" . $u['priority'] . "</priority>\n";
            if (isset($u['lastmod'])) {
                $xml .= "    <lastmod>" . $u['lastmod'] . "</lastmod>\n";
            }
            $xml .= "  </url>\n";
        }
        $xml .= "</urlset>";

        return $this->response
            ->setContentType('application/xml')
            ->setBody($xml);
    }
}
