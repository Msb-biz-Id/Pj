<?php
// Header halaman arsip: angka + judul + deskripsi, satu pola untuk semua index.
$stats = $stats ?? [];
?>
<header class="border-b border-line">
  <div class="shell py-12 sm:py-16" data-reveal>
    <p class="eyebrow"><?= esc($eyebrow ?? '') ?></p>
    <h1 class="display mt-4 max-w-3xl"><?= esc($title ?? '') ?></h1>
    <?php if (!empty($lede)): ?><p class="lede"><?= esc($lede) ?></p><?php endif; ?>
    <?php if (!empty($stats)): ?>
      <dl class="mt-8 flex flex-wrap gap-x-10 gap-y-4">
        <?php foreach ($stats as $s): ?>
          <div><dt class="meta"><?= esc($s['label']) ?></dt><dd class="stat-num mt-1" style="font-size:1.35rem"><?= esc($s['value']) ?></dd></div>
        <?php endforeach; ?>
      </dl>
    <?php endif; ?>
  </div>
</header>
