# pusatjersey.com — BRD & Blueprint Teknis (SPA HTMX Edition)

| Item | Detail |
|---|---|
| **Nama** | Pusat Jersey — Custom Jersey, Katalog & Promosi Multi-Channel |
| **Domain** | pusatjersey.com |
| **Stack** | PHP CodeIgniter 4 · SQLite (dev) / MySQL (prod) · Tailwind CSS (compiled) · HTMX SPA (`hx-boost` & OOB Swaps) + Alpine.js · Dynamic Theme (CSS Variables) · Swiper.js · GLightbox |
| **Versi** | v1.4 · Dynamic Brand, Theme, Price Calculator, Lucide Icon System, Auto TOC, Social Share & Comments System |
| **Isi** | A. BRD · B. Arsitektur Teknis SPA & Dynamic Theme · C. Database · D. Routing & HTMX Standard · E. SEO & Structured Data Strategy · F. Page Builder · G. Roadmap & Status |

> **Target performa:** Soft Navigation (SPA) < 150ms · LCP < 2.5s · CLS < 0.1 · INP < 100ms — Dual-rendering (Full SSR untuk bot/first load, Partial OOB Swaps untuk HTMX SPA).

---

## PART A — BUSINESS REQUIREMENT DOCUMENT

### A1. Ringkasan Eksekutif

Satu website = **hub katalog + pusat promosi** berteknologi **Single Page Application (SPA) berbasis HTMX** yang mengubah traffic Google (organic, Shopping Tab, AI Overview) menjadi **4 saluran konversi**: WhatsApp, TikTok Shop, Shopee, Tokopedia.

**Model Pendapatan**
1. **Jasa custom jersey** → CTA ke WhatsApp (template pesan otomatis + UTM source + kalkulator estimasi harga).
2. **Jersey original** → produk marketplace (Shopee/Tokopedia/TikTok Shop) dengan tombol beli langsung.
3. **Affiliate (produk brand lain)** → link + kode affiliate otomatis di tiap tombol marketplace. *(live di Fase 3)*

**Keunggulan Kompetitif**
- **Sensasi SPA Cepat (Zero Page Reload)**: Navigasi antar halaman (katalog, blog, layanan, portofolio, tentang kami, kontak) terjadi instan tanpa kedipan/reload halaman penuh menggunakan **HTMX SPA Engine**.
- **SEO 100% Sempurna (Full SSR First Load)**: GoogleBot dan crawler AI tetap menerima dokumen HTML utuh (SSR) beserta meta tag dan structured data (JSON-LD) lengkap yang tergenerasi secara dinamis dari **Detail Profil Brand**.
- **Kalkulator & Estimator Harga Instant**: Calon pembeli jersey tim/custom dapat mensimulasikan harga berdasarkan jumlah stel, jenis bahan, tipe cetak, dan paket add-on secara realtime, lalu langsung mengirimkan pesanan terformat rapi ke WhatsApp.
- **Live Instant Search Overlay (`Ctrl+K`)**: Modal pencarian HTMX auto-suggest cepat yang mengelompokkan hasil pencarian produk, layanan kota, artikel blog, dan portofolio secara realtime saat pengguna mengetik.
- **Dark / Light Mode Switcher**: Dukungan mode terang dan gelap interaktif yang tersimpan di `localStorage` dan terintegrasi mulus dengan CSS Variables dinamis tanpa kedipan (zero FOUC).
- **Profil Brand & Tema Dinamis**: Pengaturan tersentralisasi di Admin Dashboard untuk mengelola identitas brand (logo, tagline, legal, visi/misi, alamat, jam operasional, kontak CS), **palet warna dinamis** (`:root` CSS variables), dan **tipografi dinamis** (Google Fonts).
- **Lokasi Dinamis**: 1 layanan → N halaman kota otomatis (`pusat-jersey-custom` → `...-jakarta`, `...-surabaya`) dari master data, tanpa konten duplikat.
- **Structured Data Maksimal**: Product, LocalBusiness, Organization, AboutPage, ContactPage, Service, FAQPage, BreadcrumbList — siap untuk AI Overview & Google Shopping.
- **CTA Multi-Channel Terukur**: Tiap klik membawa nama produk + link situs + sumber brand + UTM & affiliate tracking.

### A2. Tujuan Bisnis & KPI

| # | Tujuan | KPI 3 Bulan |
|---|---|---|
| 1 | Pengalaman Pengguna (UX SPA & Branding) | Navigasi SPA instan (Soft Navigation < 150ms), tema warna & tipografi konsisten |
| 2 | Flexibilitas Branding & Pengaturan | Ubah profil brand, warna, font, tentang & kontak dari admin ≤ 5 menit tanpa edit kode |
| 3 | Konversi High-Intent (Kalkulator Harga) | Rasio pengunjung ke WA via kalkulator harga ≥ 12% dari total visitor halaman layanan |
| 4 | Eksplorasi Konten (Live Search & Dark Mode) | Waktu rata-rata di situs (Dwell Time) meningkat ≥ 35% |
| 5 | Menang di Google Shopping & AI Overview | ≥80% produk punya Product schema valid & Organization/LocalBusiness valid |
| 6 | Traffic Organik + Lokal | ≥100 halaman layanan×lokasi terindeks; ≥500 klik organik/bln |
| 7 | Konversi Multi-Channel | 10–15 order custom/minggu (WA); CTR tombol marketplace ≥5% |
| 8 | Core Web Vitals Hijau | LCP <2.5s, CLS <0.1, INP <100ms |

