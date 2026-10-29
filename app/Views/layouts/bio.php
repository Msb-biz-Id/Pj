<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="X-CSRF-TOKEN" id="page-csrf-token" content="<?= csrf_hash() ?>">
    <title id="page-title"><?= esc($metaTitle ?? 'Pusat Jersey') ?></title>
    <meta name="description" id="page-meta-desc" content="<?= esc($metaDesc ?? '') ?>">
    <link rel="canonical" id="page-canonical" href="<?= esc($canonical ?? current_url()) ?>">
    <link rel="shortcut icon" href="<?= esc($settings['brand']['favicon'] ?? '/favicon.ico') ?>" type="image/x-icon">

    <meta property="og:title" id="og-title" content="<?= esc($metaTitle ?? '') ?>">
    <meta property="og:description" id="og-desc" content="<?= esc($metaDesc ?? '') ?>">
    <meta property="og:url" id="og-url" content="<?= esc($canonical ?? current_url()) ?>">
    <meta property="og:site_name" content="<?= esc($settings['brand']['name'] ?? 'Pusat Jersey') ?>">
    <meta property="og:type" content="website">
    <meta property="og:image" content="<?= esc($settings['brand']['logo'] ?? '/favicon.ico') ?>">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="stylesheet" href="<?= base_url('assets/css/app.min.css') ?>">
    <script>document.documentElement.classList.add('js-anim');</script>
    <link rel="stylesheet" href="<?= base_url('assets/css/motion.css') ?>">

    <?= view('partials/dynamic_theme', ['settings' => $settings]) ?>
    <noscript><style>[x-cloak]{display:none!important}</style></noscript>

    <script src="<?= base_url('assets/js/htmx.min.js') ?>"></script>
    <script defer src="<?= base_url('assets/js/alpine.min.js') ?>"></script>
    <link rel="stylesheet" href="<?= base_url('assets/css/swiper-bundle.min.css') ?>">
    <script src="<?= base_url('assets/js/swiper-bundle.min.js') ?>"></script>

    <?php if (!empty($schemaJsonLd)): ?>
    <script type="application/ld+json" id="page-jsonld">
    <?= json_encode($schemaJsonLd, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
    </script>
    <?php endif; ?>

    <style>
        [x-cloak] { display: none !important; }
        html { scroll-padding-top: 1.5rem; }
    </style>
</head>
<body class="flex min-h-screen flex-col antialiased bg-[var(--color-bg)] text-[var(--color-text)] selection:bg-primary selection:text-white">

    <!-- Bio = halaman mandiri: tanpa navbar, tanpa footer -->

    <main id="main-content" class="flex-grow">
        <?= $contentView ?>
    </main>

    <script>
        // Load-more AJAX (HTMX) + reveal engine untuk elemen baru
        (function () {
            const reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            function revealAll(scope) {
                const els = (scope || document).querySelectorAll('[data-reveal]:not(.is-in)');
                if (!els.length) return;
                if (reduce || !('IntersectionObserver' in window)) {
                    els.forEach((el) => el.classList.add('is-in'));
                    return;
                }
                const io = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-in');
                            io.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
                els.forEach((el) => io.observe(el));
            }
            document.addEventListener('DOMContentLoaded', () => revealAll(document));
            document.addEventListener('htmx:afterSettle', (evt) => {
                revealAll(evt.detail && evt.detail.elt ? evt.detail.elt : document);
            });
        })();
    </script>
</body>
</html>
