<?php

namespace App\Libraries;

use Config\Services;

/**
 * Klien minimal Google Search Console API (searchanalytics.query).
 *
 * Autentikasi: service account JSON (JWT RS256 → OAuth2 token).
 * Tanpa dependency Composer — memakai openssl + cURL bawaan PHP.
 *
 * Konfigurasi: settings group `analytics`:
 *  - analytics_gsc_site     : "sc-domain:pusatjersey.com" atau "https://pusatjersey.com/"
 *  - analytics_gsc_sa_path  : path absolut file JSON kredensial
 */
class SearchConsole
{
    private const TOKEN_URI  = 'https://oauth2.googleapis.com/token';
    private const SCOPE      = 'https://www.googleapis.com/auth/webmasters.readonly';
    private const API_BASE   = 'https://searchconsole.googleapis.com/webmasters/v3';

    private ?string $accessToken = null;
    private ?int $tokenExpiry    = null;
    private ?string $lastError    = null;

    public function __construct(
        private string $site = '',
        private string $serviceAccountPath = '',
    ) {
    }

    public function lastError(): ?string
    {
        return $this->lastError;
    }

    public function isConfigured(): bool
    {
        return $this->site !== '' && $this->serviceAccountPath !== '';
    }

    /**
     * Query search analytics.
     *
     * @param array{startDate?:string,endDate?:string,dimensions?:list<string>,rowLimit?:int,dataState?:string} $params
     * @return array{rows: list<array>, totals: array, error: ?string}
     */
    public function query(array $params = []): array
    {
        $empty = ['rows' => [], 'totals' => [], 'error' => null];

        if (!$this->isConfigured()) {
            return ['rows' => [], 'totals' => [], 'error' => 'Search Console belum dikonfigurasi (site & service account kosong).'];
        }
        if (!is_file($this->serviceAccountPath)) {
            return ['rows' => [], 'totals' => [], 'error' => 'File service account tidak ditemukan: ' . $this->serviceAccountPath];
        }

        $token = $this->getAccessToken();
        if ($token === null) {
            return ['rows' => [], 'totals' => [], 'error' => $this->lastError ?: 'Gagal memperoleh token OAuth2.'];
        }

        $body = [
            'startDate' => $params['startDate'] ?? date('Y-m-d', strtotime('-28 days')),
            'endDate'   => $params['endDate'] ?? date('Y-m-d', strtotime('-1 day')),
            'dimensions' => $params['dimensions'] ?? ['date'],
            'rowLimit'  => $params['rowLimit'] ?? 1000,
        ];
        if (!empty($params['dataState'])) {
            $body['dataState'] = $params['dataState'];
        }

        $ch = curl_init(self::API_BASE . '/sites/' . rawurlencode($this->site) . '/searchAnalytics/query');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS     => json_encode($body),
            CURLOPT_TIMEOUT        => 20,
        ]);

        $raw  = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($raw === false || $code >= 400) {
            $decoded = $raw ? json_decode($raw, true) : null;
            $msg = $decoded['error']['message'] ?? ('HTTP ' . $code);
            $this->lastError = 'Search Console API: ' . $msg;
            return ['rows' => [], 'totals' => [], 'error' => $this->lastError];
        }

        $decoded = json_decode((string) $raw, true);
        $rows = [];
        foreach ($decoded['rows'] ?? [] as $row) {
            $keys = $row['keys'] ?? [];
            $rows[] = [
                'keys'    => $keys,
                'label'   => count($keys) === 1 ? (string) $keys[0] : implode(' · ', $keys),
                'clicks'  => (int) ($row['clicks'] ?? 0),
                'impr'    => (int) ($row['impressions'] ?? 0),
                'ctr'     => (float) ($row['ctr'] ?? 0),
                'position'=> (float) ($row['position'] ?? 0),
            ];
        }

        return ['rows' => $rows, 'totals' => $decoded['responseHeader'] ?? [], 'error' => null];
    }

    /**
     * Ambil access token via JWT assertion → OAuth2 token endpoint.
     */
    private function getAccessToken(): ?string
    {
        if ($this->accessToken !== null && $this->tokenExpiry !== null && $this->tokenExpiry > time() + 60) {
            return $this->accessToken;
        }

        $raw = @file_get_contents($this->serviceAccountPath);
        if ($raw === false) {
            $this->lastError = 'Gagal membaca file service account.';
            return null;
        }
        $sa = json_decode($raw, true);
        if (!is_array($sa) || empty($sa['client_email']) || empty($sa['private_key'])) {
            $this->lastError = 'Format service account JSON tidak valid.';
            return null;
        }

        $now  = time();
        $jwt  = $this->buildJwt($sa, $now);
        $post = http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ]);

        $ch = curl_init(self::TOKEN_URI);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_POSTFIELDS     => $post,
            CURLOPT_TIMEOUT        => 20,
        ]);
        $res  = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($res === false || $code >= 400) {
            $decoded = $res ? json_decode($res, true) : null;
            $this->lastError = 'OAuth2 token: ' . ($decoded['error_description'] ?? ('HTTP ' . $code));
            return null;
        }

        $data = json_decode((string) $res, true);
        if (empty($data['access_token'])) {
            $this->lastError = 'OAuth2 token: access_token tidak ditemukan.';
            return null;
        }

        $this->accessToken = (string) $data['access_token'];
        $this->tokenExpiry  = time() + (int) ($data['expires_in'] ?? 3600);

        return $this->accessToken;
    }

    /** Buat JWT RS256 (header + claims, ditandatangani openssl). */
    private function buildJwt(array $sa, int $now): string
    {
        $header = ['alg' => 'RS256', 'typ' => 'JWT'];
        $claims = [
            'iss'   => $sa['client_email'],
            'scope' => self::SCOPE,
            'aud'   => self::TOKEN_URI,
            'iat'   => $now,
            'exp'   => $now + 3600,
        ];

        $signingInput = $this->b64(json_encode($header)) . '.' . $this->b64(json_encode($claims));
        $signature    = '';
        openssl_sign($signingInput, $signature, $sa['private_key'], OPENSSL_ALGO_SHA256);

        return $signingInput . '.' . $this->b64($signature);
    }

    /** base64url tanpa padding. */
    private function b64(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
