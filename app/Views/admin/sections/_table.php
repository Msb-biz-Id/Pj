<?php
$sectionLabels = [
    'hero' => 'Hero Banner', 'trust_badges' => 'Trust Badges',
    'services' => 'Layanan Custom', 'products' => 'Katalog Produk', 'portfolio_gallery' => 'Galeri Portofolio',
    'testimonials' => 'Testimoni Klien', 'partners' => 'Slider Mitra', 'blog_latest' => 'Artikel Terbaru', 'locations_grid' => 'Jangkauan Kota',
    'cta_wa' => 'CTA WhatsApp'
];
?>
<div id="sections-table-container" class="space-y-4">
    <div class="space-y-2.5" id="sortable-sections">
        <?php foreach ($sections as $sec): ?>
            <div data-id="<?= $sec['id'] ?>" class="card p-4 sm:p-5 flex items-center justify-between transition-colors hover:border-ink/30 select-none">
                <div class="flex items-center space-x-4">
                    <span class="cursor-grab text-faint hover:text-ink transition-colors p-1" title="Tarik untuk memindahkan urutan">
                        <?= lucide_icon('grip-vertical', 'w-5 h-5') ?>
                    </span>
                    <span class="w-8 h-8 rounded-[8px] bg-[var(--color-surface-alt)] text-ink font-mono font-bold flex items-center justify-center text-xs border border-line">
                        <?= $sec['sort_order'] ?>
                    </span>
                    <div>
                        <div class="font-heading font-semibold text-ink text-sm flex items-center gap-2">
                            <span><?= esc($sectionLabels[$sec['section_key']] ?? ucfirst($sec['section_key'])) ?></span>
                            <code class="text-[10px] text-faint bg-[var(--color-surface-alt)] px-2 py-0.5 rounded font-mono border border-line"><?= esc($sec['section_key']) ?></code>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <!-- Toggle Active Button -->
                    <button type="button" 
                            hx-post="<?= site_url('admin/sections/toggle/' . $sec['id']) ?>" 
                            hx-target="#sections-table-container" 
                            hx-swap="outerHTML" 
                            hx-select="unset"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-mono font-semibold transition-colors <?= $sec['is_active'] ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/30' : 'bg-[var(--color-surface-alt)] text-faint border border-line' ?>">
                        <?= lucide_icon($sec['is_active'] ? 'eye' : 'eye-off', 'w-3.5 h-3.5') ?>
                        <span><?= $sec['is_active'] ? 'TAMPIL' : 'SEMBUNYI' ?></span>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
if (typeof Sortable !== 'undefined') {
    const el = document.getElementById('sortable-sections');
    if (el) {
        new Sortable(el, {
            animation: 150,
            handle: '.cursor-grab',
            onEnd: function (evt) {
                let order = [];
                document.querySelectorAll('#sortable-sections [data-id]').forEach((item) => {
                    order.push(item.getAttribute('data-id'));
                });
                
                htmx.ajax('POST', '<?= site_url('admin/sections/reorder') ?>', {
                    target: '#sections-table-container',
                    swap: 'outerHTML',
                    select: '#sections-table-container',
                    values: { order: order }
                });
            }
        });
    }
}
</script>
