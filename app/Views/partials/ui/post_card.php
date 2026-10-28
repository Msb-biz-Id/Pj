<?php
// Kartu artikel editorial.
$title = $post['title'] ?? '';
$slug  = $post['slug'] ?? '#';
$cat   = $post['category_name'] ?? 'Edukasi';
$img   = $post['featured_image'] ?? '';
$date  = !empty($post['created_at']) ? date('d M Y', strtotime($post['created_at'])) : '';
$exc   = $post['excerpt'] ?? '';
?>
<article class="card group flex flex-col" data-reveal>
  <?php if ($img): ?>
    <a href="/blog/<?= esc($slug) ?>" class="card-media aspect-[16/10]" tabindex="-1" aria-hidden="true">
      <img src="<?= esc($img) ?>" alt="<?= esc($title) ?>" loading="lazy">
    </a>
  <?php endif; ?>
  <div class="flex flex-1 flex-col gap-2 p-5">
    <p class="meta text-[11px]"><span class="font-semibold text-ink"><?= esc($cat) ?></span><?php if ($date): ?> <span aria-hidden="true">·</span> <?= esc($date) ?><?php endif; ?></p>
    <a href="/blog/<?= esc($slug) ?>" class="card-title line-clamp-2"><?= esc($title) ?></a>
    <?php if ($exc): ?><p class="text-[13.5px] leading-relaxed text-muted line-clamp-2"><?= esc($exc) ?></p><?php endif; ?>
    <a href="/blog/<?= esc($slug) ?>" class="link-more mt-2">Baca artikel <?= lucide_icon('arrow-right', 'w-3.5 h-3.5') ?></a>
  </div>
</article>
