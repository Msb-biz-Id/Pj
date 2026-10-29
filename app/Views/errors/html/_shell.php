<?php
/**
 * Shell halaman error branded (404/400/403/500/general/production).
 * Statis & ringan: tanpa navbar, HTMX, atau Alpine — hanya token tema dari settings.
 *
 * Vars: int $code, string $pageTitle, string $pageDesc
 */
$code     = $code ?? 500;
$pageTitle = $pageTitle ?? 'Terjadi kesalahan';
$pageDesc  = $pageDesc ?? '';

try {
    $settings = cache()->get('settings_structured_v1') ?: (new \App\Models\SettingsModel())->getAllStructured();
} catch (\Throwable $e) {
    $settings = [];
}

$brandName  = $settings['brand']['name'] ?? 'Pusat Jersey';
$legalName  = $settings['brand']['legal_name'] ?? '';
$favicon    = $settings['brand']['favicon'] ?? '/favicon.ico';
$waNumber   = preg_replace('/[^0-9]/', '', $settings['contact']['whatsapp'] ?? '6281234567890');
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= esc($pageTitle) ?> (<?= esc($code) ?>) — <?= esc($brandName) ?></title>
    <link rel="shortcut icon" href="<?= esc($favicon) ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.min.css') ?>">
    <?= view('partials/dynamic_theme', ['settings' => $settings]) ?>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="flex min-h-screen flex-col antialiased bg-[var(--color-bg)] text-[var(--color-text)] selection:bg-primary selection:text-white">

    <main class="flex flex-grow items-center justify-center px-6 py-16">
        <div class="w-full max-w-xl text-center">
            <a href="/" class="inline-flex items-center gap-3" aria-label="<?= esc($brandName) ?> — beranda">
                <span class="grid h-10 w-10 place-items-center rounded-full bg-ink font-heading text-[13px] font-bold text-[var(--color-bg)]">PJ</span>
                <span class="font-heading text-[16px] font-semibold tracking-tight text-ink"><?= esc($brandName) ?></span>
            </a>

            <p class="mt-10 font-heading text-[120px] font-extrabold leading-none tracking-tighter text-ink opacity-[0.07] select-none" aria-hidden="true"><?= esc($code) ?></p>

            <p class="mono-label -mt-6">ERROR · <?= esc($code) ?></p>
            <h1 class="display mt-3 text-balance"><?= esc($pageTitle) ?></h1>
            <p class="lede mx-auto mt-4 max-w-md"><?= esc($pageDesc) ?></p>

            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <a href="/" class="btn-outline">Kembali ke Beranda</a>
                <a href="/kontak" class="btn-outline">Hubungi Kami</a>
                <a href="https://wa.me/<?= esc($waNumber) ?>" target="_blank" hx-boost="false" class="btn-wa">Chat WhatsApp</a>
            </div>

            <p class="meta mt-10">© <?= date('Y') ?> <?= esc($legalName !== '' ? $legalName : $brandName) ?></p>
        </div>
    </main>

</body>
</html>
