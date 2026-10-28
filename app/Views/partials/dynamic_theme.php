<?php
$brand = $settings['brand'] ?? [];
$theme = $settings['theme'] ?? [];

$primary   = esc($theme['color_primary'] ?? '#0284c7');
$secondary = esc($theme['color_secondary'] ?? '#0f172a');
$accent    = esc($theme['color_accent'] ?? '#f59e0b');
$ctaWa     = esc($theme['cta_wa'] ?? '#22c55e');

// Light mode
$bgLight   = esc($theme['bg_light'] ?? '#ffffff');
$surfLight = esc($theme['surface_light'] ?? '#ffffff');
$saLight   = esc($theme['surface_alt_light'] ?? '#f1f5f9');
$textLight = esc($theme['text_light'] ?? '#0f172a');
$tmLight   = esc($theme['text_muted_light'] ?? '#475569');
$tfLight   = esc($theme['text_faint_light'] ?? '#64748b');
$bdLight   = esc($theme['border_light'] ?? '#e2e8f0');

// Dark mode
$bgDark    = esc($theme['bg_dark'] ?? '#0b0f19');
$surfDark  = esc($theme['surface_dark'] ?? '#1e293b');
$saDark    = esc($theme['surface_alt_dark'] ?? '#111827');
$textDark  = esc($theme['text_dark'] ?? '#f8fafc');
$tmDark    = esc($theme['text_muted_dark'] ?? '#cbd5e1');
$tfDark    = esc($theme['text_faint_dark'] ?? '#64748b');
$bdDark    = esc($theme['border_dark'] ?? '#334155');

$fontBody    = esc($theme['font_body'] ?? 'Inter');
$fontHeading = esc($theme['font_heading'] ?? 'Outfit');
$googleFont  = esc($theme['font_google_import'] ?? 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap');
?>
<!-- Google Fonts -->
<?php if (!empty($googleFont)): ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="<?= $googleFont ?>">
<?php endif; ?>

<!-- Anti-FOUC Script for Dark Mode -->
<script>
    (function() {
        const savedMode = localStorage.getItem('theme_mode') || 'auto';
        const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const dark = savedMode === 'dark' || (savedMode !== 'light' && systemDark);
        document.documentElement.classList.toggle('dark', dark);

        // Mode Auto: ikuti perubahan preferensi sistem secara live.
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function (e) {
            if ((localStorage.getItem('theme_mode') || 'auto') !== 'auto') return;
            document.documentElement.classList.toggle('dark', e.matches);
        });
    })();

    window.themeSwitcher = function () {
        return {
            open: false,
            mode: localStorage.getItem('theme_mode') || 'auto',
            get isDark() { return document.documentElement.classList.contains('dark'); },
            setMode(id) {
                this.mode = id;
                localStorage.setItem('theme_mode', id);
                const dark = id === 'dark' || (id !== 'light' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                document.documentElement.classList.toggle('dark', dark);
                this.open = false;
            }
        };
    };
</script>

<style id="dynamic-theme">
    :root {
        --color-primary: <?= $primary ?>;
        --color-secondary: <?= $secondary ?>;
        --color-accent: <?= $accent ?>;
        --color-cta-wa: <?= $ctaWa ?>;
        --color-bg: <?= $bgLight ?>;
        --color-surface: <?= $surfLight ?>;
        --color-surface-alt: <?= $saLight ?>;
        --color-text: <?= $textLight ?>;
        --color-text-muted: <?= $tmLight ?>;
        --color-text-faint: <?= $tfLight ?>;
        --color-border: <?= $bdLight ?>;
        --font-body: '<?= $fontBody ?>', sans-serif;
        --font-heading: '<?= $fontHeading ?>', sans-serif;
        --ease-out-soft: cubic-bezier(0.22, 1, 0.36, 1);
        --duration-fast: 150ms;
        --duration-base: 220ms;
    }
    .dark {
        --color-bg: <?= $bgDark ?>;
        --color-surface: <?= $surfDark ?>;
        --color-surface-alt: <?= $saDark ?>;
        --color-text: <?= $textDark ?>;
        --color-text-muted: <?= $tmDark ?>;
        --color-text-faint: <?= $tfDark ?>;
        --color-border: <?= $bdDark ?>;
    }
    /* Catatan: rule body/h1 dasar (bg, warna teks, font smoothing) sudah ditangani main.css.
       Partial ini HANYA mendefinisikan CSS variables token agar tidak ada duplikasi/race antar stylesheet. */
</style>