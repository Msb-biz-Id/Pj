<?php
$waNumber = $settings['contact']['whatsapp'] ?? '6281234567890';
$waText = rawurlencode('Halo Admin Pusat Jersey, saya ingin konsultasi layanan ' . $service['title'] . '.');
$price = isset($service['starting_price']) ? 'Rp ' . number_format((float)$service['starting_price'], 0, ',', '.') : 'Rp 95.000';

// Konteks geo (SEO lokal): halaman /layanan/{layanan}-{kota}
$location     = $location ?? null;
$parentSlug   = $parentServiceSlug ?? null;
$siblings     = $siblingLocations ?? [];
$crumbs       = [['label' => 'Beranda', 'url' => '/'], ['label' => 'Layanan', 'url' => '/layanan']];
if ($location && $parentSlug) {
    $crumbs[] = ['label' => ucwords(str_replace('-', ' ', $parentSlug)), 'url' => '/layanan/' . $parentSlug];
}
$crumbs[] = ['label' => $service['title']];
?>
<div class="border-b border-line">
  <div class="shell py-10 sm:py-14">
    <?= view('partials/ui/crumbs', ['trail' => $crumbs]) ?>
    <div class="mt-6 grid gap-8 lg:grid-cols-12 lg:items-end">
      <div class="lg:col-span-8">
        <p class="eyebrow">Layanan<?= $location ? ' · ' . esc($location['name']) : '' ?> · mulai <?= $price ?></p>
        <h1 class="display mt-4"><?= esc($service['title']) ?></h1>
        <p class="lede"><?= esc($service['short_desc']) ?></p>
        <?php if ($location && $parentSlug): ?>
          <p class="meta mt-3">
            Melayani pemesanan ke <strong class="text-ink"><?= esc($location['name']) ?></strong><?= !empty($location['province']) ? ' dan sekitarnya (' . esc($location['province']) . ')' : '' ?> —
            <a href="<?= base_url('layanan/' . $parentSlug) ?>" class="underline hover:text-ink">Lihat layanan nasional</a>
          </p>
        <?php endif; ?>
      </div>
      <div class="lg:col-span-4">
        <div class="flex gap-2 lg:justify-end">
          <a href="https://wa.me/<?= esc($waNumber) ?>?text=<?= $waText ?>" target="_blank" hx-boost="false" class="btn-wa">Konsultasi gratis</a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php if (!empty($service['featured_image'])): ?>
  <div class="shell pt-8"><figure class="overflow-hidden rounded-[var(--radius-lg)] border border-line bg-surface-alt"><img src="<?= esc($service['featured_image']) ?>" alt="<?= esc($service['title']) ?>" class="aspect-[21/9] w-full object-cover"></figure></div>
<?php endif; ?>
<div class="shell grid gap-10 py-10 lg:grid-cols-12">
  <div class="lg:col-span-7">
    <?php if (!empty($service['content'])): ?><div class="prose-pj max-w-none"><?= $service['content'] ?></div><?php endif; ?>
    <?php if (!empty($siblings)): ?>
      <section class="mt-8">
        <h2 class="font-heading text-[19px] font-semibold text-ink">Layanan di kota lain</h2>
        <p class="meta mt-1">Pilih kota terdekat untuk info pengiriman & konsultasi regional.</p>
        <div class="mt-3 flex flex-wrap gap-2">
          <?php foreach ($siblings as $sib): ?>
            <a href="<?= base_url('layanan/' . $parentSlug . '-' . $sib['slug']) ?>"
               class="inline-flex items-center gap-1.5 rounded-full border border-line bg-[var(--color-surface-alt)] px-3 py-1.5 text-[12px] text-ink hover:bg-surface hover:border-ink/30 transition-colors">
              <?= lucide_icon('map-pin', 'w-3.5 h-3.5 text-faint') ?>
              <?= esc($sib['name']) ?>
            </a>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>
    <?php if (!empty($faqs)): ?>
      <section class="mt-8">
        <h2 class="font-heading text-[19px] font-semibold text-ink">Pertanyaan umum</h2>
        <div class="mt-4"><?= view('partials/ui/faq', ['id' => 'svc-faq', 'items' => $faqs]) ?></div>
      </section>
    <?php endif; ?>
  </div>
  <aside class="lg:col-span-5">
    <div class="rounded-[var(--radius-lg)] border border-line p-6 lg:sticky lg:top-24">
      <p class="mono-label">Ringkasan</p>
      <dl class="mt-4 space-y-3 text-[13.5px]">
        <div class="flex justify-between border-b border-line pb-3"><dt class="text-faint">Harga mulai</dt><dd class="font-heading font-semibold text-ink"><?= $price ?></dd></div>
        <div class="flex justify-between border-b border-line pb-3"><dt class="text-faint">Min. order</dt><dd class="text-ink">6 stel</dd></div>
        <div class="flex justify-between"><dt class="text-faint">Estimasi produksi</dt><dd class="text-ink">7–10 hari</dd></div>
      </dl>
      <a href="https://wa.me/<?= esc($waNumber) ?>?text=<?= $waText ?>" target="_blank" hx-boost="false" class="btn-wa mt-5 w-full">Chat WhatsApp</a>
      <p class="meta mt-2 text-center">Respons &lt; 5 menit · 08.00–21.00</p>
    </div>
  </aside>
</div>