### A3. Persona & Alur Utama

| Persona | Kebutuhan | Alur |
|---|---|---|
| Buyer custom (individu/tim/komunitas) | Harga, estimasi budget, portofolio, profil bisnis, jasa di kotanya | Google → `/layanan/{layanan}-{kota}` (SSR) → Gunakan **Kalkulator Harga** → WhatsApp |
| Player / kolektor | Cari jersey ori cepat & alamat fisik | Google → Gunakan **Instant Search (`Ctrl+K`)** / `/produk/{slug}` → Cek `/kontak` → Shopee/Tokopedia/TikTok Shop |
| Pembeli bulk (kejurnas/instansi) | Penawaran khusus, legalitas, estimasi harga & alamat | `/tentang-kami` & `/kontak` → Kalkulator Harga / Form HTMX → WhatsApp |
| Admin | Kelola profil brand, tema warna/font, harga kalkulator, konten, produk, lokasi, FAQ, builder | `/admin/*` (HTMX Admin Dashboard — Pengaturan Profil Brand, Tema & CRUD tanpa reload) |

### A4. Fitur Wajib & Kriteria Terima (Done)

| Modul | Fitur | Kriteria Terima |
|---|---|---|
| **Profil Brand Dinamis** | Identitas Brand, Legal, Alamat, Operasional, Medsos | Mengatur nama brand, legal, logo, favicon, email, telp, WA, alamat, maps embed, jam buka & medsos di `settings`; langsung terhubung ke footer, header, & JSON-LD |
| **Dynamic About & Contact** | Halaman `/tentang-kami` dan `/kontak` | Konten halaman di-generate otomatis dari `settings`; Form Kontak HTMX dengan respon toast instant; JSON-LD `AboutPage` & `ContactPage` valid |
| **Palet Warna & Tipografi Dinamis** | Design Tokens via CSS Variables | Pilihan warna (Primary, Secondary, Accent, Background, Surface, CTA) & Google Fonts diatur via Admin; di-inject via `:root` CSS variables di `<head>` |
| **Kalkulator Harga Custom** | Estimator Harga Custom Jersey | Hitung estimasi harga realtime (jumlah stel, bahan, tipe print, paket) & tombol CTA WA dengan pesan terformat rapi; terintegrasi di halaman layanan & page builder |
| **Live Instant Search Overlay** | Search Auto-Suggest (`Ctrl+K`) | Modal pencarian HTMX instant; mengelompokkan hasil (Produk, Layanan Kota, Blog, Portofolio) dengan `hx-trigger="keyup changed delay:250ms"` |
| **Dark / Light Mode Switcher** | Toggle Mode Tampilan | Sakelar mode terang/gelap tersimpan di `localStorage` & cookie; mengatur class `.dark` pada HTML tanpa kedipan (zero FOUC) |
| **Pustaka Ikon Profesional** | Lucide SVG Icon System | Menggantikan emoji dengan SVG icons seragam dari Lucide Icons (dinamis sesuai warna Tailwind); terintegrasi di seluruh UI dan komponen builder |
| **Auto TOC (Daftar Isi Otomatis)** | Auto Table of Contents Artikel | Ekstraksi tag `<h2>` & `<h3>` otomatis pada halaman artikel blog/layanan; sticky sidebar dengan highlight navigasi scroll interaktif |
| **Tombol Share Media Sosial** | Social Share Widget | Tombol bagikan artikel/produk ke WhatsApp, Facebook, X (Twitter), LinkedIn, Telegram, & Copy Link + dukungan Web Share API native mobile |
| **Sistem Komentar Artikel** | Blog Comments System (HTMX) | Form komentar bertingkat (parent-child reply); pengiriman via HTMX dengan toast respon instant; moderasi admin (`/admin/comments`) |
| **HTMX SPA Engine** | Seamless Page Transition, OOB Swap (Title, Meta, JSON-LD, Active Nav) | URL browser berubah via History API; meta & title ter-update otomatis; JS library (Swiper, Lightbox) re-init otomatis |
| **Konten** | Blog (kategori+tag), Layanan, Produk, Portofolio | CRUD HTMX; SEO fields; status draft/publish; canonical & JSON-LD OOB ready |
| **Lokasi dinamis** | Master lokasi → auto-render `{service}-{kota}` | Halaman muncul tanpa ubah kode; canonical & 404 benar; load via SPA & SSR |
| **CTA multi-channel** | Tombol → WA / TikTok / Shopee / Tokopedia | Pesan berisi: nama produk + URL situs + sumber + UTM/kode affiliate (`hx-boost="false"` untuk CTA 302) |
| **FAQ** | Per post type (blog/service/product/portfolio) | Kelola 1 tempat; Accordion HTMX/Alpine; JSON-LD FAQPage valid |
| **Page Builder** | Komponen modular + preset | Preview HTMX realtime; layout home tersimpan; preset sekali-klik (termasuk komponen Kalkulator) |
| **Media** | Library, alt, caption, WebP, lazy, lightbox | Modal upload HTMX; WebP auto; lazy load + Lightbox re-init di SPA transition |
| **SEO** | Schema + sitemap + feed | Full SSR pada first-load/crawler; OOB JSON-LD pada HTMX request; sitemap GSC valid |

