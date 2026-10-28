<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($metaTitle ?? 'Login Admin — Pusat Jersey') ?></title>
    <link rel="shortcut icon" href="<?= esc($settings['brand']['favicon'] ?? '/favicon.ico') ?>" type="image/x-icon">

    <!-- Tailwind CSS (Compiled & Minified) -->
    <link rel="stylesheet" href="<?= base_url('assets/css/app.min.css') ?>">

    <!-- Dynamic Theme & CSS Variables -->
    <?= view('partials/dynamic_theme', ['settings' => $settings]) ?>

    <!-- HTMX 1.9.10 (local) -->
    <script src="<?= base_url('assets/js/htmx.min.js') ?>"></script>

    <style>
        [x-cloak] { display: none !important; }
        .htmx-indicator { display: none; }
        .htmx-request .htmx-indicator { display: inline-block; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-5 bg-[var(--color-bg)] text-[var(--color-text)] font-sans antialiased selection:bg-[var(--color-text)] selection:text-[var(--color-bg)]">

    <div class="w-full max-w-md">
        <!-- Editorial Minimal Login Card -->
        <div class="rounded-[20px] border border-line bg-[var(--color-surface)] p-8 sm:p-10 shadow-2xl space-y-6">
            
            <!-- Brand & Eyebrow -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="mono-label">00 · OTENTIKASI ADMIN</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-mono font-medium border border-line text-muted bg-[var(--color-surface-alt)]">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        SECURE CONSOLE
                    </span>
                </div>

                <div class="space-y-1">
                    <h1 class="font-heading text-2xl font-bold tracking-tight text-ink">
                        <?= esc($settings['brand']['name'] ?? 'Pusat Jersey') ?>
                    </h1>
                    <p class="text-xs text-muted leading-relaxed">
                        Masuk dengan kredensial terverifikasi untuk mengelola katalog, konten, dan konfigurasi brand.
                    </p>
                </div>
            </div>

            <div class="rule"></div>

            <!-- Alert (HTMX target untuk pesan error login) -->
            <div id="login-alert">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="p-4 bg-rose-500/10 border border-rose-500/30 text-rose-400 rounded-[12px] text-xs leading-relaxed flex items-start gap-2.5">
                        <div class="mt-0.5 shrink-0"><?= lucide_icon('alert-circle', 'w-4 h-4') ?></div>
                        <div><?= esc(session()->getFlashdata('error')) ?></div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Login Form -->
            <form hx-post="<?= site_url('admin/login') ?>"
                  hx-target="#login-alert"
                  hx-swap="innerHTML"
                  class="space-y-4">
                <?= csrf_field() ?>

                <div class="space-y-1.5">
                    <label class="lbl flex items-center justify-between" for="login-username">
                        <span>Username Administrator</span>
                        <span class="text-[11px] font-normal text-faint">Wajib</span>
                    </label>
                    <div class="relative">
                        <input type="text"
                               id="login-username"
                               name="username"
                               required
                               autocomplete="username"
                               autofocus
                               class="field text-xs pl-10"
                               placeholder="admin">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-faint pointer-events-none">
                            <?= lucide_icon('user', 'w-4 h-4') ?>
                        </div>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="lbl flex items-center justify-between" for="login-password">
                        <span>Kata Sandi (Password)</span>
                        <span class="text-[11px] font-normal text-faint">Terenkripsi</span>
                    </label>
                    <div class="relative">
                        <input type="password"
                               id="login-password"
                               name="password"
                               required
                               autocomplete="current-password"
                               class="field text-xs pl-10 tracking-widest"
                               placeholder="••••••••">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-faint pointer-events-none">
                            <?= lucide_icon('lock', 'w-4 h-4') ?>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="btn-primary w-full h-11 text-xs gap-2 font-semibold">
                        <span>Masuk ke Dashboard</span>
                        <span class="htmx-indicator inline-block animate-spin">⟳</span>
                        <?= lucide_icon('arrow-right', 'w-4 h-4') ?>
                    </button>
                </div>
            </form>

            <div class="rule"></div>

            <!-- Footer Link -->
            <div class="flex items-center justify-between text-xs text-muted">
                <a href="<?= base_url() ?>" class="link-quiet inline-flex items-center gap-1.5 hover:text-ink">
                    <?= lucide_icon('arrow-left', 'w-3.5 h-3.5') ?>
                    <span>Kembali ke Beranda</span>
                </a>
                <span class="font-mono text-[10px] text-faint">v2.0 · SPA HTMX</span>
            </div>

        </div>
    </div>

</body>
</html>