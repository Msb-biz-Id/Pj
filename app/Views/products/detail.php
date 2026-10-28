<?php
$fallbackImage = 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=800&q=80';
$gallery = !empty($product['gallery_json']) ? json_decode($product['gallery_json'], true) : [];
if (empty($gallery) && !empty($product['featured_image'])) $gallery = [$product['featured_image']];
$gallery = array_values(array_filter((array)$gallery));
if (empty($gallery)) $gallery = [$fallbackImage];
$currentPrice = (float)($product['discount_price'] ?? $product['price'] ?? 0);
$oldPrice = (float)($product['price'] ?? 0);
$isAffiliate = !empty($product['is_affiliate']);
$waNumber = preg_replace('/[^0-9]/', '', $settings['contact']['whatsapp'] ?? '');
$productSku = !empty($product['sku']) ? $product['sku'] : ('PJ-' . ($product['id'] ?? '0'));
$waText = 'Halo ' . ($settings['brand']['name'] ?? 'Pusat Jersey') . ', saya ingin memesan: ' . ($product['name'] ?? '') . ' (SKU ' . $productSku . ')';
$waCta = $isAffiliate && !empty($product['affiliate_link']) ? '/cta/affiliate/' . rawurlencode($product['slug']) : 'https://wa.me/' . $waNumber . '?text=' . rawurlencode($waText);
?>
<div class="shell py-8">
  <?= view('partials/ui/crumbs', ['trail' => [['label' => 'Beranda', 'url' => '/'], ['label' => 'Katalog', 'url' => '/produk'], ['label' => $product['name']]]]) ?>
  <div class="mt-6 grid gap-10 lg:grid-cols-12">
    <div class="lg:col-span-7">
      <div x-data="productGallery($el)" data-gallery='<?= htmlspecialchars(json_encode(array_values($gallery)), ENT_QUOTES, 'UTF-8') ?>' class="space-y-3">
        <!-- Main Image Preview with Click to Zoom/Slide in Lightbox -->
        <div class="group relative overflow-hidden rounded-[var(--radius-lg)] border border-line bg-surface-alt">
          <img :src="galleryImages[active] || '<?= esc($gallery[0] ?? '') ?>'"
               alt="<?= esc($product['name']) ?>"
               class="aspect-[4/3] w-full object-cover transition-all duration-300 cursor-zoom-in"
               @click="openLightbox(active)">
          
          <button type="button"
                  @click="openLightbox(active)"
                  class="absolute bottom-3 right-3 flex items-center gap-1.5 rounded-full bg-ink/75 px-3 py-1.5 text-[11px] font-medium text-white shadow backdrop-blur hover:bg-ink transition-colors cursor-pointer"
                  title="Perbesar Galeri">
            <?= lucide_icon('zoom-in', 'w-3.5 h-3.5') ?>
            <span>Perbesar</span>
          </button>

          <?php if (count($gallery) > 1): ?>
            <div class="pointer-events-none absolute inset-0 flex items-center justify-between p-3 opacity-0 transition-opacity group-hover:opacity-100">
              <button type="button" @click.stop="prev()" class="pointer-events-auto grid h-9 w-9 place-items-center rounded-full bg-surface/90 text-ink shadow backdrop-blur hover:bg-surface transition-transform active:scale-95 cursor-pointer" aria-label="Sebelumnya">
                <?= lucide_icon('chevron-left', 'w-5 h-5') ?>
              </button>
              <button type="button" @click.stop="next()" class="pointer-events-auto grid h-9 w-9 place-items-center rounded-full bg-surface/90 text-ink shadow backdrop-blur hover:bg-surface transition-transform active:scale-95 cursor-pointer" aria-label="Berikutnya">
                <?= lucide_icon('chevron-right', 'w-5 h-5') ?>
              </button>
            </div>
          <?php endif; ?>
        </div>

        <?php if (count($gallery) > 1): ?>
          <div class="grid grid-cols-4 gap-3 sm:grid-cols-5">
            <?php foreach ($gallery as $i => $g): ?>
              <button type="button"
                      @click="active = <?= $i ?>"
                      class="relative overflow-hidden rounded-[var(--radius-sm)] border-2 transition-all aspect-square bg-surface-alt cursor-pointer"
                      :class="active === <?= $i ?> ? 'border-brand ring-2 ring-brand/20' : 'border-line hover:border-ink/40'">
                <img src="<?= esc($g) ?>" alt="" loading="lazy" class="h-full w-full object-cover">
              </button>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
      <?php if (!empty($product['description'])): ?>
        <h2 class="font-heading mt-8 text-[17px] font-semibold text-ink">Deskripsi</h2>
        <div class="prose-pj mt-3 max-w-none"><?= $product['description'] ?></div>
      <?php endif; ?>
      <?php
        $specs = [];
        if (!empty($product['specs_json'])) {
            $decoded = json_decode($product['specs_json'], true);
            if (is_array($decoded)) $specs = $decoded;
        }
      ?>
      <?php if (!empty($specs)): ?>
        <h2 class="font-heading mt-8 text-[17px] font-semibold text-ink">Spesifikasi &amp; Ukuran</h2>
        <dl class="mt-3 grid gap-x-8 gap-y-3 sm:grid-cols-2">
          <?php foreach ($specs as $k => $v): ?>
            <div class="flex items-baseline justify-between gap-4 border-b border-line pb-2.5">
              <dt class="meta text-[11px] uppercase tracking-[0.14em]"><?= esc($k) ?></dt>
              <dd class="text-right text-[13.5px] font-semibold text-ink"><?= esc(is_scalar($v) ? (string)$v : json_encode($v)) ?></dd>
            </div>
          <?php endforeach; ?>
        </dl>
      <?php endif; ?>
      <?php if (!empty($gallery)): ?>
        <section class="mt-8 border-t border-line pt-8">
          <div class="flex items-center justify-between">
            <h2 class="font-heading text-[17px] font-semibold text-ink">Galeri Produk</h2>
            <span class="meta text-[12px]"><?= count($gallery) ?> foto · klik untuk buka popup &amp; slide</span>
          </div>
          <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
            <?php foreach ($gallery as $i => $g): ?>
              <a href="<?= esc($g) ?>"
                 class="glightbox group relative block overflow-hidden rounded-[var(--radius-sm)] border border-line bg-surface-alt hover:border-ink/50 transition-all cursor-zoom-in"
                 data-gallery="product-gallery"
                 data-title="<?= esc($product['name']) ?>"
                 data-description="Foto <?= $i + 1 ?> dari <?= count($gallery) ?>"
                 hx-boost="false">
                <img src="<?= esc($g) ?>" alt="<?= esc($product['name']) ?> galeri <?= $i + 1 ?>" loading="lazy" class="aspect-square w-full object-cover transition-transform duration-300 group-hover:scale-105">
                <div class="absolute inset-0 bg-ink/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                  <span class="rounded-full bg-surface/90 px-2.5 py-1 text-[11px] font-medium text-ink shadow-sm flex items-center gap-1">
                    <?= lucide_icon('zoom-in', 'w-3.5 h-3.5') ?>
                    <span>Lihat</span>
                  </span>
                </div>
              </a>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endif; ?>
      <?php $postFaqs = $faqs ?? []; ?>
      <?php if (!empty($postFaqs)): ?>
        <section class="mt-8">
          <h2 class="font-heading text-[17px] font-semibold text-ink">Pertanyaan umum</h2>
          <div class="mt-4"><?= view('partials/ui/faq', ['id' => 'prod-faq', 'items' => $postFaqs]) ?></div>
        </section>
      <?php endif; ?>
    </div>
    <aside class="lg:col-span-5">
      <div class="lg:sticky lg:top-24">
        <p class="meta text-[11px] uppercase tracking-[0.14em]"><?= esc($product['category_name'] ?? 'Jersey') ?></p>
        <h1 class="font-heading mt-2 text-[26px] font-semibold leading-tight tracking-tight text-ink"><?= esc($product['name']) ?></h1>
        <div class="mt-3 flex items-baseline gap-2">
          <span class="price !text-[22px]">Rp <?= number_format($currentPrice, 0, ',', '.') ?></span>
          <?php if ($oldPrice > $currentPrice): ?><span class="price-strike">Rp <?= number_format($oldPrice, 0, ',', '.') ?></span><?php endif; ?>
        </div>
        <dl class="meta mt-4 grid grid-cols-2 gap-3 border-y border-line py-4">
          <div><dt>SKU</dt><dd class="font-semibold text-ink"><?= esc($productSku) ?></dd></div>
          <div><dt>Tipe</dt><dd class="font-semibold text-ink"><?= !empty($product['is_custom']) ? 'Custom sublim' : 'Stok' ?></dd></div>
        </dl>
        <div class="mt-4 flex gap-2">
          <a href="<?= esc($waCta) ?>" target="_blank" hx-boost="false" class="btn-wa flex-1">Pesan via WhatsApp</a>
        </div>
        <?php if (!empty($product['shopee_link']) || !empty($product['tokopedia_link']) || !empty($product['tiktok_link'])): ?>
          <div class="mt-3 flex gap-2">
            <?php if (!empty($product['shopee_link'])): ?><a href="/cta/shopee/<?= rawurlencode($product['slug']) ?>" target="_blank" hx-boost="false" class="chip flex-1 justify-center gap-1.5"><?= brand_icon('shopee', 'h-3.5 w-3.5') ?><span>Shopee</span></a><?php endif; ?>
            <?php if (!empty($product['tokopedia_link'])): ?><a href="/cta/tokopedia/<?= rawurlencode($product['slug']) ?>" target="_blank" hx-boost="false" class="chip flex-1 justify-center gap-1.5"><?= brand_icon('tokopedia', 'h-3.5 w-3.5') ?><span>Tokopedia</span></a><?php endif; ?>
            <?php if (!empty($product['tiktok_link'])): ?><a href="/cta/tiktok/<?= rawurlencode($product['slug']) ?>" target="_blank" hx-boost="false" class="chip flex-1 justify-center gap-1.5"><?= brand_icon('tiktok', 'h-3.5 w-3.5') ?><span>TikTok</span></a><?php endif; ?>
          </div>
        <?php endif; ?>
        <div class="mt-4 rounded-[var(--radius-md)] border border-line bg-surface-alt/60 p-4 text-[13px] leading-relaxed text-muted">Konsultasi desain gratis · revisi sampai cocok · QC per potong.</div>
        <div class="mt-4"><?= view('partials/social_share', ['shareUrl' => current_url(), 'shareTitle' => $product['name']]) ?></div>
      </div>
    </aside>
  </div>
</div>
<div class="h-20 lg:hidden"></div>