### A5. Kebutuhan Non-Fungsional

- **Arsitektur HTMX SPA:** SSR penuh untuk request direct/crawler; In-App Navigation menggunakan HTMX (`hx-boost="true"` + OOB Swaps).
- **Dynamic CSS & Theme Injection:** Pengaturan tema warna, tipografi, dan mode tampilan (light/dark) di-inject dalam tag `<style id="dynamic-theme">` di `<head>` untuk mencegah FOUC (Flash of Unstyled Content).
- **Performa:** JS minimal (HTMX engine ~14KB, Alpine.js UI state ~15KB); Tailwind CSS compiled & minified; WebP + lazy load; Font subset.
- **Keamanan:** Auth admin (session + CSRF token yang otomatis diperbarui via HTMX header `X-CSRF-TOKEN`); query builder prepared statement; upload filter.
- **Data:** SQLite untuk dev; migrasi via `spark migrate` (CI4) → siap pindah ke MySQL/Postgres.

---

## PART B — ARSITEKTUR TEKNIS SPA HTMX & DYNAMIC THEME

### B1. Prinsip Arsitektur SPA HTMX & Dynamic Theme

1. **Dual-Mode Rendering (SSR + HTMX SPA)**:
   - **Request Langsung / Crawler Bot**: Controller mengembalikan layout HTML lengkap (Dokumen Utuh: `<head>`, `Navbar`, `#main-content`, `Footer`, `JSON-LD`).
   - **Navigasi HTMX (In-App)**: Controller mendeteksi header `HX-Request: true`, hanya mengembalikan fragment `#main-content` beserta **Out-of-Band (OOB) Swaps** (`hx-swap-oob="true"`) untuk tag `<title>`, `<meta>`, JSON-LD `<script>`, dan navigasi aktif.
2. **Dynamic Theme Engine (CSS Custom Properties)**:
   - `settings` menyimpan token warna (`theme_color_*`) dan font (`theme_font_*`).
   - Partial `Views/partials/dynamic_theme.php` meng-inject CSS variables `:root` di `<head>`.
3. **Price Calculator Engine**:
   - Algoritma tiering kuantitas (misal: 6–11 pcs, 12–24 pcs, 25–49 pcs, 50–99 pcs, ≥100 pcs) + koefisien bahan & cetak yang fleksibel diatur dari `settings` admin.
4. **Live Instant Search Overlay Engine**:
   - Request ringan `GET /search/suggest?q=...` via HTMX returning HTML fragment kategori hasil pencarian dengan `hx-target="#search-results"`.
5. **Dark / Light Mode Engine**:
   - Skema CSS Variables `:root` (light) dan `.dark` (dark) yang di-switch secara dinamis oleh JavaScript inline di `<head>` (mencegah kedipan FOUC).
6. **Seamless History Management**: URL browser diperbarui otomatis tanpa page reload penuh menggunakan `hx-push-url="true"` atau HTMX Boost.
7. **Automatic Re-initialization Hooks**: Event `htmx:afterSettle` digunakan untuk re-init komponen UI dinamis (Swiper slider, GLightbox, Alpine.js, GA4 pageview tracking).

### B2. Alur Data & Lifecycle Request HTMX SPA

```text
               ┌─────────────────────────────────────────┐
               │         Browser / User Interaction       │
               └────────────────────┬────────────────────┘
                                    │
                  ┌─────────────────┴─────────────────┐
                  │ Request URL (mis: /tentang-kami)  │
                  └─────────────────┬─────────────────┘
                                    │
                         Is HTMX Request? (HX-Request)
                        /                         \
                   [NO] /                           \ [YES]
                       /                             \
    ┌─────────────────────────┐               ┌─────────────────────────┐
    │  Direct / Bot / Refresh │               │ In-App HTMX Navigation  │
    └────────────┬────────────┘               └────────────┬────────────┘
                 │                                         │
    ┌────────────▼────────────┐               ┌────────────▼────────────┐
    │ CI4 Controller (SSR)    │               │ CI4 Controller (HTMX)   │
    │ Fetch Brand Settings &  │               │ Fetch Brand Settings &  │
    │ Theme Data              │               │ Theme Data              │
    └────────────┬────────────┘               └────────────┬────────────┘
                 │                                         │
    ┌────────────▼────────────┐               ┌────────────▼────────────┐
    │ Render Layout Complete  │               │ Render View Partial     │
    │ (Head, Body, Main, Foot)│               │ + OOB SEO Swaps         │
    └────────────┬────────────┘               └────────────┬────────────┘
                 │                                         │
                 └────────────────────┬────────────────────┘
                                      │
                       ┌──────────────▼──────────────┐
                       │   Response (HTML Text)      │
                       └──────────────┬──────────────┘
                                      │
                       ┌──────────────▼──────────────┐
                       │ Browser DOM Swap            │
                       │ Fire: htmx:afterSettle      │
                       │ Re-init Swiper/GLightbox/   │
                       │ Alpine/Dark Mode/GA4        │
                       └─────────────────────────────┘
```

