<?php
$settingsJson = !empty($section['settings_json']) ? json_decode($section['settings_json'], true) : [];
$title = $settingsJson['title'] ?? 'Layanan konveksi';
$lede = $settingsJson['lede'] ?? 'Satu workshop untuk semua kebutuhan apparel tim — dari desain sampai kirim.';
?>
<section class="section border-b border-line">
  <div class="shell">
    <div class="flex flex-wrap items-end justify-between gap-4">
      <?= view('partials/ui/section_head', ['eyebrow' => 'Layanan', 'title' => $title, 'lede' => $lede]) ?>
      <a href="/layanan" class="link-more">Semua layanan <?= lucide_icon('arrow-right', 'w-3.5 h-3.5') ?></a>
    </div>
    <div class="mt-8 divide-y divide-line border-y border-line">
      <?php if (!empty($services)): ?>
        <?php foreach ($services as $i => $srv): ?>
          <a href="/layanan/<?= esc($srv['slug']) ?>" class="group grid gap-2 py-5 sm:grid-cols-12 sm:items-center sm:gap-6" data-reveal>
            <span class="meta font-mono sm:col-span-1">0<?= $i + 1 ?></span>
            <span class="sm:col-span-6">
              <span class="block font-heading text-[17px] font-semibold text-ink group-hover:underline group-hover:underline-offset-4"><?= esc($srv['title']) ?></span>
              <span class="meta mt-1 line-clamp-1 block"><?= esc($srv['short_desc']) ?></span>
            </span>
            <span class="meta sm:col-span-3">Mulai Rp <?= number_format($srv['starting_price'], 0, ',', '.') ?></span>
            <span class="sm:col-span-2 sm:justify-self-end"><span class="icon-btn transition-transform group-hover:translate-x-1"><?= lucide_icon('arrow-up-right', 'w-4 h-4') ?></span></span>
          </a>
        <?php endforeach; ?>
      <?php else: ?>
        <?= view('partials/ui/empty', ['title' => 'Layanan belum tersedia', 'desc' => 'Hubungi CS untuk info layanan.']) ?>
      <?php endif; ?>
    </div>
  </div>
</section>
