<?php

namespace App\Controllers;

use App\Models\ServiceModel;
use App\Models\LocationServiceModel;
use App\Models\FaqModel;

class Services extends BaseController
{
    public function index()
    {
        $serviceModel = new ServiceModel();
        $services = $serviceModel->where('status', 'published')->orderBy('created_at', 'DESC')->findAll();
        $totalServices = (int)$serviceModel->where('status', 'published')->countAllResults();

        $seoData = [
            'meta_title' => 'Layanan Konveksi Custom Jersey — Pusat Jersey',
            'meta_description' => 'Spesialisasi cetak jersey custom futsal, sepakbola, basket, sepeda, dan apparel olahraga berkualitas pro team.',
            'breadcrumbs' => [
                ['name' => 'Beranda', 'url' => '/'],
                ['name' => 'Layanan', 'url' => '/layanan'],
            ],
        ];

        $data = [
            'metaTitle'    => 'Layanan Konveksi Custom Jersey — Pusat Jersey',
            'metaDesc'     => 'Spesialisasi cetak jersey custom futsal, sepakbola, basket, sepeda, dan apparel olahraga berkualitas pro team.',
            'services'     => $services,
            'totalServices'=> $totalServices,
        ];

        return $this->renderView('services/index', $data, $seoData);
    }

    public function detail($slug)
    {
        $serviceModel    = new ServiceModel();
        $locationService = new LocationServiceModel();

        // 1. Exact service lookup (slug equals a published service)
        $service = $serviceModel->where('slug', $slug)->where('status', 'published')->first();
        if ($service) {
            $extraSchemas = [
                [
                    '@context' => 'https://schema.org',
                    '@type'    => 'BreadcrumbList',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => base_url()],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Layanan', 'item' => base_url('layanan')],
                        ['@type' => 'ListItem', 'position' => 3, 'name' => $service['title'], 'item' => current_url()],
                    ]
                ],
                [
                    '@context' => 'https://schema.org',
                    '@type'    => 'Service',
                    'name'     => $service['title'],
                    'description' => strip_tags($service['short_desc'] ?? $service['content']),
                    'provider' => [
                        '@type' => 'Organization',
                        'name'  => $this->settings['brand']['name'],
                    ],
                    'url' => current_url(),
                    'offers' => [
                        '@type' => 'Offer',
                        'price' => $service['starting_price'] ?? 0,
                        'priceCurrency' => 'IDR',
                    ],
                ]
            ];

            $data = [
                'metaTitle' => $service['meta_title'] ?: ($service['title'] . ' — Pusat Jersey'),
                'metaDesc'  => $service['meta_description'] ?: strip_tags($service['short_desc'] ?? ''),
                'service'   => $service,
                'faqs'      => $this->getServiceFaqs((int)$service['id']),
                'extraSchemas' => $extraSchemas,
            ];

            return $this->renderView('services/detail', $data, ['faqs' => $data['faqs']]);
        }

        // 2. Geo-location resolution: /layanan/{service-slug}-{location-slug}
        //    e.g. pusat-jersey-custom-jakarta, custom-jersey-satuan-bandung
        //    Uses the location_services pivot table (custom_title/content/meta).
        foreach ($serviceModel->where('status', 'published')->findAll() as $srv) {
            $prefix = $srv['slug'] . '-';
            if (!str_starts_with($slug, $prefix)) {
                continue;
            }

            $locSlug = substr($slug, strlen($prefix));
            $resolved = $locationService->getResolved($srv['slug'], $locSlug);

            if ($resolved) {
                $location = $resolved['location'];
                $service = [
                    'title'          => $resolved['resolved_title'],
                    'slug'           => $slug,
                    'icon'           => $resolved['service']['icon'] ?? 'map-pin',
                    'short_desc'     => $resolved['resolved_short_desc'],
                    'featured_image' => $resolved['service']['featured_image'] ?? null,
                    'starting_price' => $resolved['service']['starting_price'] ?? 95000,
                    'content'        => $resolved['resolved_content'],
                ];

                // Link internal antar halaman geo (SEO: silang link antar kota)
                $siblingLocations = array_values(array_filter(
                    $locationService->getLocationsForService((int) $srv['id']),
                    static fn ($loc) => $loc['slug'] !== $locSlug
                ));

                $faqs = $this->getServiceFaqs((int) $srv['id']);

                $data = [
                    'metaTitle' => $resolved['resolved_meta_title'],
                    'metaDesc'  => $resolved['resolved_meta_description'],
                    'service'   => $service,
                    'location'  => $location,
                    'parentServiceSlug' => $srv['slug'],
                    'siblingLocations' => $siblingLocations,
                    'faqs'      => $faqs,
                    'extraSchemas' => [
                        [
                            '@context' => 'https://schema.org',
                            '@type'    => 'BreadcrumbList',
                            'itemListElement' => [
                                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => base_url()],
                                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Layanan', 'item' => base_url('layanan')],
                                ['@type' => 'ListItem', 'position' => 3, 'name' => $srv['title'], 'item' => base_url('layanan/' . $srv['slug'])],
                                ['@type' => 'ListItem', 'position' => 4, 'name' => $location['name'], 'item' => current_url()],
                            ]
                        ],
                        [
                            '@context' => 'https://schema.org',
                            '@type'    => 'Service',
                            'name' => $service['title'],
                            'description' => $resolved['resolved_meta_description'],
                            'url' => current_url(),
                            'provider' => ['@type' => 'Organization', 'name' => $this->settings['brand']['name']],
                            'areaServed' => [
                                '@type' => 'Place',
                                'name'  => $location['name'] . (!empty($location['province']) ? ', ' . $location['province'] : ''),
                            ],
                            'offers' => [
                                '@type' => 'Offer',
                                'price' => $service['starting_price'],
                                'priceCurrency' => 'IDR',
                            ],
                        ],
                    ]
                ];

                return $this->renderView('services/detail', $data, ['faqs' => $faqs]);
            }
        }

        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Layanan tidak ditemukan');
    }

    /**
     * FAQs for a service detail page (post_type = 'service', target_id = service id).
     * Falls back to global FAQs (max 3) when no per-service rows exist.
     */
    private function getServiceFaqs(int $serviceId): array
    {
        $faqModel = new FaqModel();
        $faqs = $faqModel->where('post_type', 'service')
            ->where('target_id', $serviceId)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
        if (empty($faqs)) {
            $faqs = $faqModel->where('post_type', 'global')->orderBy('sort_order', 'ASC')->findAll(3);
        }

        return $faqs;
    }
}
