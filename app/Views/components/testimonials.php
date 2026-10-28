<?php
$settingsJson = !empty($section['settings_json']) ? json_decode($section['settings_json'], true) : [];
$title = $settingsJson['title'] ?? 'Kata mereka yang sudah pesan';
$lede = $settingsJson['lede'] ?? 'Ulasan terverifikasi dari ketua tim dan komunitas.';
?>
<section class="section border-b border-line bg-surface-alt/50">
  <div class="shell">
    <?= view('partials/ui/section_head', ['eyebrow' => 'Testimoni', 'title' => $title, 'lede' => $lede, 'align' => 'center']) ?>
    <div class="mt-8 grid gap-5 md:grid-cols-3">
      <?php if (!empty($testimonials)): ?>
        <?php foreach (array_slice($testimonials, 0, 6) as $t): ?>
          <figure class="card flex flex-col p-5 sm:p-6" data-reveal>
            <span class="meta font-mono"><?= str_repeat('★', max(1, min(5, (int)($t['rating'] ?? 5)))) ?> · <?= esc($t['city'] ?? '') ?></span>
            <blockquote class="mt-3 flex-1 text-[14px] leading-relaxed text-ink">“<?= esc($t['comment']) ?>”</blockquote>
            <figcaption class="mt-4 flex items-center gap-3 border-t border-line pt-4">
              <img src="<?= esc($t['avatar'] ?? 'https://i.pravatar.cc/80?img=11') ?>" alt="<?= esc($t['client_name']) ?>" loading="lazy" class="h-9 w-9 rounded-full border border-line object-cover">
              <span><span class="block text-[13px] font-semibold text-ink"><?= esc($t['client_name']) ?></span><span class="meta block"><?= esc($t['team_name'] ?? '') ?></span></span>
            </figcaption>
          </figure>
        <?php endforeach; ?>
      <?php else: ?>
        <?= view('partials/ui/empty', ['title' => 'Testimoni segera hadir', 'desc' => 'Ulasan klien sedang dikurasi.']) ?>
      <?php endif; ?>
    </div>
  </div>
</section>