<?php
$settingsJson = !empty($section['settings_json']) ? json_decode($section['settings_json'], true) : [];
$title = $settingsJson['title'] ?? 'Catatan workshop';
$lede = $settingsJson['lede'] ?? 'Panduan bahan, sizing, dan perawatan jersey — ditulis dari meja produksi.';
?>
<section class="section">
  <div class="shell">
    <div class="flex flex-wrap items-end justify-between gap-4">
      <?= view('partials/ui/section_head', ['eyebrow' => 'Blog', 'title' => $title, 'lede' => $lede]) ?>
      <a href="/blog" class="link-more">Semua artikel <?= lucide_icon('arrow-right', 'w-3.5 h-3.5') ?></a>
    </div>
    <div class="mt-8 grid gap-5 md:grid-cols-3">
      <?php if (!empty($posts)): ?>
        <?php foreach (array_slice($posts, 0, 3) as $post): ?>
          <?= view('partials/ui/post_card', ['post' => $post]) ?>
        <?php endforeach; ?>
      <?php else: ?>
        <?= view('partials/ui/empty', ['title' => 'Artikel segera hadir', 'desc' => 'Tulisan baru sedang disiapkan.']) ?>
      <?php endif; ?>
    </div>
  </div>
</section>