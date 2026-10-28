# 🔍 Audit Total — Backend & UI (Pusat Jersey, CI4 + HTMX SPA)

Tanggal audit: 1 Oktober 2026 · Scope: seluruh `app/`, config, migrations, views, CSS, routing.

---

## Skor Ringkasan

| Area | Nilai | Catatan singkat |
|---|---|---|
| Arsitektur & Konsistensi | **6/10** | Pola bagus (dual-mode SSR/HTMX), tapi banyak duplikasi & tanggung jawab bocor |
| Keamanan | **3/10** | Auth admin bertaruh pada settings DB, tanpa rate limit, tanpa validasi input, upload via URL field bebas |
| Performa | **4/10** | PageCache aktif tanpa konfigurasi, N+1 queries, view counter menulis DB per load |
| UI/UX (public) | **7/10** | Design system "Editorial Minimal" solid & konsisten |
| UI/UX (admin) | **4/10** | Identitas visual terbelah: frontend editorial terang vs admin dashboard "AI-slop" slate-950 |
| Data Integrity | **5/10** | Foreign keys OFF, tanpa indexes eksplisit, SKU/status bebas |
| Testability | **2/10** | 0 test fungsional, hanya contoh bawaan CI4 |

---

## PART 1 — BACKEND

### 1.1 🔴 KRITIS: Autentikasi Admin Rapuh

**Lokasi:** [app/Controllers/Admin/Login.php](app/Controllers/Admin/Login.php), [app/Filters/Auth.php](app/Filters/Auth.php)

- Kredensial admin disimpan sebagai baris `settings` (`admin_username`, `admin_password_hash`). Username **plaintext di DB** — siapa pun dengan akses DB (atau halaman Settings yang menampilkan semua baris!) melihatnya.
- `Auth::before()` hanya cek `session('is_admin') === true` — **tidak ada session regeneration setelah login** (session fixation), **tidak ada rate limiting** pada `attempt()`, tidak ada lockout/2FA. Endpoint login bisa di-brute force tanpa batas.
- Logout lewat `GET /admin/logout` (lihat [app/Config/Routes.php](app/Config/Routes.php)) — bisa dipicu lintas situs (state-changing via GET, meski dampak rendah).

**Rekomendasi:**
1. Buat tabel `users` (atau minimal pindahkan kredensial ke `.env`) + `password_hash()` saat set, `password_verify()` saat cek.
2. `session()->regenerate()` setelah login sukses.
3. Tambah rate limit sederhana (mis. sleep + counter di cache, atau `throttler` library CI4).
4. Logout jadi `POST` + CSRF.

### 1.2 🔴 KRITIS: Tidak Ada Validasi Input di Seluruh Controller

**Lokasi:** semua controller, contoh [app/Controllers/Admin/Products.php](app/Controllers/Admin/Products.php), [app/Controllers/Blog.php](app/Controllers/Blog.php), [app/Controllers/Page.php](app/Controllers/Page.php)

`code_search` untuk `validation|validate|Rules` di `app/Controllers` → **0 hasil**. Konsekuensi:

- **Komentar blog** (`Blog::addComment`): tidak ada validasi format email, panjang konten, maupun cek `parent_id` benar-benar milik post yang sama → membanjiri tabel `comments` dengan junk (moderasi manual satu-satu).
- **Produk**: `price` bisa negatif via POST langsung; `status` bebas string apa pun (bukan hanya draft/published/archived); `featured_image` string bebas.
- **Form kontak**: name/phone/message langsung disisip ke pesan WA tanpa sanitasi.
- **Kalkulator**: `quantity` sudah di-clamp bagus, tapi `material`/`print_type` tidak diverifikasi terhadap whitelist (hanya dipakai di if, aman-ish, tapi rapuh saat daftar berkembang).

**Rekomendasi:** Terapkan CI4 `Validation` dengan rules per-endpoint, atau setidaknya `$model->insert($data)` setelah `$this->validateData()`. Tambah whitelist enum untuk `status`, `material`, `print_type`, `channel`.

### 1.3 🔴 KRITIS: `pagecache` Global Tanpa `PageCache` Config → Cache File Nggendong

**Lokasi:** [app/Config/Filters.php](app/Config/Filters.php) (`'pagecache'` di `$required`), **tidak ada** `app/Config/PageCache.php`.

