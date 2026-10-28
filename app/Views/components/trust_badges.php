<?php
$settingsJson = !empty($section['settings_json']) ? json_decode($section['settings_json'], true) : [];
$items = $settingsJson['items'] ?? [
    ['icon' => 'printer', 'label' => 'Sublim presisi', 'desc' => 'Warna tajam, tidak luntur.'],
    ['icon' => 'shield-check', 'label' => 'Garansi QC', 'desc' => 'Salah cetak atau jahit diganti.'],
    ['icon' => 'truck', 'label' => 'Kirim nasional', 'desc' => 'Reguler hingga kargo.'],
    ['icon' => 'pen-tool', 'label' => 'Desain gratis', 'desc' => 'Revisi sampai cocok.'],
];
?>
<section class="border-b border-line">
  <div class="shell py-10">
    <ul class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
      <?php foreach ($items as $item): ?>
        <li class="flex gap-3" data-reveal>
          <span class="icon-btn !h-10 !w-10 shrink-0"><?= lucide_icon($item['icon'] ?? 'check', 'w-4 h-4') ?></span>
          <div><p class="text-[13.5px] font-semibold text-ink"><?= esc($item['label']) ?></p><p class="meta mt-0.5"><?= esc($item['desc']) ?></p></div>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

