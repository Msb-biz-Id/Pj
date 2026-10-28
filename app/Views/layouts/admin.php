<!DOCTYPE html>
<html lang="id" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="X-CSRF-TOKEN" id="page-csrf-token" content="<?= csrf_hash() ?>">
    <title><?= esc($metaTitle ?? 'Admin Panel — Pusat Jersey') ?></title>
    <link rel="shortcut icon" href="<?= esc($settings['brand']['favicon'] ?? '/favicon.ico') ?>" type="image/x-icon">

    <!-- Tailwind CSS (Compiled & Minified) -->
    <link rel="stylesheet" href="<?= base_url('assets/css/app.min.css') ?>">

    <!-- Dynamic Theme & Anti-FOUC (CSS Tokens: Editorial Minimal) -->
    <?= view('partials/dynamic_theme', ['settings' => $settings]) ?>

    <!-- HTMX 1.9.10 (local) -->
    <script src="<?= base_url('assets/js/htmx.min.js') ?>"></script>
    <!-- SortableJS (local) -->
    <script src="<?= base_url('assets/js/sortable.min.js') ?>"></script>
    <!-- Alpine.js (local) -->
    <script defer src="<?= base_url('assets/js/alpine.min.js') ?>"></script>

    <style>
        [x-cloak] { display: none !important; }
        .htmx-indicator { display: none; }
        .htmx-request .htmx-indicator { display: inline-block; }
        .htmx-request.htmx-indicator { display: inline-block; }
    </style>

    <!-- HTMX Config: CSRF Header + SPA re-init hooks -->
    <script>
        document.addEventListener('htmx:configRequest', (evt) => {
            const csrfMeta = document.querySelector('meta[name="X-CSRF-TOKEN"]');
            if (csrfMeta) {
                evt.detail.headers['X-CSRF-TOKEN'] = csrfMeta.content;
            }
        });

        function updateAdminSidebarActive() {
            const currentPath = window.location.pathname.replace(/\/+$/, '') || '/';
            document.querySelectorAll('aside a[href]').forEach(a => {
                const hrefPath = (a.pathname || new URL(a.href, window.location.origin).pathname).replace(/\/+$/, '') || '/';
                const isDashboard = (hrefPath.endsWith('/admin/dashboard') || hrefPath.endsWith('/admin'));
                const isDashboardCurrent = (currentPath.endsWith('/admin/dashboard') || currentPath.endsWith('/admin'));
                
                let isActive = false;
                if (isDashboard && isDashboardCurrent) {
                    isActive = true;
                } else if (!isDashboard && (currentPath === hrefPath || currentPath.startsWith(hrefPath + '/'))) {
                    isActive = true;
                }
                
                const icon = a.querySelector('.nav-icon');
                if (isActive) {
                    a.classList.add('bg-[var(--color-surface-alt)]', 'text-ink', 'border-line', 'font-semibold', 'shadow-sm');
                    a.classList.remove('text-muted', 'border-transparent');
                    if (icon) {
                        icon.classList.add('text-ink');
                        icon.classList.remove('text-faint');
                    }
                    if (!a.querySelector('.admin-active-dot')) {
                        const dot = document.createElement('span');
                        dot.className = 'admin-active-dot w-1.5 h-1.5 rounded-full bg-ink';
                        a.appendChild(dot);
                    }
                } else {
                    a.classList.remove('bg-[var(--color-surface-alt)]', 'text-ink', 'border-line', 'font-semibold', 'shadow-sm');
                    a.classList.add('text-muted', 'border-transparent');
                    if (icon) {
                        icon.classList.remove('text-ink');
                        icon.classList.add('text-faint');
                    }
                    const dot = a.querySelector('.admin-active-dot');
                    if (dot) dot.remove();
                }
            });
        }

        document.addEventListener('DOMContentLoaded', updateAdminSidebarActive);
        window.addEventListener('popstate', updateAdminSidebarActive);

        document.addEventListener('htmx:afterSettle', (evt) => {
            updateAdminSidebarActive();

            // Re-init Alpine.js components after HTMX swap
            if (window.Alpine) {
                Alpine.initTree(evt.detail.elt || document.body);
            }
        });
    </script>