CI4 `PageCache` mencari `Config\PageCache` untuk menentukan TTL & excluding URI. Tanpa file config, TTL default 0 → **setiap halaman public di-cache ke file untuk TTL default**, termasuk halaman yang sering berubah, sitemap.xml, dan kemungkinan response HTMX partial. Gejala klasik: "sudah update konten di admin, tapi halaman masih lama", atau admin login loop karena halaman cached menyimpan state lama.

**Rekomendasi:** Buat `app/Config/PageCache.php` eksplisit:
```php
public array $exclude = ['admin*', 'search/suggest*', 'sitemap.xml*', 'cta/*', 'kontak/send', 'layanan/calculate'];
public int $ttl = 300; // 5 menit
```
…atau matikan dulu kalau tidak yakin. **Ini penjelasan paling mungkin untuk bug "perubahan tidak muncul".**

### 1.4 🟠 Performa Database

**a) N+1 di halaman katalog** ([app/Controllers/Products.php](app/Controllers/Products.php), [app/Controllers/Blog.php](app/Controllers/Blog.php)):
```php
foreach ($categories as &$cat) {
    $cat['count'] = (int)$productModel->where(...)->countAllResults();
}
```
1 query per kategori, plus beberapa count total lain di method yang sama. Ganti dengan satu `SELECT category_id, COUNT(*) c FROM products WHERE status='published' GROUP BY category_id` lalu map by id.

**b) View counter blog menulis DB di setiap page view** ([app/Controllers/Blog.php:detail](app/Controllers/Blog.php)):
```php
$postModel->update($post['id'], ['views_count' => (int)$post['views_count'] + 1]);
```
Read-modify-write, bukan `SET views_count = views_count + 1` (race condition), dan eksekusi per request → beban tulis tak perlu. Gunakan `$postModel->where('id', $id)->increment('views_count', 1)` (WAJIB `where()` dulu — signature CI4: `increment($column, $value)`, argumen pertama kolom string, bukan ID!) atau antrian cache, dan skip saat request bot.

**c) Settings dimuat FULL di setiap request** ([app/Controllers/BaseController.php](app/Controllers/BaseController.php)): `getAllStructured()` mengambil **semua** baris settings, diparse json_decode tiap request. Untuk website publik seharusnya di-cache (`cache()->save('settings_structured', ..., 300)`) dan di-flush saat admin update.

**d) Sitemap meng-query semua konten tanpa paginasi/limit** ([app/Controllers/Sitemap.php](app/Controllers/Sitemap.php)) — wajar saat konten kecil, tapi akan membesar. Pertimbangkan cache 1 jam untuk hasil XML.

### 1.5 🟠 Data Integrity & Migrations

**Lokasi:** [app/Database/Migrations/2026-09-02-000001_CreateInitialSchema.php](app/Database/Migrations/2026-09-02-000001_CreateInitialSchema.php)

- **`foreignKeys => false` di Database.php default** — FK di schema tidak dipakai, jadi `post_id` orphan bisa muncul bebas setelah post dihapus (kecuali manual cascade).
- Tidak ada index eksplisit selain primary & unique slug: `posts(status, created_at)`, `products(status, category_id)`, `comments(post_id, status)`, `cta_logs(product_slug, created_at)` — semua bakal full-scan saat tabel membesar.
- `status` disimpan sebagai VARCHAR bebas — seharusnya enum/check atau paling tidak di-guard oleh validation.
- `$useTimestamps = true` di model tapi kolom bertipe DATETIME nullable — OK untuk SQLite, rapuh untuk MySQL strict mode (kolom `created_at` tidak `DEFAULT CURRENT_TIMESTAMP`).

**Rekomendasi migration kedua:** add missing indexes, aktifkan FK SQLite (atau cascade manual di model `delete()`), dan standarkan status enum via validation rules.

### 1.6 🟡 Arsitektur: Duplikasi & Bocor Tanggung Jawab

- **Controller mewarisi semua logic render** — `renderView()` di [BaseController.php](app/Controllers/BaseController.php) menggabungkan SEO, JSON-LD, HTMX OOB, dan view-partial logic sekaligus (~80 baris). Setiap kali butuh variasi, dev men-copas blok schema di controller (lihat duplikasi `BreadcrumbList` + `Service` schema yang nyaris identik di `Services::detail`, `Products::detail`, `Blog::detail`).
- **Model kurang "fat"** — semuanya hanya extend `Model` + `getPublishedWithCategory`. Query filter+count masih di controller (mis. `Products::index`, `Blog::index`). Sebenernya bagus untuk refactor: pindah ke model sebagai `getFiltered($filters)` + `countByCategory()`.
- **Search controller** ([app/Controllers/Search.php](app/Controllers/Search.php)) melakukan 5 query `LIKE '%q%'` terpisah — belum ada UNION, dan tanpa index `LIKE '%...%'` di SQLite pasti full scan. Untuk scale kecil ini masih ok, tapi minimal set `ESCAPE` karakter `%`/`_` dari input user (`like()` CI4 sudah escape by default — pastikan).

