<?php
$settingsJson = !empty($section['settings_json']) ? json_decode($section['settings_json'], true) : [];
$title = $settingsJson['title'] ?? 'Katalog pilihan';
$lede = $settingsJson['lede'] ?? 'Jersey custom dan stok original — harga transparan, bisa pesan satuan.';
?>
<section class="section border-b border-line">
  <div class="shell">
    <div class="flex flex-wrap items-end justify-between gap-4">
      <?= view('partials/ui/section_head', ['eyebrow' => 'Katalog', 'title' => $title, 'lede' => $lede]) ?>
      <a href="/produk" class="link-more">Semua katalog <?= lucide_icon('arrow-right', 'w-3.5 h-3.5') ?></a>
    </div>
    <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
      <?php if (!empty($products)): ?>
        <?php foreach (array_slice($products, 0, 6) as $prd): ?>
          <?= view('partials/ui/product_card', ['product' => $prd]) ?>
        <?php endforeach; ?>
      <?php else: ?>
        <?= view('partials/ui/empty', ['title' => 'Katalog segera hadir', 'desc' => 'Hubungi CS untuk ketersediaan.']) ?>
      <?php endif; ?>
    </div>
  </div>
</section>
