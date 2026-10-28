<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CtaLogModel;

class Cta extends BaseController
{
    /**
     * CTA Multi-Channel Redirect (302).
     * /cta/{channel}/{slug}?source={page}&utm_source={source}
     * Channels: whatsapp, shopee, tokopedia, tiktok, affiliate
     * Logs click to cta_logs and redirects with UTM params + branded WA message.
     *
     * Sumber (dinamis): prioritas utm_source param → HTTP_REFERER → halaman produk.
     */
    public function redirect($channel, $slug)
    {
        $productModel = new ProductModel();
        $ctaLogModel  = new CtaLogModel();

        $channel = strtolower((string)$channel);
        $product = $productModel->where('slug', $slug)->first();

        $brandName    = $this->settings['brand']['name'] ?? 'Pusat Jersey';
        $productName  = $product['name'] ?? 'Custom Jersey';
        $productUrl   = site_url('produk/' . $slug);
        $siteUrl      = base_url();

        // --- Dinamis: tentukan halaman/sumber asal klik ---
        $referer  = (string)$this->request->getServer('HTTP_REFERER');
        $sourceQ  = (string)$this->request->getGet('source');
        $utmQ     = (string)$this->request->getGet('utm_source');

        // Sumber prioritas: query utm_source → source → referer → nama produk
        if ($utmQ !== '') {
            $utmSource = $utmQ;
            $sourcePage = $sourceQ !== '' ? $sourceQ : $referer;
        } elseif ($sourceQ !== '') {
            $utmSource = $sourceQ;
            $sourcePage = $sourceQ;
        } elseif ($referer !== '') {
            $utmSource = parse_url($referer, PHP_URL_PATH) ?: 'referal';
            $sourcePage = $referer;
        } else {
            $utmSource = 'halaman-produk';
            $sourcePage = $productUrl;
        }

        // Bersihkan agar menjadi label yang terbaca (bukan whatsapp_site)
        $utmSource = trim($utmSource, '/');
        $utmSource = $utmSource === '' ? 'website' : $utmSource;

        // --- Log CTA click ---
        try {
            $ctaLogModel->insert([
                'channel'      => $channel,
                'product_slug' => $slug,
                'source_page'  => $sourcePage,
                'utm_source'   => $utmSource,
                'user_agent'   => $this->request->getUserAgent()->getAgentString(),
                'ip_address'   => $this->request->getIPAddress(),
                'created_at'   => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            // Non-blocking: logging should never break the redirect
        }

        switch ($channel) {
            case 'whatsapp':
                $waNumber = $this->settings['contact']['whatsapp'] ?? '6281234567890';
                $message = "Halo Admin {$brandName},\n\n"
                    . "Saya tertarik dengan: {$productName}\n"
                    . "Link produk: {$productUrl}\n"
                    . "Halaman asal: {$sourcePage}\n"
                    . "Sumber: {$utmSource}\n\n"
                    . "Mohon info ketersediaan & cara pemesanan. Terima kasih!";
                $url = 'https://wa.me/' . $waNumber . '?text=' . rawurlencode($message);
                break;

            case 'shopee':
            case 'tokopedia':
            case 'tiktok':
                $link = $product[$channel . '_link'] ?? null;
                if ($product && $link) {
                    $sep = str_contains($link, '?') ? '&' : '?';
                    $url = $link . $sep . 'utm_source=' . rawurlencode($utmSource) . '&utm_medium=cta&utm_campaign=' . rawurlencode($slug);
                } else {
                    $url = $productUrl;
                }
                break;

            case 'affiliate':
            default:
                $link = $product['affiliate_link'] ?? null;
                if ($product && $link) {
                    $sep = str_contains($link, '?') ? '&' : '?';
                    $url = $link . $sep . 'utm_source=' . rawurlencode($utmSource) . '&utm_medium=affiliate&utm_campaign=' . rawurlencode($slug);
                } else {
                    $url = $productUrl;
                }
                break;
        }

        return redirect()->to($url, 302);
    }
}
