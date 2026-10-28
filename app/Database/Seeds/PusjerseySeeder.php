<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PusjerseySeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        // Empty existing tables to allow clean re-seeding
        $db->table('comments')->emptyTable();
        $db->table('page_sections')->emptyTable();
        $db->table('testimonials')->emptyTable();
        $db->table('faqs')->emptyTable();
        $db->table('location_services')->emptyTable();
        $db->table('locations')->emptyTable();
        $db->table('portfolios')->emptyTable();
        $db->table('services')->emptyTable();
        $db->table('products')->emptyTable();
        $db->table('posts')->emptyTable();
        $db->table('post_tags')->emptyTable();
        $db->table('tags')->emptyTable();
        $db->table('categories')->emptyTable();
        $db->table('settings')->emptyTable();

        // 1. SETTINGS
        $settingsData = [
            // Identitas Brand
            ['key_name' => 'brand_name', 'key_value' => 'Pusat Jersey', 'group_name' => 'brand'],
            ['key_name' => 'brand_legal_name', 'key_value' => 'PT Pusat Jersey Indonesia', 'group_name' => 'brand'],
            ['key_name' => 'brand_tagline', 'key_value' => 'Produsen Custom Jersey & Apparel Olahraga No.1', 'group_name' => 'brand'],
            ['key_name' => 'brand_logo', 'key_value' => '/favicon.ico', 'group_name' => 'brand'],
            ['key_name' => 'brand_favicon', 'key_value' => '/favicon.ico', 'group_name' => 'brand'],
            ['key_name' => 'brand_founding_year', 'key_value' => '2018', 'group_name' => 'brand'],

            // Kredensial Admin (Auth Filter)
            ['key_name' => 'admin_username', 'key_value' => 'admin', 'group_name' => 'brand'],
            ['key_name' => 'admin_password_hash', 'key_value' => password_hash('admin123', PASSWORD_DEFAULT), 'group_name' => 'brand'],

            // Kontak & CS
            ['key_name' => 'contact_phone', 'key_value' => '+6281234567890', 'group_name' => 'contact'],
            ['key_name' => 'contact_whatsapp', 'key_value' => '6281234567890', 'group_name' => 'contact'],
            ['key_name' => 'contact_email', 'key_value' => 'info@pusatjersey.com', 'group_name' => 'contact'],
            ['key_name' => 'contact_address', 'key_value' => 'Jl. Raya Jersey No. 88, Kebayoran Baru', 'group_name' => 'contact'],
            ['key_name' => 'contact_city', 'key_value' => 'Jakarta Selatan', 'group_name' => 'contact'],
            ['key_name' => 'contact_maps_embed', 'key_value' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.275551390494!2d106.800000014769!3d-6.227400095492!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNsKwMTMnMzguNiJTIDEwNsKwNDgnMDAnRSI!5e0!3m2!1sid!2sid!4v1620000000000!5m2!1sid!2sid', 'group_name' => 'contact'],
            ['key_name' => 'contact_opening_hours_json', 'key_value' => json_encode([
                ['days' => 'Senin - Jumat', 'hours' => '08:00 - 17:00 WIB'],
                ['days' => 'Sabtu', 'hours' => '09:00 - 15:00 WIB'],
                ['days' => 'Minggu', 'hours' => 'Tutup (Order via WA Tetap Buka)']
            ]), 'group_name' => 'contact'],
            ['key_name' => 'social_links_json', 'key_value' => json_encode([
                ['platform' => 'Instagram', 'url' => 'https://instagram.com/pusatjersey', 'icon' => 'instagram'],
                ['platform' => 'TikTok', 'url' => 'https://tiktok.com/@pusatjersey', 'icon' => 'tiktok'],
                ['platform' => 'Shopee', 'url' => 'https://shopee.co.id/pusatjersey', 'icon' => 'shopee'],
                ['platform' => 'Tokopedia', 'url' => 'https://tokopedia.com/pusatjersey', 'icon' => 'tokopedia']
            ]), 'group_name' => 'contact'],
            ['key_name' => 'partners_json', 'key_value' => json_encode([
                ['name' => 'Futsal Community Jakarta', 'logo' => '', 'url' => ''],
                ['name' => 'SSB Nusantara', 'logo' => '', 'url' => ''],
                ['name' => 'Komunitas Sepeda Bandung', 'logo' => '', 'url' => ''],
                ['name' => 'Sportfair Event Organizer', 'logo' => '', 'url' => ''],
                ['name' => 'Tim Basket Esport Surabaya', 'logo' => '', 'url' => ''],
                ['name' => 'Dealer Apparel Medan', 'logo' => '', 'url' => ''],
            ]), 'group_name' => 'partners'],

            // Tentang Kami
            ['key_name' => 'about_title', 'key_value' => 'Tentang Pusat Jersey Indonesia', 'group_name' => 'about'],
            ['key_name' => 'about_short_desc', 'key_value' => 'Pusat konveksi & cetak custom jersey olahraga berkualitas tinggi dengan teknologi sublimasi terkini dan bahan drifit impor.', 'group_name' => 'about'],
            ['key_name' => 'about_story_html', 'key_value' => '<p class="mb-4">Berdiri sejak tahun 2018, <strong>Pusat Jersey Indonesia</strong> telah tumbuh menjadi vendor pilihan utama untuk lebih dari 10.000+ tim sepak bola, futsal, basket, sepeda, hingga instansi pemerintah dan perusahaan swasta di seluruh nusantara.</p><p>Kami memadukan mesin cetak sublimasi raksasa buatan Jepang dengan standar jahit presisi tinggi untuk menghasilkan produk jersey berkualitas ekspor yang nyaman, tahan lama, dan bergaransi warna tajam.</p>', 'group_name' => 'about'],
            ['key_name' => 'about_vision', 'key_value' => 'Menjadi produsen jersey custom & apparel olahraga pilihan utama nomor 1 di Indonesia yang mendunia.', 'group_name' => 'about'],
            ['key_name' => 'about_mission', 'key_value' => 'Menyediakan bahan jersey berteknologi quick-dry terbaik, menjamin kepastian waktu cetak, serta memberikan layanan desain kustom tanpa batas.', 'group_name' => 'about'],
            ['key_name' => 'about_stats_json', 'key_value' => json_encode([
                ['value' => '10.000+', 'label' => 'Tim Dilayani'],
                ['value' => '99.4%', 'label' => 'Kepuasan Klien'],
                ['value' => '50+', 'label' => 'Kota Jangkauan'],
                ['value' => '24/7', 'label' => 'Dukungan CS']
            ]), 'group_name' => 'about'],

            // Theme Dynamic Settings (Light & Dark — full palette)
            ['key_name' => 'theme_color_primary', 'key_value' => '#0284c7', 'group_name' => 'theme'],
            ['key_name' => 'theme_color_secondary', 'key_value' => '#0f172a', 'group_name' => 'theme'],
            ['key_name' => 'theme_color_accent', 'key_value' => '#f59e0b', 'group_name' => 'theme'],
            ['key_name' => 'theme_color_bg_light', 'key_value' => '#ffffff', 'group_name' => 'theme'],
            ['key_name' => 'theme_color_surface_light', 'key_value' => '#ffffff', 'group_name' => 'theme'],
            ['key_name' => 'theme_color_surface_alt_light', 'key_value' => '#f1f5f9', 'group_name' => 'theme'],
            ['key_name' => 'theme_color_text_light', 'key_value' => '#0f172a', 'group_name' => 'theme'],
            ['key_name' => 'theme_color_text_muted_light', 'key_value' => '#475569', 'group_name' => 'theme'],
            ['key_name' => 'theme_color_text_faint_light', 'key_value' => '#64748b', 'group_name' => 'theme'],
            ['key_name' => 'theme_color_border_light', 'key_value' => '#e2e8f0', 'group_name' => 'theme'],
            ['key_name' => 'theme_color_bg_dark', 'key_value' => '#0b0f19', 'group_name' => 'theme'],
            ['key_name' => 'theme_color_surface_dark', 'key_value' => '#1e293b', 'group_name' => 'theme'],
            ['key_name' => 'theme_color_surface_alt_dark', 'key_value' => '#111827', 'group_name' => 'theme'],
            ['key_name' => 'theme_color_text_dark', 'key_value' => '#f8fafc', 'group_name' => 'theme'],
            ['key_name' => 'theme_color_text_muted_dark', 'key_value' => '#cbd5e1', 'group_name' => 'theme'],
            ['key_name' => 'theme_color_text_faint_dark', 'key_value' => '#64748b', 'group_name' => 'theme'],
            ['key_name' => 'theme_color_border_dark', 'key_value' => '#334155', 'group_name' => 'theme'],
            ['key_name' => 'theme_color_cta_wa', 'key_value' => '#22c55e', 'group_name' => 'theme'],
            ['key_name' => 'theme_font_body', 'key_value' => 'Inter', 'group_name' => 'theme'],
            ['key_name' => 'theme_font_heading', 'key_value' => 'Outfit', 'group_name' => 'theme'],
            ['key_name' => 'theme_font_google_import', 'key_value' => 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap', 'group_name' => 'theme'],
        ];
        foreach ($settingsData as $s) {
            $s['created_at'] = $now;
            $s['updated_at'] = $now;
            $db->table('settings')->insert($s);
        }

        // 2. CATEGORIES
        $categoryIds = [];
        $categories = [
            ['name' => 'Edukasi & Tips', 'slug' => 'edukasi-tips', 'type' => 'blog', 'description' => 'Tips perawatan jersey & panduan memilih bahan'],
            ['name' => 'Jersey Futsal & Sepakbola', 'slug' => 'futsal-sepakbola', 'type' => 'product', 'description' => 'Jersey custom futsal & sepakbola sublimasi'],
            ['name' => 'Jersey Original', 'slug' => 'jersey-original', 'type' => 'product', 'description' => 'Jersey ori brand resmi internasional & lokal'],
            ['name' => 'Portofolio Tim', 'slug' => 'portofolio-tim', 'type' => 'portfolio', 'description' => 'Dokumentasi hasil cetak tim jersey']
        ];
        foreach ($categories as $c) {
            $c['created_at'] = $now;
            $c['updated_at'] = $now;
            $db->table('categories')->insert($c);
            $categoryIds[] = (int)$db->insertID();
        }

        // 2b. TAGS
        $tagIds = [];
        $tags = [
            ['name' => 'Perawatan Jersey', 'slug' => 'perawatan-jersey'],
            ['name' => 'Bahan Kain', 'slug' => 'bahan-kain'],
            ['name' => 'Tips & Trik', 'slug' => 'tips-trik'],
            ['name' => 'Desain Tim', 'slug' => 'desain-tim']
        ];
        foreach ($tags as $tag) {
            $tag['created_at'] = $now;
            $tag['updated_at'] = $now;
            $db->table('tags')->insert($tag);
            $tagIds[] = (int)$db->insertID();
        }

        // 3. POSTS (Blog)
        $postIds = [];
        $posts = [
            [
                'category_id' => $categoryIds[0] ?? 1,
                'title' => 'Cara Merawat Jersey Sublimasi Agar Warna Tetap Tajam & Awet Berada di Lapangan',
                'slug' => 'cara-merawat-jersey-sublimasi-awet',
                'excerpt' => 'Panduan lengkap merawat jersey sublimasi bahan drifit agar tidak cepat pudar dan tetap elastis.',
                'content' => '<h2>Mengapa Jersey Sublimasi Butuh Perawatan Khusus?</h2><p>Teknologi sublimasi meresap ke dalam serat kain drifit. Namun, suhu cuci yang terlalu panas atau pemutih pakaian dapat merusak serat mikro kain tersebut.</p><h2>1. Cuci Menggunakan Air Dingin & Deterjen Lembut</h2><p>Selalu gunakan air suhu ruangan saat mencuci jersey custom Anda. Hindari menggunakan pemutih pakaian cair.</p><h2>2. Hindari Penggunaan Mesin Pengering Ber-Suhu Tinggi</h2><p>Gantung jersey di tempat yang teduh dan diangin-anginkan. Sinar matahari langsung berlebihan dapat mengurangi elastisitas serat elastan.</p><h2>3. Cara Menyetrika Jersey Sublimasi yang Benar</h2><p>Gunakan alas kain pelapis saat menyetrika atau gunakan suhu paling rendah (mode synthetic).</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1517466787929-bc90951d0974?auto=format&fit=crop&w=800&q=80',
                'meta_title' => 'Cara Merawat Jersey Sublimasi Agar Warna Tetap Tajam | Pusat Jersey',
                'meta_description' => 'Tips dan trik merawat jersey custom sublimasi drifit milano agar tidak mudah rusak dan warna tetap tajam.',
                'status' => 'published',
                'views_count' => 142,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'category_id' => $categoryIds[0] ?? 1,
                'title' => 'Panduan Memilih Bahan Kain Jersey Custom: Drifit Milano vs Serena vs Emboss',
                'slug' => 'panduan-memilih-bahan-kain-jersey-custom',
                'excerpt' => 'Ketahui perbedaan karakteristik kain Drifit Milano, Serena, dan Emboss untuk menemukan bahan yang tepat bagi tim Anda.',
                'content' => '<h2>Pentingnya Memilih Bahan Kain Jersey yang Tepat</h2><p>Setiap cabang olahraga membutuhkan spesifikasi bahan jersey yang berbeda. Sepakbola butuh sirkulasi udara maksimal, sedangkan sepeda butuh fleksibilitas ketat.</p><h2>1. Karakteristik Drifit Milano</h2><p>Memiliki motif pori-pori zigzag halus, sangat ringan, menyerap keringat dengan cepat (quick-dry), dan menjadi favorit utama pemesan custom jersey.</p><h2>2. Karakteristik Kain Serena</h2><p>Memiliki permukaan yang licin dan sedikit mengkilap. Sangat fleksibel dan nyaman dipakai untuk aktivitas olahraga santai.</p><h2>3. Karakteristik Drifit Emboss</h2><p>Bahan kelas premium dengan tekstur 3D bermotif timbul yang memberikan kesan mewah dan eksklusif saat dipakai bertanding.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&w=800&q=80',
                'meta_title' => 'Panduan Bahan Jersey Custom: Milano vs Serena vs Emboss | Pusat Jersey',
                'meta_description' => 'Perbandingan bahan kain jersey custom terlengkap: Drifit Milano, Serena, dan Emboss premium.',
                'status' => 'published',
                'views_count' => 289,
                'created_at' => $now,
                'updated_at' => $now
            ]
        ];
        foreach ($posts as $p) {
            $db->table('posts')->insert($p);
            $postIds[] = (int)$db->insertID();
        }

        // 3b. POST TAGS (Pivot) — using real inserted post/tag IDs
        if (count($postIds) >= 2 && count($tagIds) >= 4) {
            $db->table('post_tags')->insertBatch([
                ['post_id' => $postIds[0], 'tag_id' => $tagIds[0], 'created_at' => $now, 'updated_at' => $now],
                ['post_id' => $postIds[0], 'tag_id' => $tagIds[2], 'created_at' => $now, 'updated_at' => $now],
                ['post_id' => $postIds[1], 'tag_id' => $tagIds[1], 'created_at' => $now, 'updated_at' => $now],
                ['post_id' => $postIds[1], 'tag_id' => $tagIds[2], 'created_at' => $now, 'updated_at' => $now]
            ]);
        }

        // 4. PRODUCTS
        $products = [
            [
                'category_id' => $categoryIds[1] ?? 2,
                'name' => 'Jersey Custom Futsal Full Sublimasi Pro Series',
                'slug' => 'jersey-custom-futsal-full-sublimasi-pro',
                'sku' => 'PJ-CST-FUT01',
                'price' => 135000,
                'discount_price' => 125000,
                'is_custom' => 1,
                'is_affiliate' => 0,
                'shopee_link' => 'https://shopee.co.id/pusatjersey-futsal-pro',
                'tokopedia_link' => 'https://tokopedia.com/pusatjersey/futsal-pro',
                'tiktok_link' => 'https://tiktok.com/@pusatjersey/live',
                'featured_image' => 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=800&q=80',
                'gallery_json' => json_encode([
                    'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&w=800&q=80'
                ]),
                'description' => 'Jersey custom futsal & sepakbola premium dengan bahan Drifit Milano impor, cetak sublimasi full print 360 derajat, gratis desain & gratis nameset.',
                'specs_json' => json_encode(['Bahan' => 'Drifit Milano Quick-Dry', 'Teknik' => 'Full Sublimation High-Res', 'Ukuran' => 'S, M, L, XL, XXL, 3XL', 'Minimal Order' => '6 Stel']),
                'meta_title' => 'Jersey Custom Futsal Full Sublimasi Pro Series | Pusat Jersey',
                'meta_description' => 'Pesan jersey custom futsal full sublimasi bahan drifit milano berkualitas tinggi. Gratis desain & nameset.',
                'status' => 'published',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'category_id' => $categoryIds[2] ?? 3,
                'name' => 'Jersey Original National Team Edition 2026',
                'slug' => 'jersey-original-national-team-2026',
                'sku' => 'PJ-ORI-NT2026',
                'price' => 499000,
                'discount_price' => 450000,
                'is_custom' => 0,
                'is_affiliate' => 1,
                'affiliate_link' => 'https://shopee.co.id/universal-affiliate-link?product=nt2026',
                'shopee_link' => 'https://shopee.co.id/pusatjersey-ori-nt2026',
                'tokopedia_link' => 'https://tokopedia.com/pusatjersey/ori-nt2026',
                'tiktok_link' => 'https://tiktok.com/@pusatjersey/ori-nt2026',
                'featured_image' => 'https://images.unsplash.com/photo-1518091043644-c1d4457512c6?auto=format&fit=crop&w=800&q=80',
                'gallery_json' => json_encode(['https://images.unsplash.com/photo-1518091043644-c1d4457512c6?auto=format&fit=crop&w=800&q=80']),
                'description' => 'Jersey Original Authentic Player Issue resmi dengan teknologi sirkulasi udara terkini dan jahitan Seamless.',
                'specs_json' => json_encode(['Bahan' => 'Authentic Polyester 100%', 'Fit' => 'Slim Fit Player Issue', 'Lisensi' => 'Official Authentic product']),
                'meta_title' => 'Jersey Original National Team Edition 2026 | Pusat Jersey',
                'meta_description' => 'Beli jersey original national team edition 2026. 100% authentic garansi uang kembali.',
                'status' => 'published',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ];
        foreach ($products as $pr) {
            $db->table('products')->insert($pr);
        }

        // 5. SERVICES
        $serviceIds = [];
        $services = [
            [
                'title' => 'Pusat Jersey Custom',
                'slug' => 'pusat-jersey-custom',
                'short_desc' => 'Layanan cetak jersey custom full sublimasi untuk tim futsal, sepakbola, basket, dan event olahraga.',
                'content' => '<h2>Jasa Konveksi & Cetak Jersey Custom No. 1</h2><p>Kami melayani pembuatan jersey custom dengan teknologi cetak sublimasi kualitas Jepang. Bebas menentukan desain, warna, logo tim, sponsorship, dan nama pemain tanpa biaya tambahan.</p><h2>Keunggulan Layanan Kami</h2><ul><li>Bahan Kain Drifit Impor menyerap keringat.</li><li>Warna tajam, anti luntur, dan tahan cuci berkali-kali.</li><li>Garansi retur jika terjadi kesalahan cetak/jahit dari pihak konveksi.</li></ul>',
                'icon' => 'shirt',
                'featured_image' => 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=800&q=80',
                'starting_price' => 125000,
                'meta_title' => 'Pusat Jersey Custom Futsal & Sepakbola | Pusat Jersey',
                'meta_description' => 'Jasa konveksi pembuatan jersey custom sublimasi berkualitas tinggi untuk tim dan instansi.',
                'status' => 'published',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'title' => 'Custom Jersey Satuan Premium',
                'slug' => 'custom-jersey-satuan',
                'short_desc' => 'Layanan cetak jersey custom tanpa minimal order (bisa satuan) dengan kualitas hasil cetak setara pro team.',
                'content' => '<h2>Bisa Pesan Jersey Custom Satuan!</h2><p>Ingin tampil beda dengan jersey desain sendiri tanpa perlu pesan secara tim? Kami melayani pemesanan jersey custom satuan dengan pengerjaan kilat dan hasil presisi tinggi.</p>',
                'icon' => 'user-check',
                'featured_image' => 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&w=800&q=80',
                'starting_price' => 150000,
                'meta_title' => 'Custom Jersey Satuan Premium | Pusat Jersey',
                'meta_description' => 'Cetak jersey custom satuan tanpa minimal order. Bebas desain & bahan berkualitas.',
                'status' => 'published',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ];
        foreach ($services as $srv) {
            $db->table('services')->insert($srv);
            $serviceIds[] = (int)$db->insertID();
        }

        // 6. PORTFOLIOS
        $portfolios = [
            [
                'category_id' => $categoryIds[3] ?? 4,
                'client_name' => 'Garuda Futsal FC',
                'title' => 'Jersey Futsal Custom Full Sublim Garuda FC Jakarta',
                'slug' => 'jersey-futsal-garuda-fc-jakarta',
                'featured_image' => 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=800&q=80',
                'mockup_before_image' => 'https://images.unsplash.com/photo-1517466787929-bc90951d0974?auto=format&fit=crop&w=800&q=80',
                'real_after_image' => 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=800&q=80',
                'gallery_json' => json_encode(['https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=800&q=80']),
                'description' => 'Pembuatan 24 stel jersey tim futsal Garuda FC dengan tema warna gradasi navy ke emas.',
                'status' => 'published',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ];
        foreach ($portfolios as $port) {
            $db->table('portfolios')->insert($port);
        }

        // 7. LOCATIONS (10 Kota Utama)
        $locationIds = [];
        $locations = [
            ['name' => 'Jakarta', 'slug' => 'jakarta', 'province' => 'DKI Jakarta', 'type' => 'kota'],
            ['name' => 'Bandung', 'slug' => 'bandung', 'province' => 'Jawa Barat', 'type' => 'kota'],
            ['name' => 'Surabaya', 'slug' => 'surabaya', 'province' => 'Jawa Timur', 'type' => 'kota'],
            ['name' => 'Semarang', 'slug' => 'semarang', 'province' => 'Jawa Tengah', 'type' => 'kota'],
            ['name' => 'Medan', 'slug' => 'medan', 'province' => 'Sumatera Utara', 'type' => 'kota'],
            ['name' => 'Makassar', 'slug' => 'makassar', 'province' => 'Sulawesi Selatan', 'type' => 'kota'],
            ['name' => 'Palembang', 'slug' => 'palembang', 'province' => 'Sumatera Selatan', 'type' => 'kota'],
            ['name' => 'Yogyakarta', 'slug' => 'yogyakarta', 'province' => 'DI Yogyakarta', 'type' => 'kota'],
            ['name' => 'Bali', 'slug' => 'bali', 'province' => 'Bali', 'type' => 'kabupaten'],
            ['name' => 'Malang', 'slug' => 'malang', 'province' => 'Jawa Timur', 'type' => 'kota']
        ];
        foreach ($locations as $loc) {
            $loc['created_at'] = $now;
            $loc['updated_at'] = $now;
            $db->table('locations')->insert($loc);
            $locationIds[] = (int)$db->insertID();
        }

        // 8. LOCATION SERVICES (Pivot)
        foreach ([0, 1] as $si) {
            $serviceId = $serviceIds[$si] ?? ($si + 1);
            foreach ($locationIds as $li => $locationId) {
                $locName = $locations[$li]['name'];
                $srvSlug = ($si === 0) ? 'pusat-jersey-custom' : 'custom-jersey-satuan';
                $srvTitle = ($si === 0) ? 'Pusat Jersey Custom' : 'Custom Jersey Satuan';

                $db->table('location_services')->insert([
                    'service_id' => $serviceId,
                    'location_id' => $locationId,
                    'custom_title' => "{$srvTitle} {$locName} — Pesan Jersey Tim & Satuan",
                    'custom_content' => "<p>Layanan konveksi dan tempat pembuatan <strong>{$srvTitle} di {$locName}</strong>. Kirim cepat, gratis desain, dan garansi kualitas hasil cetak sublimasi terbaik untuk wilayah {$locName} dan sekitarnya.</p>",
                    'meta_title' => "{$srvTitle} {$locName} Terbaik | Pusat Jersey",
                    'meta_description' => "Cari tempat pembuatan {$srvTitle} di {$locName}? Dapatkan garansi warna tajam, bahan drifit berkualitas, dan promo gratis ongkir ke {$locName}.",
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
        }

        // 9. FAQS
        $faqs = [
            ['question' => 'Berapa lama proses pembuatan jersey custom?', 'answer' => 'Proses pengerjaan normal adalah 7 - 10 hari kerja setelah desain disetujui dan DP masuk. Untuk layanan kilat dapat selesai dalam 3-5 hari.', 'post_type' => 'global', 'sort_order' => 1],
            ['question' => 'Apakah ada minimal order pemesanan jersey tim?', 'answer' => 'Untuk pesanan tim minimal order adalah 6 stel. Namun kami juga menyediakan layanan khusus pemesanan jersey custom satuan tanpa minimal order.', 'post_type' => 'global', 'sort_order' => 2],
            ['question' => 'Bagaimana jika ada kesalahan cetak nama atau ukuran?', 'answer' => 'Setiap pesanan dilindungi Garansi Retur 100%. Jika ada kesalahan nama/nomor yang tidak sesuai file approval, kami cetak ulang secara GRATIS.', 'post_type' => 'global', 'sort_order' => 3]
        ];
        foreach ($faqs as $fq) {
            $fq['created_at'] = $now;
            $fq['updated_at'] = $now;
            $db->table('faqs')->insert($fq);
        }

        // 10. TESTIMONIALS
        $testimonials = [
            ['client_name' => 'Budi Santoso', 'team_name' => 'FC Vendor', 'city' => 'Jakarta', 'avatar' => 'https://i.pravatar.cc/100?img=11', 'comment' => 'Hasil sublimasinya benar-benar luar biasa tajam! Bahan Drifit Milano sangat adem dipakai main 2 jam full.', 'rating' => 5],
            ['client_name' => 'Reza Pratama', 'team_name' => 'Surabaya Esport', 'city' => 'Surabaya', 'avatar' => 'https://i.pravatar.cc/100?img=33', 'comment' => 'Proses pengerjaan cepat banget, 7 hari sudah sampai di Surabaya. Recommended bgt buat vendor jersey tim!', 'rating' => 5],
            ['client_name' => 'Dhani Irawan', 'team_name' => 'Komunitas Sepeda Bandung', 'city' => 'Bandung', 'avatar' => 'https://i.pravatar.cc/100?img=68', 'comment' => 'Desain bebas disesuaikan kemauan kami, admin CS ramah dan responsif membantu kalkulasi harga.', 'rating' => 5]
        ];
        foreach ($testimonials as $t) {
            $t['status'] = 'approved';
            $t['created_at'] = $now;
            $t['updated_at'] = $now;
            $db->table('testimonials')->insert($t);
        }

        // 11. PAGE SECTIONS (Page Builder)
        $sections = [
            ['page_name' => 'home', 'section_key' => 'hero', 'layout_style' => 'split_image_right', 'sort_order' => 1, 'settings_json' => json_encode(['title' => 'Vendor Custom Jersey No.1 di Indonesia', 'subtitle' => 'Cetak jersey futsal, sepakbola, basket & sepeda sublimasi kualitas pro team. Bebas desain, warna tajam, & garansi retur 100%.', 'eyebrow' => 'Terpercaya oleh 10.000+ Tim Sport', 'bg_image' => '', 'cta_primary' => ['text' => 'Konsultasi WA Sekarang', 'url' => 'https://wa.me/6281234567890?text=Halo%20Admin%20Pusat%20Jersey%2C%20saya%20ingin%20konsultasi%20pemesanan%20jersey%20custom.'], 'cta_secondary' => ['text' => 'Lihat Layanan', 'url' => '/layanan']])],
            ['page_name' => 'home', 'section_key' => 'trust_badges', 'layout_style' => 'horizontal_bar', 'sort_order' => 2, 'settings_json' => json_encode(['items' => [['icon' => 'zap', 'label' => 'Cetak Sublim High-Res', 'desc' => 'Warna tajam anti luntur'], ['icon' => 'shield-check', 'label' => 'Garansi Retur 100%', 'desc' => 'Jaminan jika ada salah jahit/cetak'], ['icon' => 'truck', 'label' => 'Kirim Seluruh Indonesia', 'desc' => 'Support pengiriman cargo kilat'], ['icon' => 'gift', 'label' => 'Gratis Desain & Nameset', 'desc' => 'Tanpa biaya tambahan layout']]])],
            ['page_name' => 'home', 'section_key' => 'services', 'layout_style' => 'grid_3_col', 'sort_order' => 4, 'settings_json' => json_encode(['title' => 'Layanan Utama Custom Apparel', 'limit' => 6])],
            ['page_name' => 'home', 'section_key' => 'products', 'layout_style' => 'slider_carousel', 'sort_order' => 5, 'settings_json' => json_encode(['title' => 'Katalog Produk & Jersey Original Ready Stock', 'limit' => 6])],
            ['page_name' => 'home', 'section_key' => 'portfolio_gallery', 'layout_style' => 'grid_3_col', 'sort_order' => 6, 'settings_json' => json_encode(['title' => 'Galeri Hasil Cetak & Portofolio Klien', 'limit' => 6])],
            ['page_name' => 'home', 'section_key' => 'testimonials', 'layout_style' => 'slider_carousel', 'sort_order' => 7, 'settings_json' => json_encode(['title' => 'Apa Kata Klien Kami?', 'limit' => 6])],
            ['page_name' => 'home', 'section_key' => 'blog_latest', 'layout_style' => 'grid_3_col', 'sort_order' => 8, 'settings_json' => json_encode(['title' => 'Artikel & Tips Edukasi Jersey', 'limit' => 3])],
            ['page_name' => 'home', 'section_key' => 'locations_grid', 'layout_style' => 'geo_grid', 'sort_order' => 9, 'settings_json' => json_encode(['title' => 'Jangkauan Pengiriman & Layanan Kota di Indonesia'])],
            ['page_name' => 'home', 'section_key' => 'partners', 'layout_style' => 'slider_carousel', 'sort_order' => 10, 'settings_json' => json_encode(['title' => 'Mitra & Partner Kami', 'lede' => 'Kolaborasi bersama klub, komunitas, dan mitra bisnis di seluruh Indonesia.'])],
            ['page_name' => 'home', 'section_key' => 'cta_wa', 'layout_style' => 'banner_gradient', 'sort_order' => 11, 'settings_json' => json_encode(['heading' => 'Siap Buat Jersey Tim Impian Kamu Hari Ini?', 'sub' => 'Dapatkan promo gratis ongkir & bonus bendera tim untuk pemesanan minggu ini!', 'text' => 'Hubungi Admin WA', 'wa_number' => '6281234567890'])]
        ];
        foreach ($sections as $sec) {
            $sec['is_active'] = 1;
            $sec['created_at'] = $now;
            $sec['updated_at'] = $now;
            $db->table('page_sections')->insert($sec);
        }

        // 12. COMMENTS
        $comments = [
            [
                'post_id' => $postIds[0] ?? 1,
                'parent_id' => null,
                'author_name' => 'Andi Wijaya',
                'author_email' => 'andi@gmail.com',
                'author_website' => null,
                'content' => 'Sangat bermanfaat artikelnya! Baru tahu kalau deterjen cair lebih disarankan daripada deterjen bubuk untuk jersey sublimasi.',
                'status' => 'approved',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ];
        foreach ($comments as $cm) {
            $db->table('comments')->insert($cm);
        }
    }
}