### B3. Spesifikasi Partial Dynamic Theme (`Views/partials/dynamic_theme.php`)

```php
<?php
// Default Tokens
$primary   = esc($settings['theme_color_primary'] ?? '#0284c7');
$secondary = esc($settings['theme_color_secondary'] ?? '#0f172a');
$accent    = esc($settings['theme_color_accent'] ?? '#f59e0b');
$bgLight   = esc($settings['theme_color_bg_light'] ?? '#ffffff');
$surfLight = esc($settings['theme_color_surface_light'] ?? '#f8fafc');
$textLight = esc($settings['theme_color_text_light'] ?? '#0f172a');
$bgDark    = esc($settings['theme_color_bg_dark'] ?? '#0b0f19');
$surfDark  = esc($settings['theme_color_surface_dark'] ?? '#1e293b');
$textDark  = esc($settings['theme_color_text_dark'] ?? '#f8fafc');
$ctaWa     = esc($settings['theme_color_cta_wa'] ?? '#22c55e');

$fontBody    = esc($settings['theme_font_body'] ?? 'Inter');
$fontHeading = esc($settings['theme_font_heading'] ?? 'Outfit');
$googleFont  = esc($settings['theme_font_google_import'] ?? 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap');
?>

<!-- Google Fonts Import -->
<?php if (!empty($googleFont)): ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="<?= $googleFont ?>">
<?php endif; ?>

<!-- Dynamic CSS Variables & Dark Mode Script (Prevent FOUC) -->
<script>
    (function() {
        const savedMode = localStorage.getItem('theme_mode');
        const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        if (savedMode === 'dark' || (!savedMode && systemDark)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    })();
</script>

<style id="dynamic-theme">
    :root {
        --color-primary: <?= $primary ?>;
        --color-secondary: <?= $secondary ?>;
        --color-accent: <?= $accent ?>;
        --color-cta-wa: <?= $ctaWa ?>;
        
        /* Light Mode Defaults */
        --color-bg: <?= $bgLight ?>;
        --color-surface: <?= $surfLight ?>;
        --color-text: <?= $textLight ?>;
        
        --font-body: '<?= $fontBody ?>', sans-serif;
        --font-heading: '<?= $fontHeading ?>', sans-serif;
    }
    
    .dark {
        --color-bg: <?= $bgDark ?>;
        --color-surface: <?= $surfDark ?>;
        --color-text: <?= $textDark ?>;
    }
    
    body {
        background-color: var(--color-bg);
        color: var(--color-text);
        font-family: var(--font-body);
    }
    
    h1, h2, h3, h4, h5, h6, .font-heading {
        font-family: var(--font-heading);
    }
    
    .btn-primary {
        background-color: var(--color-primary);
        color: #ffffff;
    }
    .btn-cta-wa {
        background-color: var(--color-cta-wa);
        color: #ffffff;
    }
</style>
```

### B4. Spesifikasi Kalkulator Harga Custom (`Views/partials/price_calculator.php`)

```html
<!-- Interactive Calculator Component (Alpine.js + HTMX) -->
<div x-data="priceCalculator()" class="p-6 md:p-8 bg-surface rounded-2xl border border-slate-700/50 shadow-xl">
    <h3 class="text-2xl font-bold font-heading text-white mb-2">🧮 Simulasi Estimasi Harga Custom Jersey</h3>
    <p class="text-slate-400 text-sm mb-6">Hitung estimasi biayamu secara instan dan kirim langsung ke WhatsApp admin untuk pemesanan.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Input Form -->
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Jumlah Pemesanan (Stel)</label>
                <div class="flex items-center space-x-3">
                    <input type="range" min="6" max="200" x-model.number="quantity" class="w-full accent-primary">
                    <input type="number" min="6" x-model.number="quantity" class="w-20 px-3 py-1.5 bg-slate-800 border border-slate-700 rounded text-center font-bold text-white">
                </div>
                <span class="text-[11px] text-amber-400" x-text="tierDiscountText"></span>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Bahan Kain</label>
                <select x-model="material" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded text-slate-200">
                    <option value="drifit_milano">Drifit Milano (Paling Populer & Adem)</option>
                    <option value="drifit_nike">Drifit Nike (Tekstur Bintik Halus)</option>
                    <option value="serena">Serena (Licin & Elastis)</option>
                    <option value="emboss">Drifit Emboss (Tekstur 3D Premium)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Tipe Cetak / Printing</label>
                <select x-model="printType" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded text-slate-200">
                    <option value="full_sublim">Full Sublimasi (Depan, Belakang, Lengan)</option>
                    <option value="front_only">Sublim Depan Saja (Badan Belakang Polos)</option>
                    <option value="polyflex">Kombinasi Polyflex / Sablon</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Paket Produk</label>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" @click="packageType = 'baju_celana'" :class="packageType === 'baju_celana' ? 'border-primary bg-primary/10 text-primary' : 'border-slate-700 bg-slate-800 text-slate-400'" class="p-2 border rounded text-xs font-semibold">Stel (Baju + Celana)</button>
                    <button type="button" @click="packageType = 'baju_saja'" :class="packageType === 'baju_saja' ? 'border-primary bg-primary/10 text-primary' : 'border-slate-700 bg-slate-800 text-slate-400'" class="p-2 border rounded text-xs font-semibold">Atasan / Baju Saja</button>
                </div>
            </div>
        </div>

        <!-- Result Box -->
        <div class="flex flex-col justify-between p-6 bg-slate-950/60 rounded-xl border border-slate-800">
            <div>
                <span class="text-xs text-slate-400 font-medium">Estimasi Harga Per Stel</span>
                <div class="text-3xl font-extrabold text-amber-400 my-1" x-text="formatRupiah(unitPrice)"></div>
                <div class="text-sm text-slate-300 mt-4">Total Estimasi (<span x-text="quantity"></span> stel):</div>
                <div class="text-2xl font-bold text-white" x-text="formatRupiah(totalPrice)"></div>
            </div>

            <a :href="whatsappUrl" target="_blank" hx-boost="false" class="mt-6 w-full inline-flex items-center justify-center space-x-2 py-3 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow-lg transition">
                <span>💬 Pesan via WhatsApp Sekarang</span>
            </a>
        </div>
    </div>
</div>
```

