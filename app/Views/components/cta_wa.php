<?php
$settingsJson = !empty($section['settings_json']) ? json_decode($section['settings_json'], true) : [];
$heading = $settingsJson['heading'] ?? 'Ada desain di kepala? Kirim saja, kami rapikan gratis.';
$text = $settingsJson['text'] ?? 'Konsultasi WhatsApp';
$waNumber = preg_replace('/[^0-9]/', '', $settingsJson['wa_number'] ?? ($settings['contact']['whatsapp'] ?? '6281234567890'));
$waMsg = rawurlencode('Halo Admin ' . ($settings['brand']['name'] ?? 'Pusat Jersey') . ', saya ingin konsultasi jersey custom.');
?>
<section class="border-b border-line">
  <div class="shell flex flex-col gap-3 py-8 sm:flex-row sm:items-center sm:justify-between" data-reveal>
    <p class="font-heading text-[17px] font-semibold tracking-tight text-ink"><?= esc($heading) ?></p>
    <a href="https://wa.me/<?= esc($waNumber) ?>?text=<?= $waMsg ?>" target="_blank" hx-boost="false" class="btn-wa btn-sm shrink-0"><?= lucide_icon('message-circle', 'w-4 h-4') ?><span><?= esc($text) ?></span></a>
  </div>
</section>