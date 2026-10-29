<?php
/**
 * Fragment HTMX load-more /bio.
 * Data: $bioType (products|services), $bioItems, $bioTotal, $bioNext.
 * Semua link membuka tab baru (target="_blank").
 */
$type  = $bioType ?? 'products';
$items = $bioItems ?? [];
$total = (int) ($bioTotal ?? 0);
$next  = (int) ($bioNext ?? 0);
$limit = 6;
$hasMore = $next < $total && !empty($items);
$isService = $type === 'services';
?>
<?php if ($isService): ?>
    <?php foreach ($items as $svc): ?>
        <li>
            <a href="/layanan/<?= esc($svc['slug']) ?>" target="_blank" rel="noopener" class="flex items-center gap-3 px-4 py-3.5 transition-colors hover:bg-[var(--color-surface-alt)]/50">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[var(--color-surface-alt)] text-ink"><?= lucide_icon('shirt', 'w-4 h-4') ?></span>
                <span class="min-w-0 flex-1">
                    <span class="block truncate text-[14px] font-semibold text-ink"><?= esc($svc['title']) ?></span>
                    <span class="meta mt-0.5 line-clamp-1 block"><?= esc($svc['short_desc'] ?? '') ?></span>
                </span>
                <span class="shrink-0 text-right">
                    <span class="meta block">mulai</span>
                    <span class="block text-[12.5px] font-semibold text-ink tabular-nums">Rp <?= number_format((float) ($svc['starting_price'] ?? 0), 0, ',', '.') ?></span>
                </span>
            </a>
        </li>
    <?php endforeach; ?>
<?php else: ?>
    <?php foreach ($items as $product): ?>
        <article class="card group flex shrink-0 snap-start basis-[70%] flex-col sm:shrink sm:basis-auto" data-reveal>
            <a href="/produk/<?= esc($product['slug']) ?>" target="_blank" rel="noopener" class="card-media aspect-[4/3]" tabindex="-1" aria-hidden="true">
                <img src="<?= esc(!empty($product['featured_image']) ? $product['featured_image'] : 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=800&q=80') ?>" alt="<?= esc($product['name'] ?? 'Produk') ?>" loading="lazy">
            </a>
            <div class="flex flex-1 flex-col gap-1.5 p-4">
                <p class="meta text-[11px] uppercase tracking-[0.12em]"><?= esc($product['category_name'] ?? 'Jersey') ?></p>
                <a href="/produk/<?= esc($product['slug']) ?>" target="_blank" rel="noopener" class="card-title line-clamp-2 text-[13.5px]"><?= esc($product['name'] ?? 'Produk') ?></a>
                <span class="price mt-1">Rp <?= number_format((float) ($product['discount_price'] ?? $product['price'] ?? 0), 0, ',', '.') ?></span>
            </div>
        </article>
    <?php endforeach; ?>
<?php endif; ?>

<?php if ($hasMore): ?>
    <<?= $isService ? 'li' : 'div' ?> class="<?= $isService ? 'border-t border-line' : 'col-span-full flex justify-center pt-1' ?>">
        <button type="button"
                hx-get="<?= esc(site_url('bio/items') . '?type=' . urlencode($type) . '&offset=' . $next . '&limit=' . $limit) ?>"
                hx-target="#bio-list-<?= esc($type) ?>"
                hx-swap="beforeend"
                hx-on::after-request="this.parentElement.remove()"
                class="btn-outline !h-9 !px-4 text-[12.5px]">
            Muat lebih banyak
        </button>
    </<?= $isService ? 'li' : 'div' ?>>
<?php endif; ?>
