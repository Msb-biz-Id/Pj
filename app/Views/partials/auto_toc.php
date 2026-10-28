<?php if (!isset($contentHtml) || empty($contentHtml)) return; $tocData = generate_auto_toc($contentHtml); $tocList = $tocData['toc']; ?>
<?php if (!empty($tocList)): ?>
<aside x-data="autoToc()" class="rounded-[var(--radius-md)] border border-line p-5">
  <p class="mono-label">Daftar isi</p>
  <nav class="mt-3 space-y-1 text-[13px]">
    <?php foreach ($tocList as $item): ?>
      <a href="#<?= esc($item['slug'] ?? $item['id'] ?? '') ?>" class="block rounded-[8px] px-2 py-1.5 text-muted hover:bg-surface-alt hover:text-ink <?= ($item['level'] ?? 2) === 3 ? 'ml-3' : '' ?>"><?= esc($item['title']) ?></a>
    <?php endforeach; ?>
  </nav>
</aside>
<?php endif; ?>