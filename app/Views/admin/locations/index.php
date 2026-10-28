<div class="space-y-6" x-data="{ openEdit: null }">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="mono-label text-[10px]">02 · LOKASI</span>
                <span class="text-faint text-xs">/</span>
                <span class="text-xs text-muted">Master Kota / Kabupaten</span>
            </div>
            <h1 class="font-heading text-2xl font-bold tracking-tight text-ink">Manajemen Lokasi (SEO Lokal)</h1>
            <p class="text-xs text-muted">Kelola daftar kota/kabupaten. Setiap lokasi bisa dipakai untuk melipatgandakan URL layanan (contoh: <span class="font-mono text-[11px]">/layanan/jasa-custom-jersey-malang</span>).</p>
        </div>
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

    <!-- Form Tambah Lokasi -->
    <form action="<?= site_url('admin/locations/store') ?>" method="POST" class="card p-6 sm:p-8">
        <?= csrf_field() ?>
        <div class="flex items-center justify-between border-b border-line pb-4 mb-5">
            <span class="mono-label">TAMBAH LOKASI BARU</span>
            <span class="text-[11px] text-faint font-mono">KOTA / KABUPATEN</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="lbl" for="loc-name">Nama Lokasi *</label>
                <input type="text" id="loc-name" name="name" required class="field text-xs" placeholder="Contoh: Lamongan">
            </div>
            <div>
                <label class="lbl" for="loc-prov">Provinsi</label>
                <input type="text" id="loc-prov" name="province" class="field text-xs" placeholder="Contoh: Jawa Timur">
            </div>
            <div>
                <label class="lbl" for="loc-type">Tipe *</label>
                <select id="loc-type" name="type" class="field text-xs">
                    <option value="kota">Kota</option>
                    <option value="kabupaten">Kabupaten</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="btn-primary btn-sm w-full justify-center gap-2">
                    <?= lucide_icon('plus', 'w-4 h-4') ?>
                    <span>Tambah</span>
                </button>
            </div>
        </div>
        <p class="meta mt-3">Slug URL dibuat otomatis dari nama (huruf kecil & strip). Nama unik per lokasi.</p>
    </form>

    <!-- Tabel Lokasi -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[var(--color-surface-alt)] text-muted font-semibold border-b border-line">
                        <th class="p-4 mono-label text-[10px]">NAMA</th>
                        <th class="p-4 mono-label text-[10px]">SLUG URL</th>
                        <th class="p-4 mono-label text-[10px]">PROVINSI</th>
                        <th class="p-4 mono-label text-[10px]">TIPE</th>
                        <th class="p-4 mono-label text-[10px]">LAYANAN</th>
                        <th class="p-4 text-right mono-label text-[10px]">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    <?php if (!empty($locations)): ?>
                        <?php foreach ($locations as $loc): ?>
                            <tr class="hover:bg-[var(--color-surface-alt)]/50 transition-colors align-top">
                                <td class="p-4 font-heading font-semibold text-ink text-[13px]">
                                    <div class="flex items-center gap-2">
                                        <?= lucide_icon('map-pin', 'w-3.5 h-3.5 text-faint') ?>
                                        <?= esc($loc['name']) ?>
                                    </div>
                                </td>
                                <td class="p-4 font-mono text-faint text-[11px]"><?= esc($loc['slug']) ?></td>
                                <td class="p-4 text-muted text-[12px]"><?= esc($loc['province'] ?? '—') ?></td>
                                <td class="p-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-mono font-semibold bg-[var(--color-surface-alt)] text-muted border border-line"><?= esc(ucfirst($loc['type'])) ?></span>
                                </td>
                                <td class="p-4">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-semibold <?= ($usageMap[$loc['id']] ?? 0) > 0 ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/30' : 'bg-[var(--color-surface-alt)] text-faint border border-line' ?>">
                                        <?= (int) ($usageMap[$loc['id']] ?? 0) ?> layanan
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <button type="button" @click="openEdit === <?= $loc['id'] ?> ? openEdit = null : openEdit = <?= $loc['id'] ?>" class="icon-btn !h-8 !w-8 hover:text-ink" title="Edit">
                                            <?= lucide_icon('edit', 'w-3.5 h-3.5') ?>
                                        </button>
                                        <form action="<?= site_url('admin/locations/delete/' . $loc['id']) ?>" method="POST" class="inline" onsubmit="return confirm('Hapus lokasi <?= esc($loc['name']) ?>? Seluruh halaman geo layanan di kota ini ikut dihapus.')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="icon-btn !h-8 !w-8 hover:border-rose-500/50 hover:text-rose-400" title="Hapus">
                                                <?= lucide_icon('trash-2', 'w-3.5 h-3.5') ?>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="p-12 text-center text-muted">
                                <?= lucide_icon('map-pin', 'w-8 h-8 mx-auto text-faint mb-3 opacity-50') ?>
                                <p class="text-sm">Belum ada lokasi. Tambahkan kota pertama lewat form di atas.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>