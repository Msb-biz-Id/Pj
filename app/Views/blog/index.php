<?= view('partials/ui/page_head', [
  'eyebrow' => 'Blog',
  'title' => 'Catatan dari meja produksi',
  'lede' => 'Bahan, sizing, dan perawatan jersey — singkat dan praktis.',
  'stats' => [['label' => 'Artikel', 'value' => number_format($totalPosts ?? 0, 0, ',', '.')]],
]) ?>
<div class="shell py-8">
  <div class="flex flex-wrap gap-2">
    <a href="/blog" class="chip" <?= ($activeCat ?? '') === '' && empty($tagSlug) ? 'aria-current="true"' : '' ?>>Semua</a>
    <?php if (!empty($categories)): ?>
      <?php foreach ($categories as $cat): ?>
        <a href="/blog?category=<?= esc($cat['slug']) ?>" class="chip" <?= ($activeCat ?? '') === $cat['slug'] ? 'aria-current="true"' : '' ?>><?= esc($cat['name']) ?></a>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
  <div class="mt-6 grid gap-5 md:grid-cols-3">
    <?php if (!empty($posts)): ?>
      <?php foreach ($posts as $post): ?><?= view('partials/ui/post_card', ['post' => $post]) ?><?php endforeach; ?>
    <?php else: ?>
      <div class="md:col-span-3"><?= view('partials/ui/empty', ['icon' => 'file-text', 'title' => 'Artikel segera hadir', 'desc' => 'Tulisan baru sedang disiapkan.']) ?></div>
    <?php endif; ?>
  </div>
</div>