### B5. Spesifikasi Live Instant Search Overlay (`Views/partials/search_overlay.php`)

- **Trigger Keyboard**: Tombol `Ctrl+K` / `Cmd+K` atau ikon pencarian navbar.
- **Endpoint HTMX**: `GET /search/suggest?q={query}`
- **Hasil Terbagi 4 Tab / Kategori**:
  1. 👕 **Produk Katalog** (Foto, Judul, Harga, Badge Ori/Affiliate)
  2. 📍 **Layanan & Kota** (Layanan Custom + Kota Target)
  3. 📰 **Artikel Blog** (Judul, Kategori, Excerpt)
  4. 🖼️ **Portofolio Desain** (Gambar & Nama Tim/Klien)

### B6. Lucide SVG Icon System & Helper (`app/Helpers/icon_helper.php`)
Menggantikan seluruh emoji dengan Pustaka Ikon Profesional **Lucide Icons** berbentuk vector SVG murni agar tampilan konsisten, tajam di retina display, dan dapat diubah ukurannya/warnanya via kelas CSS Tailwind.

```php
function lucide_icon(string $name, string $class = 'w-5 h-5', array $attrs = []): string
{
    // Rendet inline SVG dari Lucide Icon Set
    return sprintf(
        '<svg class="%s" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" %s><use href="/assets/icons/lucide-sprite.svg#%s"/></svg>',
        esc($class),
        stringifiy_attrs($attrs),
        esc($name)
    );
}
```

### B7. Auto Table of Contents (Auto TOC) (`Views/partials/auto_toc.php`)
- **Fungsi**: Memindai isi tag `<h2>` dan `<h3>` dari artikel blog atau halaman layanan secara server-side/HTML Parser helper.
- **Output**: Generasi otomatis daftar isi bersarang (nested list) di sticky sidebar dengan link anchor `#heading-slug`.
- **Active Scroll Observer**: Script Alpine.js (`x-data="autoToc()"`) mengamati scroll posisi pengguna (`IntersectionObserver`) dan menyorot judul TOC yang sedang dibaca.

### B8. Tombol Share Media Sosial (`Views/partials/social_share.php`)
- **Fungsi**: Memudahkan pengunjung membagikan halaman artikel atau produk ke jejaring sosial.
- **Channel Target**: WhatsApp, Facebook, X (Twitter), LinkedIn, Telegram, & Copy Link.
- **Native Web Share API Support**: Jika dibuka via perangkat mobile yang mendukung `navigator.share`, tombol "Bagikan" utama memicu dialog share bawaan Android/iOS.

### B9. Sistem Komentar Artikel Blog (HTMX Driven)
- **Komponen**: Form Komentar (`Views/partials/blog_comments.php`) dengan dukungan balasan bertingkat (parent-child reply).
- **HTMX Submission**: Request `POST /blog/(:segment)/comment` dikirim via HTMX, memperbarui daftar komentar atau menampilkan alert toast *"Komentar Anda telah terkirim dan menunggu moderasi admin"*.
- **Admin Moderasi**: Admin mengelola status komentar (`pending`, `approved`, `spam`) via panel dashboard `/admin/comments`.

---

## PART C — SKEMA DATABASE (SQLITE / MYSQL)

