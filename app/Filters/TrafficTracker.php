<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Tracker traffic internal: mencatat setiap halaman publik yang dilayani.
 * - Bot/crawler & request AJAX tidak dihitung sebagai kunjungan.
 * - Session id di-hash (SHA-256, dipotong) — data mentah tidak disimpan.
 * - Penyimpanan non-blocking: kegagalan tulis tidak boleh menggagalkan halaman.
 */
class TrafficTracker implements FilterInterface
{
    /** Path yang tidak dilacak (endpoint internal atau bising). */
    private const SKIP_PREFIXES = ['admin/', 'assets/', 'api/', 'bio/items'];

    public function before(RequestInterface $request, $arguments = null)
    {
        if ($request->getMethod() !== 'GET') {
            return;
        }

        $isAjax = $request->hasHeader('HX-Request') || $request->hasHeader('X-Requested-With') ? 1 : 0;

        $uri = $request->getUri();
        $path = '/' . trim($uri->getPath(), '/');
        if ($path === '/') {
            $path = '/';
        }
        foreach (self::SKIP_PREFIXES as $prefix) {
            if (str_starts_with(ltrim($path, '/'), $prefix)) {
                return;
            }
        }

        $ua = (string) ($request->getHeaderLine('User-Agent') ?: $request->getUserAgent()->getAgentString());
        if ($this->isBot($ua)) {
            return;
        }

        $referer = (string) $request->getServer('HTTP_REFERER');
        $utmSource = (string) ($request->getGet('utm_source') ?? '');
        $utmMedium = (string) ($request->getGet('utm_medium') ?? '');
        $utmCampaign = (string) ($request->getGet('utm_campaign') ?? '');

        try {
            $sid = $this->visitorHash($request);
            (new \App\Models\PageViewModel())->insert([
                'path'         => mb_substr($path, 0, 255),
                'source'       => mb_substr($this->sourceLabel($utmSource, $referer, $request), 0, 255),
                'utm_source'   => $utmSource !== '' ? mb_substr($utmSource, 0, 100) : null,
                'utm_medium'   => $utmMedium !== '' ? mb_substr($utmMedium, 0, 100) : null,
                'utm_campaign' => $utmCampaign !== '' ? mb_substr($utmCampaign, 0, 100) : null,
                'sid'          => $sid,
                'device'       => $this->device($ua),
                'referer'      => $referer !== '' ? mb_substr($referer, 0, 255) : null,
                'is_ajax'      => $isAjax,
                'created_at'   => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            // Tracker tidak boleh pernah menggagalkan request.
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }

    /** Deteksi bot/crawler (UA + header). */
    private function isBot(string $ua): bool
    {
        if ($ua === '') {
            return true;
        }
        return (bool) preg_match(
            '/bot|crawl|slurp|spider|facebookexternalhit|facebookcatalog|twitterbot|linkedin|whatsapp|telegrambot|discordbot|embedly|quora|pinterest|redditbot|slackbot|curl|wget|python-requests|httpx|scrapy|phantom|headless|chrome-lighthouse|ahrefs|semrush|bingpreview|google-inspectiontool|applebot|yandex|duckduckbot|baiduspider/i',
            $ua
        );
    }

    /** Klasifikasi perangkat kasar. */
    private function device(string $ua): string
    {
        if (preg_match('/ipad|tablet|playbook|silk/i', $ua)) {
            return 'tablet';
        }
        if (preg_match('/mobile|iphone|android|windows phone|blackberry|opera mini/i', $ua)) {
            return 'mobile';
        }
        return 'desktop';
    }

    /**
     * Label sumber: UTM > domain referer > langsung.
     */
    private function sourceLabel(string $utmSource, string $referer, RequestInterface $request): string
    {
        if ($utmSource !== '') {
            return 'utm:' . $utmSource;
        }
        if ($referer === '') {
            return 'langsung';
        }
        $host = parse_url($referer, PHP_URL_HOST);
        if (!$host) {
            return 'langsung';
        }
        $self = $request->getUri()->getHost();
        if ($self && strcasecmp($host, $self) === 0) {
            return 'internal';
        }
        return $host;
    }

    /** Hash identitas pengunjung (IP + UA + salt app). Salt diambil dari encryption.key. */
    private function visitorHash(RequestInterface $request): string
    {
        $salt = (string) (env('encryption.key') ?: config('Encryption.key') ?: 'pusatjersey-traffic');
        $ip   = $request->getIPAddress();
        $ua   = (string) ($request->getHeaderLine('User-Agent') ?: $request->getUserAgent()->getAgentString());

        return substr(hash('sha256', $salt . '|' . $ip . '|' . $ua), 0, 32);
    }
}
