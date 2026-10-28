<div class="space-y-8">
    
    <div class="space-y-1">
        <div class="flex items-center gap-2">
            <span class="mono-label text-[10px]">01 · PENGATURAN</span>
            <span class="text-faint text-xs">/</span>
            <span class="text-xs text-muted">Konfigurasi Brand & Sistem</span>
        </div>
        <h1 class="font-heading text-2xl font-bold tracking-tight text-ink">Engine Pengaturan Brand & Sistem</h1>
        <p class="text-xs text-muted max-w-2xl leading-relaxed">
            Perbarui identitas brand, palet warna dinamis, nomor kontak WhatsApp, dan alamat workshop langsung tanpa mengubah baris kode.
        </p>
    </div>

    <!-- Live HTMX Notification Target -->
    <div id="settings-alert-target"></div>

    <form hx-post="<?= site_url('admin/settings/update') ?>"
          hx-target="#settings-alert-target"
          hx-swap="innerHTML"
          hx-boost="false"
          hx-select="unset"
          class="space-y-8">
        <?= csrf_field() ?>

        <!-- Brand Identity -->
        <div class="card p-6 sm:p-8 space-y-5">
            <div class="flex items-center justify-between border-b border-line pb-4">
                <span class="mono-label">IDENTITAS BRAND & LEGALITAS</span>
                <span class="text-[11px] text-faint font-mono">SEKSI 01</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                <div>
                    <label class="lbl">Nama Brand</label>
                    <input type="text" name="settings[brand][brand_name]" value="<?= esc($grouped['brand']['brand_name'] ?? '') ?>" class="field text-xs">
                </div>
                <div>
                    <label class="lbl">Nama Legal / Badan Usaha</label>
                    <input type="text" name="settings[brand][brand_legal_name]" value="<?= esc($grouped['brand']['brand_legal_name'] ?? '') ?>" class="field text-xs">
                </div>
                <div>
                    <label class="lbl">Tagline Brand</label>
                    <input type="text" name="settings[brand][brand_tagline]" value="<?= esc($grouped['brand']['brand_tagline'] ?? '') ?>" class="field text-xs">
                </div>
                <div>
                    <label class="lbl">Tahun Berdiri</label>
                    <input type="number" name="settings[brand][brand_founding_year]" min="1900" max="2100" value="<?= esc($grouped['brand']['brand_founding_year'] ?? '2016') ?>" class="field text-xs tabular-nums">
                </div>
                <div>
                    <label class="lbl">URL Logo (Image)</label>
                    <input type="text" name="settings[brand][brand_logo]" value="<?= esc($grouped['brand']['brand_logo'] ?? '') ?>" class="field text-xs">
                </div>
                <div>
                    <label class="lbl">URL Favicon</label>
                    <input type="text" name="settings[brand][brand_favicon]" value="<?= esc($grouped['brand']['brand_favicon'] ?? '') ?>" class="field text-xs">
                </div>
            </div>
        </div>

        <!-- Dynamic Theme Colors & Fonts -->
        <div class="card p-6 sm:p-8 space-y-5">
            <div class="flex items-center justify-between border-b border-line pb-4">
                <span class="mono-label">WARNA TEMA & TIPOGRAFI (DYNAMIC CSS)</span>
                <span class="text-[11px] text-faint font-mono">SEKSI 02</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-5">
                <div>
                    <label class="lbl">Color Primary (Brand)</label>
                    <input type="color" name="settings[theme][theme_color_primary]" value="<?= esc($grouped['theme']['theme_color_primary'] ?? '#0284c7') ?>" class="field !h-11 cursor-pointer p-1">
                </div>
                <div>
                    <label class="lbl">Color Secondary (Dark/Ink)</label>
                    <input type="color" name="settings[theme][theme_color_secondary]" value="<?= esc($grouped['theme']['theme_color_secondary'] ?? '#0f172a') ?>" class="field !h-11 cursor-pointer p-1">
                </div>
                <div>
                    <label class="lbl">Color Accent (Highlight)</label>
                    <input type="color" name="settings[theme][theme_color_accent]" value="<?= esc($grouped['theme']['theme_color_accent'] ?? '#f59e0b') ?>" class="field !h-11 cursor-pointer p-1">
                </div>
                <div>
                    <label class="lbl">Font Heading</label>
                    <input type="text" name="settings[theme][theme_font_heading]" value="<?= esc($grouped['theme']['theme_font_heading'] ?? 'Outfit') ?>" class="field text-xs">
                </div>
                <div>
                    <label class="lbl">Font Body</label>
                    <input type="text" name="settings[theme][theme_font_body]" value="<?= esc($grouped['theme']['theme_font_body'] ?? 'Plus Jakarta Sans') ?>" class="field text-xs">
                </div>
                <div>
                    <label class="lbl">Warna Tombol WhatsApp</label>
                    <input type="color" name="settings[theme][theme_color_cta_wa]" value="<?= esc($grouped['theme']['theme_color_cta_wa'] ?? '#25d366') ?>" class="field !h-11 cursor-pointer p-1">
                </div>
                <div class="col-span-full">
                    <label class="lbl">Google Fonts URL (@import)</label>
                    <input type="text" name="settings[theme][theme_font_google_import]" value="<?= esc($grouped['theme']['theme_font_google_import'] ?? '') ?>" class="field text-xs font-mono" placeholder="https://fonts.googleapis.com/css2?family=...">
                </div>
            </div>

            <div class="border-t border-line pt-4">
                <p class="mono-label text-[10px] mb-3">PALET TEMA — LIGHT MODE</p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <?php
                    $lightFields = [
                        'theme_color_bg_light'          => ['Background', '#f7f8fa'],
                        'theme_color_surface_light'     => ['Surface', '#ffffff'],
                        'theme_color_surface_alt_light' => ['Surface Alt', '#eef1f5'],
                        'theme_color_text_light'        => ['Teks Utama', '#0f172a'],
                        'theme_color_text_muted_light'  => ['Teks Redup', '#5b6676'],
                        'theme_color_text_faint_light'  => ['Teks Pudar', '#94a0b1'],
                        'theme_color_border_light'      => ['Border', '#dfe4eb'],
                    ];
                    foreach ($lightFields as $k => [$label, $def]): ?>
                        <div>
                            <label class="lbl text-[11px]"><?= $label ?></label>
                            <input type="color" name="settings[theme][<?= $k ?>]" value="<?= esc($grouped['theme'][$k] ?? $def) ?>" class="field !h-10 cursor-pointer p-1">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="border-t border-line pt-4">
                <p class="mono-label text-[10px] mb-3">PALET TEMA — DARK MODE</p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <?php
                    $darkFields = [
                        'theme_color_bg_dark'          => ['Background', '#0b0f16'],
                        'theme_color_surface_dark'     => ['Surface', '#111827'],
                        'theme_color_surface_alt_dark' => ['Surface Alt', '#0f172a'],
                        'theme_color_text_dark'        => ['Teks Utama', '#e6eaf0'],
                        'theme_color_text_muted_dark'  => ['Teks Redup', '#9aa6b8'],
                        'theme_color_text_faint_dark'  => ['Teks Pudar', '#64748b'],
                        'theme_color_border_dark'      => ['Border', '#1f2937'],
                    ];
                    foreach ($darkFields as $k => [$label, $def]): ?>
                        <div>
                            <label class="lbl text-[11px]"><?= $label ?></label>
                            <input type="color" name="settings[theme][<?= $k ?>]" value="<?= esc($grouped['theme'][$k] ?? $def) ?>" class="field !h-10 cursor-pointer p-1">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="card p-6 sm:p-8 space-y-5">
            <div class="flex items-center justify-between border-b border-line pb-4">
                <span class="mono-label">KONTAK, WHATSAPP & WORKSHOP</span>
                <span class="text-[11px] text-faint font-mono">SEKSI 03</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                <div>
                    <label class="lbl">Nomor WhatsApp Customer Service</label>
                    <input type="text" name="settings[contact][contact_whatsapp]" value="<?= esc($grouped['contact']['contact_whatsapp'] ?? '') ?>" class="field text-xs">
                </div>
                <div>
                    <label class="lbl">Telepon Kantor / Workshop</label>
                    <input type="text" name="settings[contact][contact_phone]" value="<?= esc($grouped['contact']['contact_phone'] ?? '') ?>" class="field text-xs">
                </div>
                <div>
                    <label class="lbl">Email Resmi</label>
                    <input type="email" name="settings[contact][contact_email]" value="<?= esc($grouped['contact']['contact_email'] ?? '') ?>" class="field text-xs">
                </div>
                <div>
                    <label class="lbl">Kota Workshop</label>
                    <input type="text" name="settings[contact][contact_city]" value="<?= esc($grouped['contact']['contact_city'] ?? '') ?>" class="field text-xs">
                </div>
                <div class="col-span-full">
                    <label class="lbl">Alamat Lengkap Workshop</label>
                    <input type="text" name="settings[contact][contact_address]" value="<?= esc($grouped['contact']['contact_address'] ?? '') ?>" class="field text-xs">
                </div>
                <div class="col-span-full">
                    <label class="lbl">Google Maps Embed URL</label>
                    <input type="url" name="settings[contact][contact_maps_embed]" value="<?= esc($grouped['contact']['contact_maps_embed'] ?? '') ?>" class="field text-xs font-mono" placeholder="https://www.google.com/maps/embed?pb=...">
                </div>
                <div class="col-span-full">
                    <label class="lbl">Jam Operasional (JSON)</label>
                    <textarea name="settings[contact][contact_opening_hours_json]" rows="3" class="field text-xs font-mono"><?= esc($grouped['contact']['contact_opening_hours_json'] ?? '') ?></textarea>
                </div>
                <div class="col-span-full">
                    <label class="lbl">Social Media Links (JSON)</label>
                    <textarea name="settings[contact][social_links_json]" rows="3" class="field text-xs font-mono"><?= esc($grouped['contact']['social_links_json'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Tentang Kami (About Page) -->
        <div class="card p-6 sm:p-8 space-y-5">
            <div class="flex items-center justify-between border-b border-line pb-4">
                <span class="mono-label">PROFIL TENTANG KAMI (/tentang-kami)</span>
                <span class="text-[11px] text-faint font-mono">SEKSI 04</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                <div>
                    <label class="lbl">Judul Halaman</label>
                    <input type="text" name="settings[about][about_title]" value="<?= esc($grouped['about']['about_title'] ?? '') ?>" class="field text-xs">
                </div>
                <div>
                    <label class="lbl">Deskripsi Singkat (SEO Meta)</label>
                    <input type="text" name="settings[about][about_short_desc]" value="<?= esc($grouped['about']['about_short_desc'] ?? '') ?>" class="field text-xs">
                </div>
                <div>
                    <label class="lbl">Visi Perusahaan</label>
                    <textarea name="settings[about][about_vision]" rows="2" class="field text-xs"><?= esc($grouped['about']['about_vision'] ?? '') ?></textarea>
                </div>
                <div>
                    <label class="lbl">Misi Perusahaan</label>
                    <textarea name="settings[about][about_mission]" rows="2" class="field text-xs"><?= esc($grouped['about']['about_mission'] ?? '') ?></textarea>
                </div>
                <div class="col-span-full">
                    <label class="lbl">Cerita Perjalanan (HTML)</label>
                    <textarea name="settings[about][about_story_html]" rows="5" class="field text-xs font-mono"><?= esc($grouped['about']['about_story_html'] ?? '') ?></textarea>
                </div>
                <div class="col-span-full">
                    <label class="lbl">Statistik Pencapaian (JSON)</label>
                    <textarea name="settings[about][about_stats_json]" rows="3" class="field text-xs font-mono"><?= esc($grouped['about']['about_stats_json'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Slider Mitra / Partner -->
        <div class="card p-6 sm:p-8 space-y-5">
            <div class="flex items-center justify-between border-b border-line pb-4">
                <span class="mono-label">SLIDER MITRA & PARTNER</span>
                <span class="text-[11px] text-faint font-mono">SEKSI 05</span>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:gap-5">
                <div class="col-span-full">
                    <label class="lbl">Daftar Mitra (JSON)</label>
                    <textarea name="settings[partners][partners_json]" rows="6" class="field text-xs font-mono leading-relaxed" placeholder='[{"name":"Nama Mitra","logo":"","url":""}]'><?= esc($grouped['partners']['partners_json'] ?? '') ?></textarea>
                    <p class="meta mt-1.5">Tampil sebagai slider di halaman Beranda & Tentang Kami. Kolom: <code>name</code> (wajib), <code>logo</code> (URL gambar, opsional), <code>url</code> (tautan, opsional).</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="btn-primary btn-sm px-6 gap-2">
                <?= lucide_icon('save', 'w-4 h-4') ?>
                <span>Simpan Semua Pengaturan</span>
            </button>
        </div>

    </form>
</div>
