<?php
$settingsJson = !empty($section['settings_json']) ? json_decode($section['settings_json'], true) : [];
$title = $settingsJson['title'] ?? 'Kami melayani 50+ kota';
$lede = $settingsJson['lede'] ?? 'Pilih kota untuk melihat halaman layanan dan estimasi kirim.';
?>
<section class="section border-b border-line">
  <div class="shell">
    <?= view('partials/ui/section_head', ['eyebrow' => 'Jangkauan', 'title' => $title, 'lede' => $lede, 'align' => 'center']) ?>
    <?php if (!empty($locations)): ?>
      <div class="mx-auto mt-8 grid max-w-3xl gap-0 border-y border-line" data-reveal>
        <?php foreach (array_slice($locations, 0, 12) as $loc): ?>
          <a href="/layanan/pusat-jersey-custom-<?= esc($loc['slug']) ?>" class="group flex items-center justify-between py-3.5">
            <span class="text-[14px] text-ink">Pusat Jersey Custom <?= esc($loc['name']) ?></span>
            <span class="meta flex items-center gap-2">Lihat halaman <?= lucide_icon('arrow-right', 'w-3.5 h-3.5 transition-transform group-hover:translate-x-1') ?></span>
          </a>
          <span class="border-b border-line last:border-0"></span>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="mx-auto mt-8 max-w-3xl"><?= view('partials/ui/empty', ['title' => 'Jangkauan segera hadir', 'desc' => 'Daftar kota sedang disiapkan.']) ?></div>
    <?php endif; ?>
  </div>
</section>