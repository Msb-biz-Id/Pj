<?php
/**
 * Google Analytics 4 (gtag.js) + meta verifikasi Search Console.
 * Dipasang di <head> kedua layout publik. Aman di produksi maupun dev.
 * Data dari settings group `analytics` (diisi lewat Admin → Pengaturan).
 */
$analytics = $settings['analytics'] ?? [];
$ga4Id = trim((string) ($analytics['ga4_id'] ?? ''));
$gscToken = trim((string) ($analytics['site_verification'] ?? ''));
?>
<?php if ($gscToken !== ''): ?>
    <meta name="google-site-verification" content="<?= esc($gscToken, 'attr') ?>">
<?php endif; ?>

<?php if ($ga4Id !== '' && preg_match('/^G-[A-Z0-9]{4,20}$/i', $ga4Id)): ?>
    <!-- Google Analytics 4 -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= esc($ga4Id, 'attr') ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', <?= json_encode($ga4Id) ?>, {
            anonymize_ip: true,
            send_page_view: <?= json_encode(($analytics['ga4_htmx'] ?? '0') !== '1') ?>
        });
        // Navigasi HTMX = page view baru (SPA).
        document.addEventListener('htmx:afterSettle', function (evt) {
            var el = evt.detail && evt.detail.elt;
            var url = (el && el.getAttribute) ? el.getAttribute('hx-get') || el.getAttribute('hx-post') : null;
            if (!url) { url = location.pathname + location.search; }
            gtag('event', 'page_view', {
                page_location: location.origin + url,
                page_title: document.title,
                page_path: location.pathname
            });
        });
    </script>
<?php endif; ?>
