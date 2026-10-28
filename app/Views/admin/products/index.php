<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="mono-label text-[10px]">02 · KATALOG</span>
                <span class="text-faint text-xs">/</span>
                <span class="text-xs text-muted">Daftar Produk</span>
            </div>
            <h1 class="font-heading text-2xl font-bold tracking-tight text-ink">Manajemen Produk & Apparel</h1>
            <p class="text-xs text-muted">Kelola koleksi jersey custom sublimasi dan produk authentic official tim.</p>
        </div>
        <a href="<?= site_url('admin/products/create') ?>" class="btn-primary btn-sm gap-2">
            <?= lucide_icon('plus', 'w-4 h-4') ?>
            <span>Tambah Produk</span>
        </a>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[var(--color-surface-alt)] text-muted font-semibold border-b border-line">
                        <th class="p-4 w-16 mono-label text-[10px]">FOTO</th>
                        <th class="p-4 mono-label text-[10px]">NAMA PRODUK</th>
                        <th class="p-4 mono-label text-[10px]">KATEGORI</th>
                        <th class="p-4 mono-label text-[10px]">HARGA SATUAN</th>
                        <th class="p-4 mono-label text-[10px]">TIPE</th>
                        <th class="p-4 mono-label text-[10px]">STATUS</th>
                        <th class="p-4 text-right mono-label text-[10px]">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $p): ?>
                            <tr class="hover:bg-[var(--color-surface-alt)]/50 transition-colors">
                                <td class="p-4">
                                    <img src="<?= esc($p['featured_image'] ?? 'https://placehold.co/100x100/1e293b/94a3b8?text=No+Img') ?>"
                                         alt="<?= esc($p['name']) ?>"
                                         class="w-12 h-12 object-cover rounded-[10px] border border-line bg-[var(--color-surface-alt)]">
                                </td>
                                <td class="p-4">
                                    <div class="font-heading font-semibold text-ink text-[13px]"><?= esc($p['name']) ?></div>
                                    <?php if ($p['sku']): ?>
                                        <span class="inline-block text-[10px] text-faint font-mono mt-0.5">SKU: <?= esc($p['sku']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4 text-muted">
                                    <span class="tag text-[10px]"><?= esc($p['category_name'] ?? 'Umum') ?></span>
                                </td>
                                <td class="p-4">
                                    <span class="font-heading font-semibold text-ink tabular-nums text-[13px]">
                                        Rp <?= number_format($p['discount_price'] ?? $p['price'], 0, ',', '.') ?>
                                    </span>
                                    <?php if (!empty($p['discount_price'])): ?>
                                        <span class="block text-[11px] text-faint line-through tabular-nums">
                                            Rp <?= number_format($p['price'], 0, ',', '.') ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4">
                                    <div class="flex flex-wrap gap-1">
                                        <?php if ($p['is_custom']): ?>
                                            <span class="tag text-[10px] bg-sky-500/10 text-sky-500 border-sky-500/30">Custom</span>
                                        <?php endif; ?>
                                        <?php if ($p['is_affiliate']): ?>
                                            <span class="tag text-[10px] bg-amber-500/10 text-amber-500 border-amber-500/30">Affiliate</span>
                                        <?php endif; ?>
                                        <?php if ($p['shopee_link']): ?>
                                            <span class="tag text-[10px] bg-orange-500/10 text-orange-500 border-orange-500/30">Shopee</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-semibold <?= $p['status'] === 'published' ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/30' : 'bg-[var(--color-surface-alt)] text-faint border border-line' ?>">
                                        <span class="w-1.5 h-1.5 rounded-full <?= $p['status'] === 'published' ? 'bg-emerald-500' : 'bg-faint' ?>"></span>
                                        <?= $p['status'] === 'published' ? 'TAYANG' : strtoupper(esc($p['status'])) ?>
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="<?= base_url('produk/' . esc($p['slug'])) ?>" target="_blank" hx-boost="false" class="icon-btn !h-8 !w-8 hover:text-ink" title="Lihat di Frontend">
                                            <?= lucide_icon('external-link', 'w-3.5 h-3.5') ?>
                                        </a>
                                        <a href="<?= site_url('admin/products/edit/' . $p['id']) ?>" class="icon-btn !h-8 !w-8 hover:text-ink" title="Edit">
                                            <?= lucide_icon('edit', 'w-3.5 h-3.5') ?>
                                        </a>
                                        <form action="<?= site_url('admin/products/delete/' . $p['id']) ?>" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
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
                                <?= lucide_icon('shopping-bag', 'w-8 h-8 mx-auto text-faint mb-3 opacity-50') ?>
                                <p class="text-sm">Belum ada produk yang terdaftar.</p>
                                <a href="<?= site_url('admin/products/create') ?>" class="btn-primary btn-sm mt-3 inline-flex">Tambah Produk Pertama</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