### 1.7 🟡 Koherensi Fungsi Pendukung

- `unique_slug()` helper ([app/Helpers/slug_helper.php](app/Helpers/slug_helper.php)) dipakai di `Products::store` via variabel `$id ?? null` yang **belum terdefinisi di store** (bug halus — harusnya `null`). Di `update()` juga slug tidak di-update/validasi sama sekali → update produk bisa mengubah `name` tapi slug tetap lama.
- `Cta::redirect` ([app/Controllers/Cta.php](app/Controllers/Cta.php)) sudah rapi (302 + log + UTM), tapi `affiliate_link` tidak pernah divalidasi host → admin bisa menulis link apa pun (ok, admin-only, tapi idealnya whitelist domain marketplace).

---

## PART 2 — UI / FRONTEND

### 2.1 Desain Visual — Dua Kepribadian

**Public site (bagus, keep!):** Design token "Editorial Minimal" di `main.css` — wireframe seperti kartu terpisah, mono-label, spacing konsisten, icon Lucide, anti-gradient. Ini identitas kuat.

**Admin panel (banyak di-neglect):** [layouts/admin.php](app/Views/layouts/admin.php) & semua `admin/**/*.php` memakai estetika berbeda (slate-950/800, rounded-3xl, text-[10-13px] super kecil, neon accents). Terasa seperti template admin generik, tidak nyambung dengan brand.

**Rekomendasi:** Regenerasi admin UI pakai token yang sama (`bg-surface-alt`, `text-ink`, `font-heading`, `btn`, `mono-label`) → jadi satu bahasa desain. Ini perubahan low-risk (view-only) dengan efek visual paling terasa.

### 2.2 Aksesibilitas (A11y)

