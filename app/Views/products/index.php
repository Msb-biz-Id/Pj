<?= view('partials/ui/page_head', [
  'eyebrow' => 'Katalog',
  'title' => 'Katalog jersey custom & stok original',
  'lede' => 'Harga transparan per potong. Buka detail untuk size chart, bahan, dan cara pesan.',
  'stats' => [['label' => 'Produk aktif', 'value' => number_format($totalProducts ?? 0, 0, ',', '.')]],
]) ?>
<div class="shell py-8">
  <div class="flex flex-wrap gap-2" aria-label="Filter kategori">
    <a href="/produk" class="chip" <?= ($activeCat ?? '') === '' ? 'aria-current="true"' : '' ?>>Semua</a>
    <?php if (!empty($categories)): ?>
      <?php foreach ($categories as $cat): ?>
        <a href="/produk?category=<?= esc($cat['slug']) ?>" class="chip" <?= ($activeCat ?? '') === $cat['slug'] ? 'aria-current="true"' : '' ?>><?= esc($cat['name']) ?><?php if (isset($cat['count'])): ?> · <?= (int)$cat['count'] ?><?php endif; ?></a>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
  <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
    <?php if (!empty($products)): ?>
      <?php foreach ($products as $prd): ?><?= view('partials/ui/product_card', ['product' => $prd]) ?><?php endforeach; ?>
    <?php else: ?>
      <div class="sm:col-span-2 lg:col-span-3"><?= view('partials/ui/empty', ['icon' => 'shopping-bag', 'title' => 'Belum ada produk', 'desc' => 'Hubungi CS untuk ketersediaan.']) ?></div>
    <?php endif; ?>
  </div>
</div>