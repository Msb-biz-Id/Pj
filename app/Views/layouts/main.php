<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="X-CSRF-TOKEN" id="page-csrf-token" content="<?= csrf_hash() ?>">
    <title id="page-title"><?= esc($metaTitle ?? 'Pusat Jersey — Custom Jersey & Apparel Olahraga No.1') ?></title>
    <meta name="description" id="page-meta-desc" content="<?= esc($metaDesc ?? 'Produsen jersey custom futsal, sepakbola, basket, dan sepeda berkualitas tinggi dengan cetak sublimasi Jepang.') ?>">
    <link rel="canonical" id="page-canonical" href="<?= esc($canonical ?? current_url()) ?>">
    <link rel="shortcut icon" href="<?= esc($settings['brand']['favicon'] ?? '/favicon.ico') ?>" type="image/x-icon">

    <!-- Open Graph / Social Meta -->
    <meta property="og:title" id="og-title" content="<?= esc($metaTitle ?? 'Pusat Jersey') ?>">
    <meta property="og:description" id="og-desc" content="<?= esc($metaDesc ?? '') ?>">
    <meta property="og:url" id="og-url" content="<?= esc($canonical ?? current_url()) ?>">
    <meta property="og:site_name" content="<?= esc($settings['brand']['name'] ?? 'Pusat Jersey') ?>">
    <meta property="og:type" content="website">
    <meta property="og:image" content="<?= esc($settings['brand']['logo'] ?? '/favicon.ico') ?>">
    <meta property="og:locale" content="id_ID">
    <meta name="twitter:card" content="summary_large_image">

    <!-- Tailwind CSS (Compiled & Minified) — harus SEBELUM dynamic theme agar token CSS dari settings menimpa dengan pasti -->
    <link rel="stylesheet" href="<?= base_url('assets/css/app.min.css') ?>">

    <!-- Motion: scroll reveal + keyframes (CSS-first, non-blocking) -->
    <script>document.documentElement.classList.add('js-anim');</script>
    <link rel="stylesheet" href="<?= base_url('assets/css/motion.css') ?>">

    <!-- Dynamic Theme & Anti-FOUC (setelah Tailwind) -->
    <?= view('partials/dynamic_theme', ['settings' => $settings]) ?>
    <noscript><style>[x-cloak]{display:none!important}</style></noscript>

    <!-- HTMX 1.9.10 (local) -->
    <script src="<?= base_url('assets/js/htmx.min.js') ?>"></script>

    <!-- Anime.js (local) -->
    <script src="<?= base_url('assets/js/anime.min.js') ?>"></script>

    <!-- Alpine.js 3.x (local, defer) -->
    <script defer src="<?= base_url('assets/js/alpine.min.js') ?>"></script>

    <!-- Swiper 11 CSS & JS (local) -->
    <link rel="stylesheet" href="<?= base_url('assets/css/swiper-bundle.min.css') ?>">
    <script src="<?= base_url('assets/js/swiper-bundle.min.js') ?>"></script>

    <!-- GLightbox CSS & JS (local) -->
    <link rel="stylesheet" href="<?= base_url('assets/css/glightbox.min.css') ?>">
    <script src="<?= base_url('assets/js/glightbox.min.js') ?>"></script>

    <!-- Structured Data (JSON-LD) -->
    <?php if (!empty($schemaJsonLd)): ?>
    <script type="application/ld+json" id="page-jsonld">
    <?= json_encode($schemaJsonLd, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
    </script>
    <?php endif; ?>

    <style>
        [x-cloak] { display: none !important; }
        .htmx-indicator { display: none; }
        .htmx-request .htmx-indicator { display: inline-block; }
        .htmx-request.htmx-indicator { display: inline-block; }
        /* Offset navbar sticky (68px) untuk anchor in-page & scrollIntoView. */
        html { scroll-padding-top: calc(68px + 1.25rem); }
    </style>
</head>
<body hx-boost="true" hx-target="#main-content" hx-select="#main-content" hx-swap="outerHTML" class="min-h-screen flex flex-col antialiased selection:bg-primary selection:text-white">

    <!-- Global Progress Bar for HTMX SPA Navigation -->
    <div id="htmx-progress-bar" class="fixed top-0 left-0 right-0 h-[2px] bg-ink z-50 transition-all duration-300 opacity-0 pointer-events-none"></div>

    <!-- Header Navbar -->
    <?= view('partials/navbar', ['settings' => $settings]) ?>

    <!-- Main Content Container (SPA Swap Target) -->
    <main id="main-content" hx-history-elt class="flex-grow">
        <?= $contentView ?>
    </main>

    <!-- Footer -->
    <?= view('partials/footer', ['settings' => $settings]) ?>

    <!-- Instant Live Search Modal Overlay -->
    <?= view('partials/search_overlay', ['settings' => $settings]) ?>

    <!-- Re-initialization Hooks for HTMX Navigation -->
    <script>
        // ================= ANIMATION ENGINE (anime.js + HTMX SPA) =================
        (function () {
            window.PJAnime = {
                prefersReduced: window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches,

                init(root) {
                    if (!window.anime) return;
                    if (this.prefersReduced) return;
                    this.counters(root);
                    this.hoverFx(root);
                },

                // ---------- 1. ENTRANCE REVEAL → CSS keyframes (motion.css: [data-animate="hero"], .hero-copy) ----------

                // ---------- 2. SCROLL REVEAL (CSS-first: lihat motion.css + reveal engine di bawah) ----------

                // ---------- 3. ANIMATED COUNTER ----------
                counters(root) {
                    document.querySelectorAll('[data-counter]').forEach((el) => {
                        if (el.dataset.done) return;
                        el.dataset.done = '1';
                        const target = parseFloat(el.dataset.counter) || 0;
                        const suffix = el.dataset.suffix || '';
                        const prefix = el.dataset.prefix || '';
                        const decimals = (el.dataset.counter.includes('.')) ? (el.dataset.counter.split('.')[1] || '').length : 0;
                        const io = new IntersectionObserver((entries) => {
                            entries.forEach((entry) => {
                                if (entry.isIntersecting) {
                                    const obj = { val: 0 };
                                    anime({
                                        targets: obj,
                                        val: target,
                                        round: 1,
                                        easing: 'easeOutExpo',
                                        duration: 1600,
                                        update() {
                                            el.textContent = prefix + obj.val.toLocaleString('id-ID', {
                                                minimumFractionDigits: decimals,
                                                maximumFractionDigits: decimals
                                            }) + suffix;
                                        }
                                    });
                                    io.unobserve(el);
                                }
                            });
                        }, { threshold: 0.4 });
                        io.observe(el);
                    });
                },

                // ---------- 4. HOVER MICRO-INTERACTION ----------
                hoverFx(root) {
                    // 4a. Icon wiggle on card/button hover
                    document.querySelectorAll('[data-hover="wiggle"]').forEach((el) => {
                        el.addEventListener('mouseenter', () => {
                            anime({ targets: el, scale: [1, 1.15], rotate: [0, 8], duration: 300, easing: 'easeOutQuad' });
                        });
                        el.addEventListener('mouseleave', () => {
                            anime({ targets: el, scale: 1, rotate: 0, duration: 400, easing: 'easeInOutQuad' });
                        });
                    });

                    // 4b. Arrow slide on group hover
                    document.querySelectorAll('[data-hover="arrow"]').forEach((group) => {
                        const arrow = group.querySelector('[data-arrow]');
                        if (!arrow) return;
                        group.addEventListener('mouseenter', () => {
                            anime({ targets: arrow, translateX: 6, duration: 250, easing: 'easeOutQuad' });
                        });
                        group.addEventListener('mouseleave', () => {
                            anime({ targets: arrow, translateX: 0, duration: 250, easing: 'easeInOutQuad' });
                        });
                    });

                    // 4c. Floating CTA subtle pulse
                    document.querySelectorAll('[data-hover="pulse"]').forEach((el) => {
                        const tween = anime({
                            targets: el, scale: [1, 1.03], duration: 1200, loop: true,
                            direction: 'alternate', easing: 'easeInOutSine'
                        });
                        el.addEventListener('mouseenter', () => tween.pause());
                        el.addEventListener('mouseleave', () => tween.play());
                    });
                }
            };
        })();

        document.addEventListener('DOMContentLoaded', () => {
            window.PJAnime && window.PJAnime.init(document);
        });

        // ================= SCROLL REVEAL ENGINE (CSS-first: motion.css) =================
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
            // afterSettle: elt bisa berupa <title> (swap OOB head) → selalu scan document,
            // selector :not(.is-in) membuatnya idempoten & murah.
            document.addEventListener('htmx:afterSettle', () => revealAll(document));
        })();

        // ================= HERO PARALLAX (pointer-driven, delegated) =================
        (function () {
            const reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            const fine = window.matchMedia && window.matchMedia('(hover: hover) and (pointer: fine)').matches;
            if (reduce || !fine) return;

            let raf = 0;
            let target = null;

            function apply() {
                raf = 0;
                if (!target) return;
                target.style.transform = 'translate3d(' + target._tx + 'px,' + target._ty + 'px,0)';
            }

            document.addEventListener('mousemove', (e) => {
                const hero = e.target && e.target.closest ? e.target.closest('[data-animate="hero"]') : null;
                if (!hero) return;
                const fig = hero.querySelector('[data-parallax]');
                if (!fig) return;
                const r = hero.getBoundingClientRect();
                fig._tx = ((e.clientX - r.left) / r.width - 0.5) * 14;
                fig._ty = ((e.clientY - r.top) / r.height - 0.5) * 14;
                target = fig;
                if (!raf) raf = requestAnimationFrame(apply);
            }, { passive: true });

            document.addEventListener('mouseout', (e) => {
                const hero = e.target && e.target.closest ? e.target.closest('[data-animate="hero"]') : null;
                if (!hero || hero.contains(e.relatedTarget)) return;
                const fig = hero.querySelector('[data-parallax]');
                if (fig) fig.style.transform = '';
            }, { passive: true });
        })();

        // ================= ALPINE GLOBAL COMPONENTS =================
        window.autoToc = function () {
            return {
                activeId: '',
                init() {
                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach((entry) => {
                            if (entry.isIntersecting) this.activeId = entry.target.id;
                        });
                    }, { rootMargin: '-10% 0px -60% 0px' });
                    document.querySelectorAll('h2[id], h3[id]').forEach((h) => observer.observe(h));
                }
            }
        };

        // Product detail gallery (race-free: reads dataset.gallery safely via $el)
        window.productGallery = function (el) {
            let imgs = [];
            if (el && el.dataset && el.dataset.gallery) {
                try { imgs = JSON.parse(el.dataset.gallery); } catch (e) { imgs = []; }
            }
            if (!imgs.length && Array.isArray(el)) {
                imgs = el;
            }
            return {
                galleryImages: imgs.filter(Boolean),
                active: 0,
                openLightbox(idx) {
                    const items = document.querySelectorAll('.glightbox[data-gallery="product-gallery"]');
                    const target = items[idx ?? this.active] || items[0];
                    if (target) target.click();
                },
                prev() {
                    const n = this.galleryImages.length;
                    if (n > 0) this.active = (this.active - 1 + n) % n;
                },
                next() {
                    const n = this.galleryImages.length;
                    if (n > 0) this.active = (this.active + 1) % n;
                }
            };
        };

        // Product order summary card (race-free: reads data-* attributes)
        window.orderSummary = function () {
            const el = this.$el || {};
            const d = el.dataset || {};
            const basePrice = parseFloat(d.basePrice) || 0;
            const waBase = d.waBase || '#';
            const defaultMode = d.mode || 'ready';
            return {
                basePrice,
                qty: 1,
                fastShip: false,
                mode: defaultMode,
                get total() {
                    return this.basePrice * this.qty + (this.fastShip ? 30000 : 0);
                },
                get ctaUrl() {
                    const sep = waBase.indexOf('?') !== -1 ? '&' : '?';
                    const count = encodeURIComponent('Kuantitas: ' + this.qty + ' pcs' + (this.fastShip ? ' | Express' : ''));
                    return waBase + sep + 'text=' + count;
                }
            };
        };

        document.addEventListener('htmx:configRequest', (evt) => {
            // Include CSRF Token in HTMX headers
            const csrfHeader = 'X-CSRF-TOKEN';
            const csrfMeta = document.querySelector('meta[name="X-CSRF-TOKEN"]');
            if (csrfMeta) {
                evt.detail.headers[csrfHeader] = csrfMeta.content;
            }
            // Show top progress bar
            const pBar = document.getElementById('htmx-progress-bar');
            if (pBar) {
                pBar.style.width = '30%';
                pBar.style.opacity = '1';
            }
        });

        document.addEventListener('htmx:beforeSwap', (evt) => {
            const pBar = document.getElementById('htmx-progress-bar');
            if (pBar) pBar.style.width = '75%';
        });

        // Reset posisi scroll ke paling atas setiap navigasi SPA halaman penuh (hx-boost).
        // htmx men-scroll elts[0] dari settle info, sedangkan elts[0] di sini adalah elemen
        // hasil OOB swap di <head> (<title>/<meta>) yang tidak punya layout box, sehingga
        // tidak pernah menggulir & posisi scroll tertinggal dari halaman sebelumnya.
        function scrollPageToTop() {
            const root = document.documentElement;
            const prevBehavior = root.style.scrollBehavior;
            root.style.scrollBehavior = 'auto'; // lewati .scroll-smooth agar lompat langsung
            window.scrollTo(0, 0);
            root.style.scrollBehavior = prevBehavior;
        }

        document.addEventListener('htmx:afterSwap', (evt) => {
            const detail = evt.detail || {};
            const cfg = detail.requestConfig || {};
            const target = cfg.target || detail.target;
            const boosted = detail.boosted || cfg.boosted;
            // Hanya navigasi halaman penuh (boost) yang mengganti #main-content.
            // Swap fragment (komentar, FAQ, hasil pencarian) tidak digulir.
            if (boosted && target && (target.id === 'main-content' || target.tagName === 'BODY')) {
                scrollPageToTop();
            }
        });

        function initSwipers(root) {
            if (typeof Swiper === 'undefined') return;
            (root || document).querySelectorAll('.swiper-container-init').forEach(el => {
                if (el.swiper) { el.swiper.destroy(true, true); delete el.swiper; }
                const slides = el.querySelectorAll('.swiper-slide').length;
                const perView = window.innerWidth >= 1024 ? 3 : window.innerWidth >= 640 ? 2 : 1;
                const canLoop = slides >= perView * 2;
                new Swiper(el, {
                    slidesPerView: 1,
                    spaceBetween: 20,
                    loop: canLoop,
                    autoplay: canLoop ? { delay: 4000, disableOnInteraction: false } : false,
                    breakpoints: {
                        640: { slidesPerView: 2 },
                        1024: { slidesPerView: 3 }
                    }
                });
            });
        }

        let activeGlightbox = null;
        function initGlightbox(root) {
            if (typeof GLightbox === 'undefined') return;
            if (activeGlightbox && typeof activeGlightbox.destroy === 'function') {
                try { activeGlightbox.destroy(); } catch (e) {}
            }
            activeGlightbox = GLightbox({
                selector: '.glightbox',
                touchNavigation: true,
                loop: true,
                zoomable: true,
                openEffect: 'zoom',
                closeEffect: 'zoom'
            });
            window.activeGlightbox = activeGlightbox;
        }

        function updateNavbarActive() {
            const currentPath = window.location.pathname.replace(/\/+$/, '') || '/';
            document.querySelectorAll('header nav a[href]').forEach(a => {
                const href = a.getAttribute('href').replace(/\/+$/, '') || '/';
                const isActive = (href === '/' && currentPath === '/') || (href !== '/' && currentPath.startsWith(href));
                if (isActive) {
                    a.setAttribute('aria-current', 'page');
                    a.classList.add('font-semibold', 'text-ink');
                    a.classList.remove('text-muted');
                } else {
                    a.removeAttribute('aria-current');
                    a.classList.remove('font-semibold', 'text-ink');
                    a.classList.add('text-muted');
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            initSwipers(document);
            initGlightbox(document);
            updateNavbarActive();
        });

        window.addEventListener('popstate', updateNavbarActive);

        document.addEventListener('htmx:afterSettle', (evt) => {
            const pBar = document.getElementById('htmx-progress-bar');
            if (pBar) {
                pBar.style.width = '100%';
                setTimeout(() => { pBar.style.opacity = '0'; pBar.style.width = '0%'; }, 200);
            }

            // Sync navbar active state on SPA page changes
            updateNavbarActive();

            // Re-init animations after SPA navigation
            window.PJAnime && window.PJAnime.init(evt.detail.elt || document.body);

            // Re-init Alpine.js components (TOC, FAQ, comments reply)
            if (window.Alpine) {
                Alpine.initTree(evt.detail.elt || document.body);
            }

            initGlightbox(evt.detail.elt);
            initSwipers(evt.detail.elt);
        });
    </script>
</body>
</html>
