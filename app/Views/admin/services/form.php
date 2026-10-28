<?php $isEdit = !empty($service); ?>

<div class="space-y-6 max-w-4xl">
    <div class="flex items-center justify-between">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="mono-label text-[10px]">02 · LAYANAN</span>
                <span class="text-faint text-xs">/</span>
                <a href="<?= site_url('admin/services') ?>" class="text-xs text-muted hover:text-ink">Layanan</a>
                <span class="text-faint text-xs">/</span>
                <span class="text-xs text-ink font-medium"><?= $isEdit ? 'Edit Layanan' : 'Tambah Baru' ?></span>
            </div>
            <h1 class="font-heading text-2xl font-bold tracking-tight text-ink">
                <?= $isEdit ? 'Edit Layanan: ' . esc($service['title']) : 'Tambah Layanan Baru' ?>
            </h1>
        </div>
        <a href="<?= site_url('admin/services') ?>" class="btn-outline btn-sm gap-1.5 text-xs">
            <?= lucide_icon('arrow-left', 'w-3.5 h-3.5') ?>
            <span>Kembali</span>
        </a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="p-4 bg-rose-500/10 border border-rose-500/30 text-rose-400 rounded-[12px] text-xs flex items-center gap-2.5">
            <?= lucide_icon('alert-circle', 'w-4 h-4 shrink-0') ?>
            <span><?= esc(session()->getFlashdata('error')) ?></span>
        </div>
    <?php endif; ?>

    <form action="<?= $isEdit ? site_url('admin/services/update/' . $service['id']) : site_url('admin/services/store') ?>" method="POST" class="space-y-6">
        <?= csrf_field() ?>

        <div class="card p-6 sm:p-8 space-y-5">
            <div class="flex items-center justify-between border-b border-line pb-4">
                <span class="mono-label">INFORMASI LAYANAN</span>
                <span class="text-[11px] text-faint font-mono">STEP 01</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                <div class="md:col-span-2">
                    <label class="lbl flex justify-between" for="srv-title">
                        <span>Nama Layanan *</span>
                        <span class="text-faint font-normal text-[11px]">Wajib diisi</span>
                    </label>
                    <input type="text" id="srv-title" name="title" required value="<?= esc($service['title'] ?? '') ?>"
                           class="field text-xs"
                           placeholder="Contoh: Custom Jersey Futsal Full Sublim">
                </div>

                <div>
                    <label class="lbl flex justify-between" for="srv-slug">
                        <span>Slug URL</span>
                        <span class="text-faint font-normal text-[11px]">Auto / Kustom</span>
                    </label>
                    <input type="text" id="srv-slug" name="slug" value="<?= esc($service['slug'] ?? '') ?>"
                           class="field text-xs font-mono"
                           placeholder="custom-jersey-futsal">
                </div>

                <div>
                    <label class="lbl flex justify-between" for="srv-icon">
                        <span>Ikon Lucide</span>
                        <a href="https://lucide.dev/icons" target="_blank" class="text-faint hover:text-ink text-[11px]">Daftar Ikon ↗</a>
                    </label>
                    <input type="text" id="srv-icon" name="icon" value="<?= esc($service['icon'] ?? 'shirt') ?>"
                           class="field text-xs font-mono"
                           placeholder="shirt, bike, trophy, zap ...">
                </div>

                <div>
                    <label class="lbl" for="srv-price">Harga Mulai (Rp)</label>
                    <input type="number" id="srv-price" name="starting_price" min="0" step="1000" value="<?= esc($service['starting_price'] ?? '0') ?>"
                           class="field text-xs tabular-nums">
                </div>

                <div>
                    <label class="lbl" for="srv-status">Status</label>
                    <select id="srv-status" name="status" class="field text-xs">
                        <option value="published" <?= ($service['status'] ?? 'published') === 'published' ? 'selected' : '' ?>>Tayang (Published)</option>
                        <option value="draft" <?= ($service['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="lbl" for="srv-img">URL Gambar Featured</label>
                    <input type="text" id="srv-img" name="featured_image" value="<?= esc($service['featured_image'] ?? '') ?>"
                           class="field text-xs"
                           placeholder="https://... atau /uploads/...">
                </div>

                <div class="md:col-span-2">
                    <label class="lbl" for="srv-desc">Deskripsi Singkat (Ringkasan)</label>
                    <textarea id="srv-desc" name="short_desc" rows="2"
                              class="field text-xs leading-relaxed"
                              placeholder="Deskripsi singkat yang tampil pada kartu layanan beranda..."><?= esc($service['short_desc'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="card p-6 sm:p-8 space-y-4">
            <div class="flex items-center justify-between border-b border-line pb-4">
                <span class="mono-label">KONTEN DETAIL HALAMAN LAYANAN</span>
                <span class="text-[11px] text-faint font-mono">STEP 02</span>
            </div>
            <textarea name="content" rows="10"
                      class="field text-xs font-mono leading-relaxed"
                      placeholder="Konten lengkap halaman layanan (mendukung HTML dasar)"><?= esc($service['content'] ?? '') ?></textarea>
        </div>

        <!-- SEO -->
        <div class="card p-6 sm:p-8 space-y-4">
            <div class="flex items-center justify-between border-b border-line pb-4">
                <span class="mono-label">OPTIMASI SEO META</span>
                <span class="text-[11px] text-faint font-mono">OPSIONAL</span>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="lbl flex justify-between">
                        <span>Meta Title</span>
                        <span class="text-faint text-[11px]">Max 70 char</span>
                    </label>
                    <input type="text" name="meta_title" maxlength="70" value="<?= esc($service['meta_title'] ?? '') ?>"
                           class="field text-xs"
                           placeholder="Otomatis dari nama layanan jika kosong">
                </div>
                <div>
                    <label class="lbl flex justify-between">
                        <span>Meta Description</span>
                        <span class="text-faint text-[11px]">Max 160 char</span>
                    </label>
                    <textarea name="meta_description" maxlength="160" rows="2"
                              class="field text-xs"
                              placeholder="Ringkasan layanan untuk Google Search snippet..."><?= esc($service['meta_description'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Multi-Lokasi (SEO Lokal) -->
        <div class="card p-6 sm:p-8 space-y-4">
            <div class="flex items-center justify-between border-b border-line pb-4">
                <span class="mono-label">LOKASI — MULTI URL SEO LOKAL</span>
                <span class="text-[11px] text-faint font-mono">1 LAYANAN → BANYAK KOTA</span>
            </div>
            <p class="text-xs text-muted">Centang kota/kabupaten agar layanan ini punya halaman
                <span class="font-mono text-[11px]">/layanan/<?= esc($service['slug'] ?? '{slug-layanan}') ?>-{kota}</span>.
                Untuk kustomisasi judul &amp; meta per kota, gunakan tombol <strong>Lokasi SEO</strong> di daftar layanan.</p>
            <?php if (!empty($allLocations)): ?>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
                    <?php foreach ($allLocations as $loc): ?>
                        <label class="flex items-center gap-2 p-2.5 rounded-[10px] border border-line bg-[var(--color-surface-alt)]/50 hover:bg-[var(--color-surface-alt)] cursor-pointer transition-colors">
                            <input type="checkbox" name="location_ids[]" value="<?= $loc['id'] ?>"
                                   <?= in_array((int) $loc['id'], $attachedLocationIds, true) ? 'checked' : '' ?>
                                   class="rounded border-faint text-ink focus:ring-0 bg-transparent">
                            <span class="text-[12px] text-ink"><?= esc($loc['name']) ?></span>
                            <span class="text-[10px] text-faint font-mono ml-auto"><?= esc(ucfirst($loc['type'])) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-xs text-muted">Belum ada data lokasi. Tambahkan kota lewat menu <a href="<?= site_url('admin/locations') ?>" class="text-ink underline">Lokasi (SEO Lokal)</a>.</p>
            <?php endif; ?>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="btn-primary btn-sm px-6 gap-2">
                <?= lucide_icon('save', 'w-4 h-4') ?>
                <span><?= $isEdit ? 'Simpan Perubahan' : 'Simpan Layanan' ?></span>
            </button>
            <a href="<?= site_url('admin/services') ?>" class="btn-outline btn-sm px-5">
                Batal
            </a>
        </div>
    </form>
</div>