```sql
-- TABEL SETTINGS (Multi-purpose Brand, Dynamic Theme & Calculator Settings)
CREATE TABLE settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    key_name VARCHAR(100) UNIQUE NOT NULL,
    key_value TEXT NULL,
    group_name VARCHAR(50) DEFAULT 'general', -- 'brand', 'theme', 'about', 'contact', 'calculator', 'seo'
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- TABEL COMMENTS (Blog & Content Discussions)
CREATE TABLE comments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    post_id INTEGER NOT NULL,
    parent_id INTEGER NULL DEFAULT NULL,
    author_name VARCHAR(100) NOT NULL,
    author_email VARCHAR(100) NOT NULL,
    author_website VARCHAR(255) NULL,
    content TEXT NOT NULL,
    status VARCHAR(20) DEFAULT 'pending', -- 'pending', 'approved', 'spam'
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE
);

-- SEEDER AWAL MATRIKS SETTINGS (Brand, Theme & Calculator Defaults)
INSERT INTO settings (key_name, key_value, group_name) VALUES
-- Identitas Brand
('brand_name', 'Pusat Jersey', 'brand'),
('brand_legal_name', 'PT Pusat Jersey Indonesia', 'brand'),
('brand_tagline', 'Produsen Custom Jersey & Apparel Olahraga No.1', 'brand'),
('brand_logo', '/uploads/logo.png', 'brand'),
('brand_favicon', '/uploads/favicon.ico', 'brand'),
('brand_founding_year', '2018', 'brand'),

-- Kontak & Operasional CS
('contact_phone', '+6281234567890', 'contact'),
('contact_whatsapp', '6281234567890', 'contact'),
('contact_email', 'info@pusatjersey.com', 'contact'),
('contact_address', 'Jl. Raya Jersey No. 88, Kebayoran Baru', 'contact'),
('contact_city', 'Jakarta Selatan', 'contact'),
('contact_maps_embed', '<iframe src="https://www.google.com/maps/embed?..."></iframe>', 'contact'),
('contact_opening_hours_json', '[{"days":"Senin - Sabtu","hours":"08:00 - 17:00 WIB"}]', 'contact'),
('social_links_json', '[{"platform":"Instagram","url":"https://instagram.com/pusatjersey"},{"platform":"TikTok","url":"https://tiktok.com/@pusatjersey"}]', 'contact'),

-- Tentag Kami
('about_title', 'Tentang Pusat Jersey Indonesia', 'about'),
('about_short_desc', 'Spesialis konveksi & cetak custom jersey olahraga berkualitas tinggi dengan teknologi sublimasi terkini.', 'about'),
('about_story_html', '<p>Berdiri sejak 2018, Pusat Jersey telah melayani lebih dari 10.000+ tim olahraga...</p>', 'about'),
('about_vision', 'Menjadi produsen jersey custom pilihan utama di seluruh Indonesia.', 'about'),
('about_mission', 'Memberikan kualitas bahan terbaik, garansi warna tajam, dan ketepatan waktu pengiriman.', 'about'),
('about_stats_json', '[{"value":"10.000+","label":"Tim Dilayani"},{"value":"99.4%","label":"Kepuasan Klien"},{"value":"50+","label":"Kota Jangkauan"}]', 'about'),

-- Palet Warna Dinamis & Tipografi
('theme_color_primary', '#0284c7', 'theme'),
('theme_color_secondary', '#0f172a', 'theme'),
('theme_color_accent', '#f59e0b', 'theme'),
('theme_color_bg_light', '#ffffff', 'theme'),
('theme_color_surface_light', '#f8fafc', 'theme'),
('theme_color_text_light', '#0f172a', 'theme'),
('theme_color_bg_dark', '#0b0f19', 'theme'),
('theme_color_surface_dark', '#1e293b', 'theme'),
('theme_color_text_dark', '#f8fafc', 'theme'),
('theme_color_cta_wa', '#22c55e', 'theme'),
('theme_font_body', 'Inter', 'theme'),
('theme_font_heading', 'Outfit', 'theme'),
('theme_font_google_import', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap', 'theme'),

-- Setting Base Harga Kalkulator Custom
('calc_base_baju_celana', '135000', 'calculator'),
('calc_base_baju_saja', '95000', 'calculator'),
('calc_tier_discounts_json', '[{"min":6,"max":11,"discount":0},{"min":12,"max":24,"discount":5000},{"min":25,"max":49,"discount":10000},{"min":50,"max":99,"discount":15000},{"min":100,"max":9999,"discount":20000}]', 'calculator');
```

---

## PART D — ROUTING PLAN & HTMX ENDPOINTS

