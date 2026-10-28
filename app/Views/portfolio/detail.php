<?php
$gallery = !empty($portfolio['gallery_json']) ? json_decode($portfolio['gallery_json'], true) : [];
if (empty($gallery) && !empty($portfolio['featured_image'])) $gallery = [$portfolio['featured_image']];
$gallery = array_values(array_filter((array)$gallery));
?>
<div class="shell py-8">
  <?= view('partials/ui/crumbs', ['trail' => [['label' => 'Beranda', 'url' => '/'], ['label' => 'Portofolio', 'url' => '/portofolio'], ['label' => $portfolio['title']]]]) ?>
  <div class="mt-6 grid gap-10 lg:grid-cols-12">
    <div class="lg:col-span-8">
      <p class="meta text-[11px] uppercase tracking-[0.14em]"><?= esc($portfolio['client_name'] ?? 'Klien') ?></p>
      <h1 class="font-heading mt-2 text-[28px] font-semibold leading-tight tracking-tight text-ink sm:text-[34px]"><?= esc($portfolio['title']) ?></h1>
      <figure class="mt-6 overflow-hidden rounded-[var(--radius-lg)] border border-line bg-surface-alt"><img src="<?= esc($portfolio['featured_image']) ?>" alt="<?= esc($portfolio['title']) ?>" class="aspect-[16/10] w-full object-cover"></figure>
      <?php if (count($gallery) > 1): ?>
        <div class="mt-3 grid grid-cols-3 gap-3">
          <?php foreach (array_slice($gallery, 1, 6) as $g): ?>
            <a href="<?= esc($g) ?>" class="glightbox overflow-hidden rounded-[var(--radius-sm)] border border-line bg-surface-alt" data-gallery="portfolio"><img src="<?= esc($g) ?>" alt="" loading="lazy" class="aspect-[4/3] w-full object-cover"></a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <?php if (!empty($portfolio['description'])): ?><div class="prose-pj mt-7 max-w-[68ch]"><?= $portfolio['description'] ?></div><?php endif; ?>
      <?php if (!empty($faqs)): ?>
        <h2 class="font-heading mt-10 text-[18px] font-semibold text-ink">Pertanyaan umum</h2>
        <div class="mt-4"><?= view('partials/ui/faq', ['id' => 'pf', 'items' => $faqs]) ?></div>
      <?php endif; ?>
    </div>
    <aside class="lg:col-span-4">
      <div class="rounded-[var(--radius-lg)] border border-line p-6 lg:sticky lg:top-24">
        <p class="mono-label">Fakta proyek</p>
        <dl class="meta mt-4 space-y-3">
          <div class="flex justify-between border-b border-line pb-3"><dt>Klien</dt><dd class="font-semibold text-ink"><?= esc($portfolio['client_name'] ?? '-') ?></dd></div>
          <div class="flex justify-between"><dt>Selesai</dt><dd class="text-ink"><?= !empty($portfolio['created_at']) ? date('d M Y', strtotime($portfolio['created_at'])) : '-' ?></dd></div>
        </dl>
        <a href="https://wa.me/<?= esc($settings['contact']['whatsapp'] ?? '6281234567890') ?>" target="_blank" hx-boost="false" class="btn-wa mt-5 w-full">Buat seperti ini</a>
        <?php if (!empty($related)): ?>
          <div class="mt-6 border-t border-line pt-5">
            <p class="mono-label">Proyek lain</p>
            <div class="mt-3 space-y-3">
              <?php foreach (array_slice($related, 0, 3) as $rp): ?>
                <a href="/portofolio/<?= esc($rp['slug']) ?>" class="group flex gap-3">
                  <span class="h-14 w-16 shrink-0 overflow-hidden rounded-[var(--radius-sm)] border border-line bg-surface-alt"><img src="<?= esc($rp['featured_image']) ?>" alt="" loading="lazy" class="h-full w-full object-cover"></span>
                  <span class="card-title !text-[13.5px] line-clamp-2"><?= esc($rp['title']) ?></span>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </aside>
  </div>
</div>