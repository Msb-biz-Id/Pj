<?php
$settingsJson = !empty($section['settings_json']) ? json_decode($section['settings_json'], true) : [];
$title = $settingsJson['title'] ?? 'Arsip jahitan terbaru';
$lede = $settingsJson['lede'] ?? 'Dokumentasi hasil produksi — klik untuk melihat detail.';
?>
<section class="section">
  <div class="shell">
    <div class="flex flex-wrap items-end justify-between gap-4">
      <?= view('partials/ui/section_head', ['eyebrow' => 'Portofolio', 'title' => $title, 'lede' => $lede]) ?>
      <a href="/portofolio" class="link-more">Semua portofolio <?= lucide_icon('arrow-right', 'w-3.5 h-3.5') ?></a>
    </div>
    <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
      <?php if (!empty($portfolios)): ?>
        <?php foreach (array_slice($portfolios, 0, 6) as $port): ?>
          <a href="/portofolio/<?= esc($port['slug']) ?>" class="card group" data-reveal>
            <span class="card-media block aspect-[4/3]">
              <img src="<?= esc($port['featured_image']) ?>" alt="<?= esc($port['title']) ?>" loading="lazy">
            </span>
            <span class="block p-5">
              <span class="meta block"><?= esc($port['client_name'] ?? 'Klien') ?></span>
              <span class="card-title mt-1 line-clamp-2 block"><?= esc($port['title']) ?></span>
            </span>
          </a>
        <?php endforeach; ?>
      <?php else: ?>
        <?= view('partials/ui/empty', ['title' => 'Portofolio segera hadir', 'desc' => 'Dokumentasi produksi sedang disiapkan.']) ?>
      <?php endif; ?>
    </div>
  </div>
</section>