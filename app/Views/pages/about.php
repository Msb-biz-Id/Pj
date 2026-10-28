<?php $about = $settings['about'] ?? []; $stats = $about['stats'] ?? []; ?>
<?= view('partials/ui/page_head', [
  'eyebrow' => 'Tentang',
  'title' => $about['title'] ?? 'Workshop konveksi yang rapi sejak awal',
  'lede' => $about['short_desc'] ?? 'Kami mengerjakan jersey custom dengan alur jelas: desain, fitting, produksi, QC, kirim.',
]) ?>
<div class="shell py-10">
  <?php if (!empty($stats)): ?>
    <dl class="grid grid-cols-2 gap-6 border-y border-line py-8 md:grid-cols-4">
      <?php foreach ($stats as $st): ?>
        <div><dt class="meta"><?= esc($st['label']) ?></dt><dd class="stat-num mt-1 text-2xl"><?= esc($st['value']) ?></dd></div>
      <?php endforeach; ?>
    </dl>
  <?php endif; ?>
  <div class="mt-10 grid gap-10 lg:grid-cols-12">
    <div class="lg:col-span-7">
      <h2 class="font-heading text-[20px] font-semibold text-ink">Cara kami bekerja</h2>
      <div class="prose-pj mt-4 max-w-[68ch]"><?= $about['story_html'] ?? '' ?></div>
      <div class="mt-6 grid gap-4 sm:grid-cols-2">
        <div class="rounded-[var(--radius-md)] border border-line p-5"><p class="font-heading text-[15px] font-semibold text-ink">Visi</p><p class="meta mt-1.5"><?= esc($about['vision'] ?? '') ?></p></div>
        <div class="rounded-[var(--radius-md)] border border-line p-5"><p class="font-heading text-[15px] font-semibold text-ink">Misi</p><p class="meta mt-1.5"><?= esc($about['mission'] ?? '') ?></p></div>
      </div>
    </div>
    <div class="lg:col-span-5">
      <figure class="overflow-hidden rounded-[var(--radius-lg)] border border-line bg-surface-alt lg:sticky lg:top-24">
        <img src="https://images.unsplash.com/photo-1517466787929-bc90951d0974?auto=format&fit=crop&w=800&q=80" alt="Workshop Pusat Jersey" class="aspect-[4/3] w-full object-cover">
        <figcaption class="meta border-t border-line px-4 py-3">Meja potong · Batch harian</figcaption>
      </figure>
    </div>
  </div>
  <div class="mt-12">
    <?= view('partials/partners_slider', ['settings' => $settings]) ?>
  </div>
  <?php if (!empty($faqs)): ?>
    <div class="mt-12">
      <h2 class="font-heading text-[20px] font-semibold text-ink">Pertanyaan umum</h2>
      <div class="mt-4 max-w-3xl"><?= view('partials/ui/faq', ['id' => 'about', 'items' => $faqs]) ?></div>
    </div>
  <?php endif; ?>
</div>