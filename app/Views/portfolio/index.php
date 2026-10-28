<?php $totalProjects = $totalProjects ?? 0; $portfolios = $portfolios ?? []; $categories = $categories ?? []; $activeCat = $activeCat ?? ''; ?>
<?= view('partials/ui/page_head', [
  'eyebrow' => 'Portofolio',
  'title' => 'Hasil jahitan yang sudah dikirim',
  'lede' => 'Arsip produksi klien — klik untuk detail bahan dan tim.',
  'stats' => [['label' => 'Proyek terdokumentasi', 'value' => number_format($totalProjects, 0, ',', '.')], ['label' => 'Tim dilayani', 'value' => '10.000+']],
]) ?>
<div class="shell py-8">
  <div class="flex flex-wrap gap-2">
    <a href="/portofolio" class="chip" <?= $activeCat === '' ? 'aria-current="true"' : '' ?>>Semua</a>
    <?php foreach ($categories as $cat): ?>
      <a href="/portofolio?category=<?= esc($cat['slug']) ?>" class="chip" <?= $activeCat === $cat['slug'] ? 'aria-current="true"' : '' ?>><?= esc($cat['name']) ?></a>
    <?php endforeach; ?>
  </div>
  <?php if (!empty($portfolios)): ?>
    <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
      <?php foreach ($portfolios as $port): ?>
        <a href="/portofolio/<?= esc($port['slug']) ?>" class="card group">
          <span class="card-media block aspect-[4/3]"><img src="<?= esc($port['featured_image']) ?>" alt="<?= esc($port['title']) ?>" loading="lazy"></span>
          <span class="block p-5"><span class="meta block"><?= esc($port['client_name'] ?? '') ?></span><span class="card-title mt-1 line-clamp-2 block"><?= esc($port['title']) ?></span></span>
        </a>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <?= view('partials/ui/empty', ['icon' => 'image', 'title' => 'Portofolio segera hadir', 'desc' => 'Dokumentasi sedang disiapkan.']) ?>
  <?php endif; ?>
</div>