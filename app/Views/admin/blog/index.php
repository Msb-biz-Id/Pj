<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="mono-label text-[10px]">02 · ARTIKEL</span>
                <span class="text-faint text-xs">/</span>
                <span class="text-xs text-muted">Daftar Tulisan</span>
            </div>
            <h1 class="font-heading text-2xl font-bold tracking-tight text-ink">Manajemen Artikel Blog</h1>
            <p class="text-xs text-muted">Kelola artikel panduan jersey, tips perawatan apparel olahraga, dan konten SEO.</p>
        </div>
        <a href="<?= site_url('admin/blog/create') ?>" class="btn-primary btn-sm gap-2">
            <?= lucide_icon('plus', 'w-4 h-4') ?>
            <span>Tulis Artikel</span>
        </a>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[var(--color-surface-alt)] text-muted font-semibold border-b border-line">
                        <th class="p-4 mono-label text-[10px]">JUDUL ARTIKEL</th>
                        <th class="p-4 mono-label text-[10px]">KATEGORI</th>
                        <th class="p-4 mono-label text-[10px]">STATUS</th>
                        <th class="p-4 mono-label text-[10px]">TANGGAL</th>
                        <th class="p-4 text-right mono-label text-[10px]">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    <?php if (!empty($posts)): ?>
                        <?php foreach ($posts as $p): ?>
                            <tr class="hover:bg-[var(--color-surface-alt)]/50 transition-colors">
                                <td class="p-4">
                                    <span class="font-heading font-semibold text-ink text-[13px] block"><?= esc($p['title']) ?></span>
                                    <span class="font-mono text-[10px] text-faint">/blog/<?= esc($p['slug']) ?></span>
                                </td>
                                <td class="p-4 text-muted">
                                    <span class="tag text-[10px]"><?= esc($p['category_name'] ?? 'Umum') ?></span>
                                </td>
                                <td class="p-4">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-semibold
                                        <?php if ($p['status'] === 'published'): ?>bg-emerald-500/10 text-emerald-500 border border-emerald-500/30
                                        <?php elseif ($p['status'] === 'draft'): ?>bg-amber-500/10 text-amber-500 border border-amber-500/30
                                        <?php else: ?>bg-[var(--color-surface-alt)] text-faint border border-line<?php endif; ?>">
                                        <span class="w-1.5 h-1.5 rounded-full <?= $p['status'] === 'published' ? 'bg-emerald-500' : ($p['status'] === 'draft' ? 'bg-amber-500' : 'bg-faint') ?>"></span>
                                        <?= strtoupper(esc($p['status'])) ?>
                                    </span>
                                </td>
                                <td class="p-4 text-faint font-mono text-[11px]">
                                    <?= date('d M Y', strtotime($p['created_at'])) ?>
                                </td>
                                <td class="p-4 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="<?= base_url('blog/' . esc($p['slug'])) ?>" target="_blank" hx-boost="false" class="icon-btn !h-8 !w-8 hover:text-ink" title="Lihat di Frontend">
                                            <?= lucide_icon('external-link', 'w-3.5 h-3.5') ?>
                                        </a>
                                        <a href="<?= site_url('admin/blog/edit/' . $p['id']) ?>" class="icon-btn !h-8 !w-8 hover:text-ink" title="Edit">
                                            <?= lucide_icon('edit', 'w-3.5 h-3.5') ?>
                                        </a>
                                        <form action="<?= site_url('admin/blog/delete/' . $p['id']) ?>" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus artikel ini?')">
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
                            <td colspan="5" class="p-12 text-center text-muted">
                                <?= lucide_icon('file-text', 'w-8 h-8 mx-auto text-faint mb-3 opacity-50') ?>
                                <p class="text-sm">Belum ada artikel yang diterbitkan.</p>
                                <a href="<?= site_url('admin/blog/create') ?>" class="btn-primary btn-sm mt-3 inline-flex">Tulis Artikel Pertama</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
