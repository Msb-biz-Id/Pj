<?php
// Kartu produk editorial: media 4/3, hairline, tanpa badge warna-warni.
$name = $product['name'] ?? 'Produk';
$slug = $product['slug'] ?? '#';
$cat  = $product['category_name'] ?? 'Jersey';
$img  = !empty(trim($product['featured_image'] ?? '')) ? $product['featured_image'] : 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=800&q=80';
$priceNow = (float)($product['discount_price'] ?? $product['price'] ?? 0);
$priceOld = (float)($product['price'] ?? 0);
?>
<article class="card group flex flex-col" data-reveal>
  <a href="/produk/<?= esc($slug) ?>" class="card-media aspect-[4/3]" tabindex="-1" aria-hidden="true">
    <img src="<?= esc($img) ?>" alt="<?= esc($name) ?>" loading="lazy">
  </a>
  <div class="flex flex-1 flex-col gap-2 p-5">
    <p class="meta uppercase tracking-[0.12em] text-[11px]"><?= esc($cat) ?></p>
    <a href="/produk/<?= esc($slug) ?>" class="card-title line-clamp-2"><?= esc($name) ?></a>
    <div class="mt-1 flex items-baseline gap-2">
      <span class="price">Rp <?= number_format($priceNow, 0, ',', '.') ?></span>
      <?php if ($priceOld > $priceNow): ?>
        <span class="price-strike">Rp <?= number_format($priceOld, 0, ',', '.') ?></span>
      <?php endif; ?>
    </div>
    <div class="mt-3 flex items-center gap-2 border-t border-line pt-4">
      <a href="/produk/<?= esc($slug) ?>" class="btn-outline btn-sm flex-1">Detail</a>
      <a href="/cta/whatsapp/<?= esc($slug) ?>" target="_blank" hx-boost="false" class="icon-btn" title="Pesan via WhatsApp" aria-label="Pesan <?= esc($name) ?> via WhatsApp"><?= lucide_icon('message-circle', 'w-4 h-4') ?></a>
    </div>
  </div>
</article>
