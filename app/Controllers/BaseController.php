<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\SettingsModel;

abstract class BaseController extends Controller
{
    protected $helpers = ['url', 'form', 'icon', 'seo', 'toc', 'slug'];
    protected bool $isHtmx = false;
    protected bool $isBoosted = false;
    protected array $settings = [];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // HTMX request: partial/OOB rendering
        $this->isHtmx = $request->hasHeader('HX-Request');
        // Boosted request: full layout document needed so hx-select="#main-content" works
        $this->isBoosted = $request->hasHeader('HX-Boosted');

        // Load all settings (cached — di-flush otomatis saat admin menyimpan settings)
        $this->settings = $this->getStructuredSettings();
    }

    /**
     * Structured settings dengan cache 10 menit.
     * Dipakai semua controller; Admin\Settings::update() mem-flush-nya.
     */
    protected function getStructuredSettings(): array
    {
        $cache = cache();
        $key   = 'settings_structured_v1';

        $settings = $cache->get($key);

        if (!is_array($settings) || $settings === []) {
            $settingsModel = new SettingsModel();
            $settings      = $settingsModel->getAllStructured();
            $cache->save($key, $settings, 600);
        }

        return $settings;
    }

    /**
     * Render view with SSR / HTMX dual mode logic.
     * If HTMX: render partial view + SEO Out-of-Band (OOB) swap.
     * If SSR: render full layout containing navbar, dynamic theme, footer, and main content.
     */
    protected function renderView(string $viewName, array $data = [], array $seoData = []): string
    {
        $data['settings'] = $this->settings;
        $data['isHtmx'] = $this->isHtmx;
        $data['isBoosted'] = $this->isBoosted;

        // Default SEO values — prefer $seoData, then $data keys, then fallback
        $metaTitle = $seoData['meta_title'] ?? $data['metaTitle'] ?? ($this->settings['brand']['name'] . ' — ' . $this->settings['brand']['tagline']);
        $metaDesc = $seoData['meta_description'] ?? $data['metaDesc'] ?? $this->settings['about']['short_desc'];
        $canonical = $seoData['canonical'] ?? $data['canonical'] ?? current_url();

        $data['metaTitle'] = $metaTitle;
        $data['metaDesc'] = $metaDesc;
        $data['canonical'] = $canonical;

        // Build JSON-LD schema array
        $schemas = generate_schema_jsonld($this->settings['brand'], $this->settings['contact'], $seoData);

        // Merge any extra schemas passed by the controller (e.g., Product, BlogPosting)
        $extraSchemas = $data['extraSchemas'] ?? [];
        if (is_array($extraSchemas)) {
            $schemas = array_merge($schemas, $extraSchemas);
        }

        $data['schemaJsonLd'] = $schemas;

        // SSR: full document (direct request / crawler / boosted navigation)
        if (!$this->isHtmx || $this->isBoosted) {
            $data['contentView'] = view($viewName, $data);
            $fullDoc = view('layouts/main', $data);
            // For boosted (SPA) navigation, append OOB SEO swaps so title/meta/JSON-LD
            // update in the current document head even though hx-select extracts #main-content.
            if ($this->isBoosted) {
                $seoOob = view('partials/seo_oob', $data);
                $fullDoc = $fullDoc . "\n" . $seoOob;
            }
            return $fullDoc;
        }

        // HTMX (non-boosted, e.g. search suggest): partial fragment
        $contentHtml = view($viewName, $data);
        $seoOobHtml = view('partials/seo_oob', $data);
        return $contentHtml . "\n" . $seoOobHtml;
    }

    /**
     * Render view dengan layout mandiri (tanpa navbar/footer) — dipakai /bio
     * yang berdiri sendiri sebagai halaman profil mandiri.
     */
    protected function renderStandaloneView(string $viewName, array $data = [], array $seoData = []): string
    {
        $data['settings'] = $this->settings;
        $data['isHtmx']  = $this->isHtmx;
        $data['isBoosted'] = $this->isBoosted;

        $metaTitle = $seoData['meta_title'] ?? $data['metaTitle'] ?? ($this->settings['brand']['name'] . ' — ' . $this->settings['brand']['tagline']);
        $metaDesc  = $seoData['meta_description'] ?? $data['metaDesc'] ?? $this->settings['about']['short_desc'];
        $canonical = $seoData['canonical'] ?? $data['canonical'] ?? current_url();

        $data['metaTitle'] = $metaTitle;
        $data['metaDesc']  = $metaDesc;
        $data['canonical'] = $canonical;

        $schemas = generate_schema_jsonld($this->settings['brand'], $this->settings['contact'], $seoData);
        $extra   = $data['extraSchemas'] ?? [];
        if (is_array($extra)) {
            $schemas = array_merge($schemas, $extra);
        }
        $data['schemaJsonLd'] = $schemas;

        $data['contentView'] = view($viewName, $data);

        return view('layouts/bio', $data);
    }

    /**
     * Render admin view with SSR / HTMX dual mode logic.
     */
    protected function renderAdminView(string $viewName, array $data = []): string
    {
        $data['settings'] = $this->settings;
        $data['isHtmx'] = $this->isHtmx;
        $data['isBoosted'] = $this->isBoosted;

        // SSR: full admin layout for direct / crawler / boosted navigation
        if (!$this->isHtmx || $this->isBoosted) {
            $data['contentView'] = view($viewName, $data);
            return view('layouts/admin', $data);
        }

        // HTMX (non-boosted): partial fragment only
        return view($viewName, $data);
    }
}
