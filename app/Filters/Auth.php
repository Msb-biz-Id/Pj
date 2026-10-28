<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Auth implements FilterInterface
{
    /**
     * Protect admin routes. Redirect to login when not authenticated.
     * For HTMX requests, respond with HX-Redirect header so the SPA
     * navigates to the login page without a full reload.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if ($session->get('is_admin') !== true) {
            $loginUrl = site_url('admin/login');

            if ($request->hasHeader('HX-Request')) {
                return service('response')
                    ->setHeader('HX-Redirect', $loginUrl)
                    ->setStatusCode(200)
                    ->setBody('<div class="p-4 text-xs text-amber-400">Sesi berakhir. Mengalihkan ke login...</div>');
            }

            return redirect()->to($loginUrl);
        }

        // Cek session idle timeout (maksimal 2 jam tanpa aktivitas)
        $lastActivity = $session->get('last_activity');
        $maxIdleTime  = 7200; // 2 jam dalam detik

        if ($lastActivity && (time() - (int)$lastActivity > $maxIdleTime)) {
            $session->destroy();
            $loginUrl = site_url('admin/login');

            if ($request->hasHeader('HX-Request')) {
                return service('response')
                    ->setHeader('HX-Redirect', $loginUrl)
                    ->setStatusCode(200)
                    ->setBody('<div class="p-4 text-xs text-amber-400">Sesi telah berakhir karena tidak ada aktivitas. Silakan login kembali.</div>');
            }

            return redirect()->to($loginUrl)->with('error', 'Sesi telah berakhir karena tidak ada aktivitas. Silakan login kembali.');
        }

        // Perbarui waktu aktivitas terakhir
        $session->set('last_activity', time());

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}