</head>
<body hx-boost="true"
      hx-target="#admin-content"
      hx-select="#admin-content"
      hx-swap="outerHTML show:top"
      x-data="{ mobileNav: false }"
      class="min-h-screen flex flex-col bg-[var(--color-bg)] text-[var(--color-text)] font-sans antialiased selection:bg-[var(--color-text)] selection:text-[var(--color-bg)]">

    <!-- Admin Topbar -->
    <header class="h-16 bg-[var(--color-surface)]/90 backdrop-blur border-b border-line px-4 sm:px-6 flex items-center justify-between sticky top-0 z-30 transition-colors">
        <div class="flex items-center space-x-3">
            <!-- Mobile Drawer Toggle -->
            <button type="button"
                    @click="mobileNav = !mobileNav"
                    class="md:hidden p-2 -ml-1 rounded-lg text-muted hover:text-ink hover:bg-[var(--color-surface-alt)] transition-colors focus:outline-none"
                    aria-label="Toggle Navigation Drawer"
                    :aria-expanded="mobileNav">
                <span x-show="!mobileNav"><?= lucide_icon('menu', 'w-5 h-5') ?></span>
                <span x-show="mobileNav" x-cloak><?= lucide_icon('x', 'w-5 h-5') ?></span>
            </button>

            <a href="<?= site_url('admin/dashboard') ?>" class="flex items-center space-x-2 font-bold text-base text-ink font-heading group">
                <span class="p-1.5 bg-ink text-[var(--color-bg)] rounded-lg transition-transform group-hover:scale-105">
                    <?= lucide_icon('shield-check', 'w-4 h-4') ?>
                </span>
                <span class="tracking-tight"><?= esc($settings['brand']['name'] ?? 'PusatJersey') ?></span>
                <span class="mono-label text-[10px] px-2 py-0.5 rounded-full border border-line bg-[var(--color-surface-alt)]">
                    CONSOLE
                </span>
            </a>
            <?php if (!empty($settings['brand']['tagline'])): ?>
                <span class="hidden lg:inline text-xs text-faint border-l border-line pl-3">
                    <?= esc($settings['brand']['tagline']) ?>
                </span>
            <?php endif; ?>
        </div>

        <div class="flex items-center space-x-2 sm:space-x-3">
            <!-- Theme Switcher (Auto / Normal / Dark) -->
            <?= view('partials/theme_switcher', ['btnClass' => '!h-8 !w-8']) ?>

            <a href="<?= base_url() ?>" target="_blank" hx-boost="false" class="btn-outline btn-sm gap-1.5 text-xs">
                <?= lucide_icon('external-link', 'w-3.5 h-3.5') ?>
                <span class="hidden sm:inline">Lihat Situs</span>
            </a>

            <form action="<?= site_url('admin/logout') ?>" method="POST" hx-boost="false" class="inline">
                <?= csrf_field() ?>
                <button type="submit" class="btn-outline btn-sm gap-1.5 text-xs hover:border-rose-500/50 hover:text-rose-400 transition-colors">
                    <?= lucide_icon('log-out', 'w-3.5 h-3.5') ?>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </header>

    <div class="flex-1 flex overflow-hidden relative">
        <!-- Mobile Drawer Backdrop -->
        <div x-show="mobileNav"
             x-cloak
             @click="mobileNav = false"
             x-transition:enter="transition-opacity duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 md:hidden"></div>

        <!-- Sidebar Navigation (Desktop Static + Mobile Off-Canvas Drawer) -->
        <?php
        $currentUri = uri_string();
        $navItem = function ($href, $label, $icon, $matchSegment) use ($currentUri) {
            $isActive = str_contains($currentUri, $matchSegment) || ($matchSegment === 'admin/dashboard' && ($currentUri === 'admin' || $currentUri === 'admin/dashboard'));
            $activeClass = $isActive
                ? 'bg-[var(--color-surface-alt)] text-ink border border-line font-semibold shadow-sm'
                : 'text-muted hover:text-ink hover:bg-[var(--color-surface-alt)]/60 border border-transparent';
            $iconColor = $isActive ? 'text-ink' : 'text-faint group-hover:text-ink';
            
            return '<a href="' . site_url($href) . '" class="group flex items-center justify-between px-3 py-2 rounded-[10px] text-xs transition-colors ' . $activeClass . '">
                <div class="flex items-center space-x-2.5">
                    <span class="nav-icon ' . $iconColor . ' transition-colors">' . lucide_icon($icon, 'w-4 h-4') . '</span>
                    <span>' . esc($label) . '</span>
                </div>
                ' . ($isActive ? '<span class="admin-active-dot w-1.5 h-1.5 rounded-full bg-ink"></span>' : '') . '
            </a>';
        };
        ?>
        <aside :class="mobileNav ? 'translate-x-0 shadow-2xl' : '-translate-x-full md:translate-x-0'"
               @click="if ($event.target.closest('a')) mobileNav = false"
               class="fixed inset-y-0 left-0 z-50 md:static w-72 md:w-64 bg-[var(--color-surface)] border-r border-line flex flex-col shrink-0 p-4 space-y-6 overflow-y-auto transition-transform duration-200 ease-in-out md:transition-none">
            
            <!-- Section 1 -->
            <div class="space-y-1">
                <span class="mono-label px-3 text-[10px] block mb-2">01 · IKHTISAR & SISTEM</span>
                <?= $navItem('admin/dashboard', 'Dashboard Ringkasan', 'layout-dashboard', 'admin/dashboard') ?>
                <?= $navItem('admin/settings', 'Pengaturan Brand', 'sliders', 'admin/settings') ?>
                <?= $navItem('admin/sections', 'Page Builder Home', 'layers', 'admin/sections') ?>
            </div>

            <!-- Section 2 -->
            <div class="space-y-1">
                <span class="mono-label px-3 text-[10px] block mb-2">02 · KONTEN KATALOG</span>
                <?= $navItem('admin/products', 'Katalog Produk', 'shopping-bag', 'admin/products') ?>
                <?= $navItem('admin/services', 'Layanan Konveksi', 'shirt', 'admin/services') ?>
                <?= $navItem('admin/locations', 'Lokasi (SEO Lokal)', 'map-pin', 'admin/locations') ?>
                <?= $navItem('admin/blog', 'Artikel Blog', 'file-text', 'admin/blog') ?>
                <?= $navItem('admin/comments', 'Moderasi Komentar', 'message-square', 'admin/comments') ?>
            </div>

            <!-- Bottom System Status -->
            <div class="mt-auto pt-4 border-t border-line space-y-2">
                <div class="p-3 bg-[var(--color-surface-alt)] rounded-[12px] border border-line text-[11px] text-muted space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="mono-label text-[9px]">ENGINE</span>
                        <span class="inline-flex items-center gap-1 text-[10px] font-mono text-emerald-500 font-bold">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> ACTIVE
                        </span>
                    </div>
                    <p class="text-[11px] text-faint">Dual-Mode SSR + HTMX SPA v2.0</p>
                </div>
            </div>

        </aside>

        <!-- Main Content Area -->
        <main id="admin-content" hx-history-elt class="flex-1 overflow-y-auto p-6 sm:p-8 lg:p-10 max-w-7xl w-full">
            <?= $contentView ?>
        </main>
    </div>

</body>
</html>
