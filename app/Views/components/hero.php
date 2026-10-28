<?php
$settingsJson = !empty($section['settings_json']) ? json_decode($section['settings_json'], true) : [];
$title = $settingsJson['title'] ?? 'Konveksi jersey custom untuk tim yang serius tampil rapi.';
$subtitle = $settingsJson['subtitle'] ?? 'Futsal, sepak bola, basket, sepeda — sublimasi presisi, nameset gratis, dan QC per potong.';
$eyebrow = $settingsJson['eyebrow'] ?? 'Konveksi · Sublimasi · Sejak 2018';
$waNumber = $settingsJson['wa_number'] ?? ($settings['contact']['whatsapp'] ?? '6281234567890');
$ctaPrimary = $settingsJson['cta_primary'] ?? ['text' => 'Konsultasi via WhatsApp', 'url' => 'https://wa.me/' . $waNumber];
$ctaSecondary = $settingsJson['cta_secondary'] ?? ['text' => 'Lihat Layanan', 'url' => '/layanan'];
$heroImage = $settingsJson['bg_image'] ?? 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=1200&q=80';
?>
<section class="border-b border-line">
  <div class="shell grid gap-10 py-12 sm:py-16 lg:grid-cols-12 lg:gap-12" data-animate="hero">
    <div class="hero-copy lg:col-span-7">
      <p class="eyebrow"><?= esc($eyebrow) ?></p>
      <h1 class="display mt-5 max-w-[18ch]"><?= esc($title) ?></h1>
      <p class="lede"><?= esc($subtitle) ?></p>
      <div class="mt-7 flex flex-wrap items-center gap-2">
        <a href="<?= esc($ctaPrimary['url']) ?>" target="_blank" hx-boost="false" class="btn-wa anim-glow"><?= lucide_icon('message-circle','w-4 h-4') ?><span><?= esc($ctaPrimary['text']) ?></span></a>
        <a href="<?= esc($ctaSecondary['url']) ?>" class="btn-outline"><?= esc($ctaSecondary['text']) ?></a>
      </div>
      <dl class="mt-10 grid max-w-lg grid-cols-3 gap-6 border-t border-line pt-6">
        <div><dt class="meta">Tim dilayani</dt><dd class="stat-num mt-1 text-xl">10.000+</dd></div>
        <div><dt class="meta">Kepuasan</dt><dd class="stat-num mt-1 text-xl">99,4%</dd></div>
        <div><dt class="meta">Produksi</dt><dd class="stat-num mt-1 text-xl">±7 hari</dd></div>
      </dl>
    </div>
    <div class="lg:col-span-5">
      <figure class="overflow-hidden rounded-[var(--radius-lg)] border border-line bg-surface-alt" data-parallax>
        <img src="<?= esc($heroImage) ?>" alt="Hasil jersey custom Pusat Jersey" class="aspect-[4/3] w-full object-cover">
        <figcaption class="flex items-center justify-between border-t border-line px-4 py-3 text-[12px] text-faint">
          <span>Batch produksi terbaru</span><span class="font-mono">QC · 03</span>
        </figcaption>
      </figure>
    </div>
  </div>
</section>

