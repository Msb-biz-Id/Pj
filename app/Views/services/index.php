<?= view('partials/ui/page_head', [
  'eyebrow' => 'Layanan',
  'title' => 'Satu workshop untuk semua apparel tim',
  'lede' => 'Futsal sampai sepeda. Pilih layanan untuk harga mulai, proses, dan contoh hasil.',
  'stats' => [['label' => 'Layanan aktif', 'value' => number_format($totalServices ?? 0, 0, ',', '.')], ['label' => 'Kota terlayani', 'value' => '50+'], ['label' => 'Tim dilayani', 'value' => '10.000+']],
]) ?>
<div class="shell py-8">
  <div class="divide-y divide-line border-y border-line">
    <?php if (!empty($services)): ?>
      <?php foreach ($services as $i => $srv): ?>
        <a href="/layanan/<?= esc($srv['slug']) ?>" class="group grid gap-2 py-5 sm:grid-cols-12 sm:items-center sm:gap-6">
          <span class="meta font-mono sm:col-span-1">0<?= $i + 1 ?></span>
          <span class="sm:col-span-7"><span class="block font-heading text-[18px] font-semibold text-ink group-hover:underline group-hover:underline-offset-4"><?= esc($srv['title']) ?></span><span class="meta mt-1 line-clamp-2 block max-w-xl"><?= esc($srv['short_desc']) ?></span></span>
          <span class="meta sm:col-span-2">Mulai Rp <?= number_format($srv['starting_price'], 0, ',', '.') ?></span>
          <span class="sm:col-span-2 sm:justify-self-end"><span class="icon-btn transition-transform group-hover:translate-x-1"><?= lucide_icon('arrow-up-right', 'w-4 h-4') ?></span></span>
        </a>
      <?php endforeach; ?>
    <?php else: ?>
      <?= view('partials/ui/empty', ['icon' => 'shirt', 'title' => 'Layanan segera hadir', 'desc' => 'Hubungi CS untuk info.']) ?>
    <?php endif; ?>
  </div>
</div>