- ✅ Sudah baik: `aria-label` di icon buttons, `aria-current="page"` di nav aktif, `hx-boost="false"` untuk CTA eksternal, `noscript` untuk `[x-cloak]`, `scroll-padding-top`.
- ❌ **Kontras warna**: `text-faint` (#94a3b8) di atas bg putih = kontras ~2.4:1 (butuh 4.5:1). `text-muted` (#475569) di atas surface-alt ok, tapi `text-faint` banyak dipakai di body text (`meta` class).
- ❌ **Focus visible**: belum ada `:focus-visible` ring eksplisit di `main.css` — keyboard navigation berkurang.
- ❌ **Dark mode toggle** (`navbar.php`) — button tanpa label text, hanya `aria-label="Ganti tema"` (ok), tapi animasi transisi tema tidak ada (flash langsung, tidak ada JS `transition: background-color .2s` pada body — minor).

### 2.3 Dark Mode — Berpotensi Tidak Konsisten

**Lokasi:** [app/Views/partials/dynamic_theme.php](app/Views/partials/dynamic_theme.php)

Style `dynamic-theme` di-inject **sebelum** Tailwind `app.min.css` di [layouts/main.php](app/Views/layouts/main.php). Berarti:
- CSS variables dari settings berhasil menimpa `:root` di main.css ✅
- TAPI `body { font-family: var(--font-body) }` dari dynamic_theme juga menimpa rule di main.css yang punya `-webkit-font-smoothing` & `text-rendering` — efeknya: properti yang hilang, inconsistent font rendering antar device.
- `.dark` di dynamic_theme hanya menimpa subset `:root` variables, **tidak menimpa `.dark` selector di main.css** — berarti kalau ada utility Tailwind yang hard-code warna (mis. `text-slate-300`, `bg-slate-800` di admin), dark mode dynamic theme tidak mempengaruhinya.

**Rekomendasi:** Pindahkan inject `dynamic_theme` **setelah** Tailwind (masih sebelum `</head>`), dan buat `body`/`h1` rule dari dynamic_theme cukup satu sumber kebenaran (hapus dari main.css, atau sebaliknya). Ini juga menghilangkan "flash" tema antar page load karena order CSS yang tidak pasti.

### 2.4 Bug Fungsional Kecil (Perlu Diverifikasi di Browser)

- **`price_calculator.php`** memakai `x-data="priceCalculator()"` tapi saya belum melihat definisi fungsi ini di file manapun (`code_search` untuk `priceCalculator` belum dijalankan). Kalau memang tidak ada, **kalkulator mati total** (tombol & hasil tidak update) — perlu dicek.
- **`unique_slug` dengan `$id ?? null` di `Products::store`** — `$id` belum didefinisikan di konteks store; berarti slug duplicate bisa lolos ke DB meski ada unique constraint (fallback error 500).
- **CSRF meta tag** di `seo_oob.php` di-regenerate per partial HTMX — dengan `regenerate=false` di `Security.php` ini aman, tapi kalau nanti `regenerate=true` akan break. Beri komentar di seo_oob.

### 2.5 Struktur View

- Home page builder ([app/Views/home/index.php](app/Views/home/index.php)) — pola map `section_key` → `components/{key}.php` bagus, tapi `is_file()` check di view kurang ideal. Pindah ke `Home::index` controller: `$sections` sudah di-resolve ke `view` path, dan view hanya loop render. 
- Semua component (hero, products, services, dll.) mengharapkan SEMUA variabel (`$products`, `$portfolios`, `$posts`, `$testimonials`, `$faqs`, `$locations`) dikirim padahal tidak semua perlu — controller `Home::index` selalu query semua. Refactor ke per-section lazy data fetch atau minimal filter null.

---

## PART 3 — PRIORITAS IMPROVISASI

| # | Prioritas | Item | Effort | Dampak |
|---|---|---|---|---|
| 1 | 🔴 P0 | Matikan / konfigurasi `pagecache` (bug tersembunyi) | 15 mnt | Perubahan konten langsung terlihat |
| 2 | 🔴 P0 | Validasi input di semua endpoint POST (Form Request + whitelist status) | 2–3 jam | Data bersih, jalur spam ditutup |
| 3 | 🔴 P0 | Auth admin: tabel users + regenerate session + rate limit + POST logout | 2–3 jam | Tidak bisa di-brute force |
| 4 | 🟠 P1 | Cache settings di BaseController + flush on update | 30 mnt | Save ~5–10 queries/request |
| 5 | 🟠 P1 | Perbaiki N+1 di Products/Blog index (GROUP BY count) | 30 mnt | Halaman katalog lebih cepat |
| 6 | 🟠 P1 | Ganti view counter dengan `increment()` + skip bot | 15 mnt | Mengurangi write DB |
| 7 | 🟠 P1 | Unifikasi admin UI dengan design system frontend | 3–4 jam | Branding konsisten, admin enak dipakai |
| 8 | 🟡 P2 | Migration kedua: indexes + FK ON | 1 jam | Skalabilitas DB |
| 9 | 🟡 P2 | Fix `dynamic_theme` order + hapus duplikasi CSS body/h1 | 30 mnt | Font/tema konsisten antar halaman |
| 10 | 🟡 P2 | Refactor `renderView()` + helper schema() untuk kurangi duplikasi controller | 2 jam | Maintainability |
| 11 | 🟢 P3 | A11y: perbaiki kontras `text-faint`, tambah `:focus-visible` ring | 30 mnt | Skor Lighthouse ↑ |
| 12 | 🟢 P3 | Tulis smoke test: `/`, `/produk`, `/blog`, admin login + CRUD produk | 2 jam | Safety net refactor |

**Quick wins (≤30 mnt, efek besar):** #1, #4, #5, #6, #11.

---

## Hal yang Sudah Bagus (Jangan Diubah)

- ✅ Dual-mode SSR/HTMX di `renderView()` — arsitektur SPA-SEO solid.
- ✅ Design system public (`main.css` editorial minimal, token CSS vars) — bersih & konsisten.
- ✅ Sitemap lengkap: static + geo pages + konten.
- ✅ CTA logging multi-channel dengan UTM + fallback.
- ✅ Anti-FOUC dark mode & scroll-padding anchor.
- ✅ CSRF via HTMX header `X-CSRF-TOKEN` + meta tag.

---

*Disusun otomatis oleh Buffy (Codebuff) — tidak semua temuan diuji runtime; lakukan verifikasi manual untuk item yang ditandai "perlu diverifikasi".*
