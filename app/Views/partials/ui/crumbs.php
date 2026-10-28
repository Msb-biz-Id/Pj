<?php
// Breadcrumb tipis satu baris.
$trail = $trail ?? [['label' => 'Beranda', 'url' => '/']];
?>
<nav aria-label="Breadcrumb" class="flex items-center gap-2 text-[12px] text-faint">
  <?php foreach ($trail as $i => $c): ?>
    <?php if ($i > 0): ?><span aria-hidden="true" class="text-line">/</span><?php endif; ?>
    <?php if (!empty($c['url']) && $i < count($trail) - 1): ?>
      <a href="<?= esc($c['url']) ?>" class="transition-colors hover:text-ink"><?= esc($c['label']) ?></a>
    <?php else: ?>
      <span class="max-w-[220px] truncate text-ink"><?= esc($c['label']) ?></span>
    <?php endif; ?>
  <?php endforeach; ?>
</nav>
