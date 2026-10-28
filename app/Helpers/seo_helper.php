<?php

if (!function_exists('generate_schema_jsonld')) {
    /**
     * Generate JSON-LD Structured Data array based on page context.
     * Returns an array of schema objects for merging and single encoding.
     */
    function generate_schema_jsonld(array $brand, array $contact, array $pageContext = []): array
    {
        $schemas = [];

        // 1. Organization & LocalBusiness
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            '@id' => base_url('#organization'),
            'name' => $brand['name'],
            'legalName' => $brand['legal_name'],
            'url' => base_url(),
            'logo' => base_url($brand['logo']),
            'image' => base_url($brand['logo']),
            'description' => $brand['tagline'],
            'telephone' => $contact['phone'],
            'email' => $contact['email'],
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $contact['address'],
                'addressLocality' => $contact['city'],
                'addressCountry' => 'ID'
            ],
            'priceRange' => '$$'
        ];

        // 2. WebSite
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            '@id' => base_url('#website'),
            'url' => base_url(),
            'name' => $brand['name'],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => base_url('search/suggest?q={search_term_string}'),
                'query-input' => 'required name=search_term_string'
            ]
        ];

        // 3. Product Page Schema
        if (isset($pageContext['product'])) {
            $prod = $pageContext['product'];
            $prodImg = !empty($prod['featured_image'])
                ? (preg_match('#^https?://#i', $prod['featured_image']) ? $prod['featured_image'] : base_url($prod['featured_image']))
                : 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=800&q=80';
            $schemas[] = [
                '@context' => 'https://schema.org',
                '@type' => 'Product',
                'name' => $prod['name'] ?? 'Produk',
                'image' => $prodImg,
                'description' => strip_tags(!empty($prod['description']) ? $prod['description'] : ($prod['name'] ?? '')),
                'sku' => !empty($prod['sku']) ? $prod['sku'] : ('PJ-' . ($prod['id'] ?? '0')),
                'offers' => [
                    '@type' => 'Offer',
                    'url' => current_url(),
                    'priceCurrency' => 'IDR',
                    'price' => (float)($prod['discount_price'] ?? $prod['price'] ?? 0),
                    'availability' => 'https://schema.org/InStock',
                    'seller' => [
                        '@type' => 'Organization',
                        'name' => $brand['name'] ?? 'Pusat Jersey'
                    ]
                ]
            ];
        }

        // 4. FAQPage Schema
        if (isset($pageContext['faqs']) && is_array($pageContext['faqs']) && count($pageContext['faqs']) > 0) {
            $mainEntity = [];
            foreach ($pageContext['faqs'] as $faq) {
                $mainEntity[] = [
                    '@type' => 'Question',
                    'name' => $faq['question'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => strip_tags($faq['answer'])
                    ]
                ];
            }
            $schemas[] = [
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => $mainEntity
            ];
        }

        // 5. BreadcrumbList Schema
        if (isset($pageContext['breadcrumbs']) && is_array($pageContext['breadcrumbs'])) {
            $itemList = [];
            $pos = 1;
            foreach ($pageContext['breadcrumbs'] as $bc) {
                $itemList[] = [
                    '@type' => 'ListItem',
                    'position' => $pos++,
                    'name' => $bc['name'],
                    'item' => base_url($bc['url'])
                ];
            }
            $schemas[] = [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => $itemList
            ];
        }

        return $schemas;
    }
}