```php
// Route Publik HTMX SPA & Calculator & Search
$routes->get('/', 'Home::index');
$routes->get('tentang-kami', 'Page::about');
$routes->get('kontak', 'Page::contact');
$routes->post('kontak/send', 'Page::sendContactForm');

// Calculator & Search Endpoints
$routes->get('search/suggest', 'Search::suggest');
$routes->post('layanan/calculate', 'Service::calculatePrice');

// Katalog & Konten Publik
$routes->get('produk', 'Product::index');
$routes->get('produk/(:segment)', 'Product::detail/$1');
$routes->get('blog', 'Blog::index');
$routes->get('blog/(:segment)', 'Blog::detail/$1');
$routes->post('blog/(:segment)/comment', 'Blog::postComment');
$routes->get('portofolio', 'Portfolio::index');
$routes->get('portofolio/(:segment)', 'Portfolio::detail/$1');
$routes->get('layanan', 'Service::index');
$routes->get('layanan/(:segment)', 'Service::detail/$1');

// Redirect CTA (302) dengan UTM & Affiliate
$routes->get('cta/(:segment)/(:segment)', 'Cta::redirect/$1/$2');

// Admin Panel (HTMX Dashboard)
$routes->group('admin', ['filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'Admin\Dashboard::index');
    $routes->get('comments', 'Admin\Comments::index');
    $routes->post('comments/(:num)/status', 'Admin\Comments::updateStatus/$1');
    $routes->get('settings/brand', 'Admin\Settings::brand');
    $routes->post('settings/brand/update', 'Admin\Settings::updateBrand');
    $routes->get('settings/theme', 'Admin\Settings::theme');
    $routes->post('settings/theme/update', 'Admin\Settings::updateTheme');
    $routes->get('settings/calculator', 'Admin\Settings::calculator');
    $routes->post('settings/calculator/update', 'Admin\Settings::updateCalculator');
});
```

---

## PART E — SEO & STRUCTURED DATA STRATEGY

### E1. Multi-Schema Output Matrix

1. **`Organization` Schema**: Tergenerasi otomatis di setiap halaman dari settings Profil Brand.
2. **`LocalBusiness` Schema**: Menyajikan nama bisnis, alamat, koordinat maps, nomor telepon, dan jam buka kantor pusat.
3. **`AboutPage` & `ContactPage` Schema**: Khusus untuk halaman `/tentang-kami` dan `/kontak`.
4. **`Product` Schema**: Lengkap dengan `Offer`, `Brand`, `priceCurrency: "IDR"`, dan `availability`.
5. **`BreadcrumbList` Schema**: Membantu Google menampilkan breadcrumbs navigasi di SERP.

---

## PART F — PAGE BUILDER SPECIFICATION (MULTI-STYLE & HTMX REALTIME PREVIEW)

### F1. Multi-Layout & Interactive Display Variants
Setiap komponen dalam Page Builder dirancang **Polimorfik / Multi-Style**. Admin dapat memilih opsi tampilan (`layout_style`) secara dinamis tanpa mengubah struktur data. 

Sistem partial view (`Views/components/{key}.php`) akan membaca `$settings['layout_style']` dan secara otomatis mengaplikasikan markup HTML, kelas Tailwind, serta JS wrapper yang sesuai (Swiper.js untuk Slider/Carousel, Masonry.js/CSS Grid, Alpine.js untuk Tabs/Accordion).

### F2. Admin Realtime Swap Workflow
- Admin mengedit section di Admin Panel (`/admin/page-builder`).
- Opsi `layout_style` (misal: ubah tampilan produk dari `grid` menjadi `slider`) memicu request `POST /admin/page-builder/preview-section` via HTMX.
- Server mengembalikan partial HTML section terbaru sesuai variasi gaya yang dipilih dan langsung me-replace canvas preview di admin dashboard tanpa refresh seluruh halaman.

### F3. Matriks Komponen & Variasi Layout Interaktif

| Key Komponen | Deskripsi | Opsi Variasi Layout (`layout_style`) | Props Utama (`settings_json`) |
|---|---|---|---|
| `hero` | Hero Banner Utama | • `split_image_right` (Teks kiri, Gambar/Mockup kanan)<br>• `fullscreen_bg` (Background gambar full + overlay)<br>• `centered_minimal` (Teks tengah minimalis)<br>• `video_bg` (Background video loop) | `title, subtitle, eyebrow, bg_image, rating, cta_primary, cta_secondary, align` |
| `price_calculator` | Kalkulator Estimator Harga | • `card_two_col` (Form kiri, Ringkasan & WA kanan)<br>• `compact_vertical` (Tampilan ringkas 1 kolom)<br>• `tabbed_steps` (Multi-step wizard per tahap) | `title, subtitle, default_material, default_package, show_wa_cta` |
| `services` | Katalog Layanan Custom | • `grid_3_col` (Grid 3 kolom dengan hover effect)<br>• `slider_carousel` (Slider Swiper interaktif)<br>• `cards_stack` (Tumpukan kartu modern)<br>• `icon_list` (Daftar list vertikal dengan ikon) | `title, service_ids[], category_id, limit, show_price` |
| `products` | Katalog Produk Ready/Ori | • `slider_carousel` (Slider Swiper touch/swipe)<br>• `grid_4_col` (Grid 4 kolom e-commerce)<br>• `grid_3_col` (Grid 3 kolom besar)<br>• `horizontal_scroll` (Scroll horizontal mobile-friendly)<br>• `masonry` (Grid masonry variatif) | `title, product_ids[], category_id, limit, swiper{loop,autoplay,per_view}` |
| `portfolio_gallery` | Galeri Hasildesain & Klien | • `masonry` (Grid tinggi dinamis / Pinterest style)<br>• `grid_3_col` (Grid standar 3 kolom + Lightbox)<br>• `slider_carousel` (Slider galeri foto)<br>• `before_after_slider` (Slider komparasi desain vs hasil jahit) | `title, category_id, limit, columns, lightbox, before_after` |
| `testimonials` | Ulasan / Testimoni Klien | • `slider_carousel` (Slider testimoni auto-rotate)<br>• `grid_cards` (Grid kartu ulasan + bintang)<br>• `masonry_quotes` (Masonry kutipan testimoni)<br>• `single_featured` (1 testimoni utama besar) | `title, location_code, limit, autoplay` |
| `blog_latest` | Artikel & Edukasi Jersey | • `grid_3_col` (Grid 3 kolom berita)<br>• `featured_left_list_right` (1 artikel utama kiri, 3 artikel list kanan)<br>• `slider_carousel` (Slider kartu artikel)<br>• `minimal_list` (List teks minimalis) | `title, category_id, limit, show_excerpt` |
| `trust_badges` | Trust Badges & keunggulan | • `horizontal_bar` (Bar horizontal 4 ikon)<br>• `grid_4_col` (Grid 4 kolom dengan box)<br>• `slider_marquee` (Running text / Marquee slider halus) | `items[{icon,label,desc}]` |
| `locations_grid` | Geo-Grid Jangkauan Kota | • `geo_grid` (Grid badge kota dengan pencarian)<br>• `accordion_region` (Accordion per provinsi/wilayah)<br>• `slider_pills` (Slider pill nama kota)<br>• `combobox_search` (Dropdown live search kota) | `title, service_slug, location_ids[]` |
| `cta_wa` | Banner CTA WhatsApp | • `banner_gradient` (Banner lebar dengan background gradient)<br>• `split_card` (Kartu 2 kolom dengan ilustrasi)<br>• `floating_fixed` (Widget melayang di pojok layar) | `heading, sub, text, wa_number, message_template, floating` |

