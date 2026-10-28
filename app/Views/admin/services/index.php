<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="mono-label text-[10px]">02 · LAYANAN</span>
                <span class="text-faint text-xs">/</span>
                <span class="text-xs text-muted">Daftar Layanan</span>
            </div>
            <h1 class="font-heading text-2xl font-bold tracking-tight text-ink">Manajemen Layanan Konveksi</h1>
            <p class="text-xs text-muted">Kelola jenis layanan cetak jersey custom (futsal, sepakbola, basket, sepeda, dll.).</p>
        </div>
        <a href="<?= site_url('admin/services/create') ?>" class="btn-primary btn-sm gap-2">
            <?= lucide_icon('plus', 'w-4 h-4') ?>
            <span>Tambah Layanan</span>
        </a>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[var(--color-surface-alt)] text-muted font-semibold border-b border-line">
                        <th class="p-4 w-12 mono-label text-[10px]">IKON</th>
                        <th class="p-4 mono-label text-[10px]">NAMA LAYANAN</th>
                        <th class="p-4 mono-label text-[10px]">SLUG URL</th>
                        <th class="p-4 mono-label text-[10px]">HARGA MULAI</th>
                        <th class="p-4 mono-label text-[10px]">LOKASI</th>
                        <th class="p-4 mono-label text-[10px]">STATUS</th>
                        <th class="p-4 text-right mono-label text-[10px]">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    <?php if (!empty($services)): ?>
                        <?php foreach ($services as $s): ?>
                            <tr class="hover:bg-[var(--color-surface-alt)]/50 transition-colors">
                                <td class="p-4">
                                    <div class="w-9 h-9 rounded-[10px] bg-[var(--color-surface-alt)] text-ink flex items-center justify-center border border-line">
                                        <?= lucide_icon($s['icon'] ?? 'shirt', 'w-4 h-4') ?>
                                    </div>
                                </td>
                                <td class="p-4 font-heading font-semibold text-ink text-[13px]"><?= esc($s['title']) ?></td>
                                <td class="p-4 font-mono text-faint text-[11px]">/layanan/<?= esc($s['slug']) ?></td>
                                <td class="p-4 font-heading font-semibold text-ink tabular-nums text-[13px]">
                                    Rp <?= number_format($s['starting_price'], 0, ',', '.') ?>
                                </td>
                                <td class="p-4">
                                    <?php $locTotal = $locationCountMap[$s['id']] ?? 0; ?>
                                    <a href="<?= site_url('admin/services/locations/' . $s['id']) ?>" title="Kelola Lokasi SEO"
                                       class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-semibold transition-colors <?= $locTotal > 0 ? 'bg-sky-500/10 text-sky-500 border border-sky-500/30 hover:bg-sky-500/20' : 'bg-[var(--color-surface-alt)] text-faint border border-line hover:text-ink' ?>">
                                        <?= lucide_icon('map-pin', 'w-3 h-3') ?>
                                        <?= $locTotal ?> kota
                                    </a>
                                </td>
                                <td class="p-4">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-semibold <?= $s['status'] === 'published' ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/30' : 'bg-[var(--color-surface-alt)] text-faint border border-line' ?>">
                                        <span class="w-1.5 h-1.5 rounded-full <?= $s['status'] === 'published' ? 'bg-emerald-500' : 'bg-faint' ?>"></span>
                                        <?= $s['status'] === 'published' ? 'TAYANG' : strtoupper(esc($s['status'])) ?>
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="<?= base_url('layanan/' . esc($s['slug'])) ?>" target="_blank" hx-boost="false" class="icon-btn !h-8 !w-8 hover:text-ink" title="Lihat Layanan">
                                            <?= lucide_icon('external-link', 'w-3.5 h-3.5') ?>
                                        </a>
                                        <a href="<?= site_url('admin/services/edit/' . $s['id']) ?>" class="icon-btn !h-8 !w-8 hover:text-ink" title="Edit">
                                            <?= lucide_icon('edit', 'w-3.5 h-3.5') ?>
                                        </a>
                                        <a href="<?= site_url('admin/services/locations/' . $s['id']) ?>" class="icon-btn !h-8 !w-8 hover:text-ink" title="Kelola Lokasi SEO">
                                            <?= lucide_icon('map-pin', 'w-3.5 h-3.5') ?>
                                        </a>
                                        <form action="<?= site_url('admin/services/delete/' . $s['id']) ?>" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus layanan ini?')">
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
                            <td colspan="7" class="p-12 text-center text-muted">
                                <?= lucide_icon('shirt', 'w-8 h-8 mx-auto text-faint mb-3 opacity-50') ?>
                                <p class="text-sm">Belum ada layanan konveksi yang terdaftar.</p>
                                <a href="<?= site_url('admin/services/create') ?>" class="btn-primary btn-sm mt-3 inline-flex">Tambah Layanan Pertama</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
