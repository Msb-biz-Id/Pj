<?php $geoBase = '/layanan/' . $service['slug'] . '-'; ?>
<div class="space-y-6 max-w-5xl">
    <div class="flex items-center justify-between">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="mono-label text-[10px]">02 · LAYANAN</span>
                <span class="text-faint text-xs">/</span>
                <a href="<?= site_url('admin/services') ?>" class="text-xs text-muted hover:text-ink">Layanan</a>
                <span class="text-faint text-xs">/</span>
                <span class="text-xs text-ink font-medium">Lokasi SEO</span>
            </div>
            <h1 class="font-heading text-2xl font-bold tracking-tight text-ink">Lokasi SEO: <?= esc($service['title']) ?></h1>
            <p class="text-xs text-muted">Satu layanan → banyak URL kota. Setiap lokasi yang dipasang menghasilkan halaman
                <span class="font-mono text-[11px]"><?= esc($geoBase) ?>{kota}</span> dengan judul &amp; meta yang bisa dikustomisasi.</p>
        </div>
        <a href="<?= site_url('admin/services') ?>" class="btn-outline btn-sm gap-1.5 text-xs">
            <?= lucide_icon('arrow-left', 'w-3.5 h-3.5') ?>
            <span>Kembali</span>
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-500 rounded-[12px] text-xs flex items-center gap-2.5">
            <?= lucide_icon('check-circle', 'w-4 h-4 shrink-0') ?>
            <span><?= esc(session()->getFlashdata('success')) ?></span>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="p-4 bg-rose-500/10 border border-rose-500/30 text-rose-400 rounded-[12px] text-xs flex items-center gap-2.5">
            <?= lucide_icon('alert-circle', 'w-4 h-4 shrink-0') ?>
            <span><?= esc(session()->getFlashdata('error')) ?></span>
        </div>
    <?php endif; ?>

    <!-- Pasang Lokasi Baru -->
    <form action="<?= site_url('admin/services/locations/' . $service['id'] . '/attach') ?>" method="POST" class="card p-6 sm:p-8">
        <?= csrf_field() ?>
        <div class="flex items-center justify-between border-b border-line pb-4 mb-5">
            <span class="mono-label">PASANG LOKASI BARU</span>
            <span class="text-[11px] text-faint font-mono"><?= count($available) ?> BELUM TERPAKAI</span>
        </div>
        <?php if (!empty($available)): ?>
            <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                <div class="flex-1">
                    <label class="lbl" for="loc-attach">Pilih Kota / Kabupaten</label>
                    <select id="loc-attach" name="location_id" class="field text-xs" required>
                        <?php foreach ($available as $loc): ?>
                            <option value="<?= $loc['id'] ?>"><?= esc($loc['name']) ?> (<?= esc($loc['province'] ?? '—') ?>) — <?= esc($loc['type']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn-primary btn-sm gap-2">
                    <?= lucide_icon('link', 'w-4 h-4') ?>
                    <span>Pasang &amp; Buat URL</span>
                </button>
            </div>
        <?php else: ?>
            <p class="text-xs text-muted">Semua lokasi sudah terpasang. Tambah kota baru lewat menu <a href="<?= site_url('admin/locations') ?>" class="text-ink underline">Lokasi (SEO Lokal)</a>.</p>
        <?php endif; ?>
    </form>

    <!-- Daftar Lokasi Terpasang + Form SEO per Kota -->
    <?php if (!empty($attached)): ?>
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <span class="mono-label">HALAMAN GEO AKTIF (<?= count($attached) ?>)</span>
                <span class="text-[11px] text-faint font-mono">KOSONGKAN = PAKAI DEFAULT OTOMATIS</span>
            </div>

            <?php foreach ($attached as $row): ?>
                <div class="card p-6 sm:p-8 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-line pb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-[10px] bg-[var(--color-surface-alt)] text-ink flex items-center justify-center border border-line">
                                <?= lucide_icon('map-pin', 'w-4 h-4') ?>
                            </div>
                            <div>
                                <p class="font-heading font-semibold text-ink text-[13px]"><?= esc($row['loc_name']) ?>
                                    <span class="text-[10px] font-mono text-faint ml-1"><?= esc($row['loc_province'] ?? '') ?></span>
                                </p>
                                <a href="<?= base_url($geoBase . $row['loc_slug']) ?>" target="_blank" hx-boost="false" class="font-mono text-[11px] text-muted hover:text-ink inline-flex items-center gap-1">
                                    <?= esc($geoBase . $row['loc_slug']) ?> <?= lucide_icon('external-link', 'w-3 h-3') ?>
                                </a>
                            </div>
                        </div>
                        <form action="<?= site_url('admin/services/locations/' . $service['id'] . '/detach/' . $row['location_id']) ?>" method="POST" onsubmit="return confirm('Lepas lokasi <?= esc($row['loc_name']) ?>? Halaman geo ini akan berhenti tampil & hilang dari sitemap.')">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn-outline btn-sm gap-1.5 text-xs !text-rose-400 hover:!border-rose-500/50">
                                <?= lucide_icon('unlink', 'w-3.5 h-3.5') ?>
                                <span>Lepas</span>
                            </button>
                        </form>
                    </div>

                    <form action="<?= site_url('admin/services/locations/' . $service['id'] . '/update/' . $row['location_id']) ?>" method="POST" class="space-y-4">
                        <?= csrf_field() ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="lbl">Judul Halaman (H1) — Custom</label>
                                <input type="text" name="custom_title" maxlength="255" value="<?= esc($row['custom_title'] ?? '') ?>"
                                       class="field text-xs" placeholder="Default: <?= esc($service['title'] . ' ' . $row['loc_name']) ?>">
                            </div>
                            <div class="md:col-span-2">
                                <label class="lbl">Konten Lokal (HTML dasar) — Custom</label>
                                <textarea name="custom_content" rows="4" class="field text-xs font-mono leading-relaxed"
                                          placeholder="Default: intro otomatis + konten dasar layanan. Isi untuk mengoverride tampilan konten di kota ini."><?= esc($row['custom_content'] ?? '') ?></textarea>
                            </div>
                            <div>
                                <label class="lbl">Meta Title</label>
                                <input type="text" name="meta_title" maxlength="255" value="<?= esc($row['meta_title'] ?? '') ?>"
                                       class="field text-xs" placeholder="Default: <?= esc($service['title'] . ' ' . $row['loc_name'] . ' | Pusat Jersey') ?>">
                            </div>
                            <div>
                                <label class="lbl">Meta Description</label>
                                <textarea name="meta_description" maxlength="160" rows="2" class="field text-xs"
                                          placeholder="Default: deskripsi otomatis dengan nama kota."><?= esc($row['meta_description'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="btn-primary btn-sm gap-1.5">
                                <?= lucide_icon('save', 'w-3.5 h-3.5') ?>
                                <span>Simpan SEO <?= esc($row['loc_name']) ?></span>
                            </button>
                        </div>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="card p-12 text-center text-muted">
            <?= lucide_icon('map-pin', 'w-8 h-8 mx-auto text-faint mb-3 opacity-50') ?>
            <p class="text-sm">Belum ada lokasi terpasang untuk layanan ini.</p>
            <p class="text-xs mt-1">Pasang kota lewat form di atas untuk membuat halaman geo per kota.</p>
        </div>
    <?php endif; ?>
</div>