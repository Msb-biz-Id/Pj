<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingsModel;

class Login extends BaseController
{
    /** Maks percobaan login gagal per IP sebelum diblok sementara. */
    private const MAX_ATTEMPTS = 5;
    /** Durasi blokir & jendela hitung percobaan (detik). */
    private const LOCKOUT_SECONDS = 900; // 15 menit

    public function index()
    {
        $session = session();

        // Already logged in? Redirect to dashboard
        if ($session->get('is_admin') === true) {
            return redirect()->to('/admin/dashboard');
        }

        return view('admin/login', [
            'metaTitle' => 'Login Admin — Pusat Jersey',
            'settings'  => $this->settings,
        ]);
    }

    public function attempt()
    {
        $session  = session();
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        if (empty($username) || empty($password)) {
            return $this->fail('Username dan password harus diisi.');
        }

        // --- Rate limiting per IP (anti brute force) ---
        $ip      = $this->request->getIPAddress();
        $cache   = cache();
        $key     = 'login_attempts_' . md5($ip);
        $attempts = (int)($cache->get($key) ?? 0);

        if ($attempts >= self::MAX_ATTEMPTS) {
            $minutes = (int)ceil(self::LOCKOUT_SECONDS / 60);
            return $this->fail("Terlalu banyak percobaan gagal. Coba lagi dalam {$minutes} menit.");
        }

        $settingsModel = new SettingsModel();
        $brandSettings = $settingsModel->getGroup('brand');

        // Prioritas kredensial dari .env untuk keamanan terbaik (12-Factor App)
        $adminUsername = env('ADMIN_USERNAME') ?: ($brandSettings['admin_username'] ?? 'admin');
        $adminHash     = env('ADMIN_PASSWORD_HASH') ?: ($brandSettings['admin_password_hash'] ?? '');
        $envPlainPass  = env('ADMIN_PASSWORD');

        $isValidUser = is_string($username) && hash_equals((string)$adminUsername, (string)$username);
        $isValidPass = false;

        if (is_string($password) && $password !== '') {
            if (!empty($adminHash) && password_verify($password, $adminHash)) {
                $isValidPass = true;
            } elseif (!empty($envPlainPass) && hash_equals((string)$envPlainPass, (string)$password)) {
                $isValidPass = true;
            }
        }

        if ($isValidUser && $isValidPass) {
            // Reset counter percobaan gagal
            $cache->delete($key);

            // Regenerate session ID — cegah session fixation
            $session->regenerate(true);
            $session->set('is_admin', true);
            $session->set('admin_username', $username);
            $session->set('last_activity', time());

            // HTMX redirect
            if ($this->isHtmx) {
                return service('response')
                    ->setHeader('HX-Redirect', site_url('admin/dashboard'))
                    ->setBody('<div class="p-4 text-xs text-emerald-400">Login berhasil! Mengalihkan...</div>');
            }

            return redirect()->to('/admin/dashboard');
        }

        // Catat percobaan gagal
        $cache->save($key, $attempts + 1, self::LOCKOUT_SECONDS);
        $remaining = self::MAX_ATTEMPTS - ($attempts + 1);

        $msg = 'Username atau password salah.';
        if ($remaining > 0 && $remaining <= 2) {
            $msg .= " Sisa {$remaining} percobaan sebelum diblokir sementara.";
        }

        return $this->fail($msg);
    }

    public function logout()
    {
        $session = session();

        // Regenerate session ID SEBELUM destroy (regenerate setelah destroy akan error
        // karena session sudah tidak ada).
        $session->regenerate(true);
        $session->destroy();

        return redirect()->to('/admin/login');
    }

    /**
     * Respons gagal seragam untuk HTMX & non-HTMX.
     */
    private function fail(string $message)
    {
        if ($this->isHtmx) {
            return '<div class="p-4 bg-rose-500/10 border border-rose-500/30 text-rose-400 rounded-2xl text-xs">' . esc($message) . '</div>';
        }

        return redirect()->back()->with('error', $message);
    }
}