---

## PART G — ROADMAP IMPLEMENTASI & STATUS

### Fase 1 — MVP (Minggu 1–4)
| Pekan | Deliverable |
|---|---|
| 1 | Setup CI4 + SQLite + HTMX SPA Engine + Dynamic Theme (`dynamic_theme.php`) + Dark/Light Mode + Tailwind |
| 2 | Admin Dashboard HTMX: Settings Profil Brand, Dynamic Theme, Kalkulator Harga & CRUD Konten |
| 3 | Halaman Publik SPA + Komponen Kalkulator Harga Custom + Instant Search Overlay (`Ctrl+K`) |
| 4 | OOB JSON-LD (Organization, LocalBusiness, AboutPage, ContactPage, Product); Sitemap + Robots |

---

## STATUS IMPLEMENTASI — MVP (SPA HTMX & Dynamic Brand Settings Enabled)

> Diperbarui otomatis saat scaffold MVP selesai dieksekusi.

**Yang sudah jalan (terverifikasi via `spark serve` → localhost:8080):**
- ✅ CodeIgniter 4.7.4 + SQLite (`writable/pusjersey.sqlite`) + Tailwind CSS minified (`public/assets/css/app.min.css`)
- ✅ **Engine SPA HTMX Active**: Global `hx-boost="true"` pada body, target `#main-content`, OOB updates untuk Title, Meta Description, Canonical URL, dan JSON-LD Structured Data (`Views/partials/seo_oob.php`).
- ✅ **Dynamic Theme & Dark Mode Injector**: Inject CSS variables `:root` untuk palet warna, tipografi Google Fonts, & sakelar mode terang/gelap tanpa FOUC (`Views/partials/dynamic_theme.php`).
- ✅ **Profil Brand & Settings Dynamic**: Integrasi data profil brand (logo, tagline, legal, alamat, kontak CS, sosial media, jam operasional) tersimpan di `settings`.
- ✅ **Kalkulator & Estimator Harga Custom Jersey**: Fitur simulasi harga realtime berdasarkan kuantitas, bahan, tipe cetak, dan paket produk + konversi CTA WA Branded.
- ✅ **Live Instant Search Overlay (`Ctrl+K`)**: Modal HTMX search auto-suggest yang mengelompokkan Produk, Layanan Kota, Blog, dan Portofolio.
- ✅ **Halaman Dinamis Tentang Kami (`/tentang-kami`) & Kontak (`/kontak`)**: Lengkap dengan form kontak HTMX & schema JSON-LD `AboutPage` & `ContactPage`.
- ✅ **Resolver Lokasi Dinamis SPA**: `/layanan/pusat-jersey-custom-jakarta`, `...-bandung`, `...-surabaya`, `...-semarang`, `...-medan`, dan `custom-jersey-satuan-bandung` → Respon Dual-Mode (200 Full SSR untuk Bot / 200 Partial HTMX + OOB JSON-LD untuk User Navigation).
- ✅ CTA multi-channel `/cta/{channel}/{slug}` dengan `hx-boost="false"` → 302 Direct (WA dengan pesan branded; Shopee/Tokopedia/TikTok dengan kode affiliate + UTM) + tercatat di `cta_logs`.

**Jalankan:** `php spark serve --port 8080` · build CSS: `npm run build`

*Dokumen selesai — acuan sprint SPA HTMX, Dynamic Brand, Price Calculator & Live Search pusatjersey.com.*
