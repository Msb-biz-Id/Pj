<?php
$about = $settings['about'] ?? [];
$brand = $settings['brand'] ?? [];
$contact = $settings['contact'] ?? [];
$stats = $about['stats'] ?? [];
$waNumber = preg_replace('/[^0-9]/', '', $contact['whatsapp'] ?? '6281234567890');
?>
<?= view('partials/ui/page_head', [
    'eyebrow' => 'Bio',
    'title'   => $about['title'] ?? ('Bio ' . ($brand['name'] ?? 'Pusat Jersey')),
    'lede'    => $about['short_desc'] ?? '',
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
            <!-- Profil singkat (data dinamis: settings brand) -->
            <section>
                <h2 class="font-heading text-[20px] font-semibold text-ink">Profil Singkat</h2>
                <dl class="mt-4 divide-y divide-line border-y border-line text-[14px]">
                    <div class="flex justify-between gap-4 py-3"><dt class="text-faint">Nama Brand</dt><dd class="text-ink font-semibold"><?= esc($brand['name'] ?? '') ?></dd></div>
                    <div class="flex justify-between gap-4 py-3"><dt class="text-faint">Badan Hukum</dt><dd class="text-ink"><?= esc($brand['legal_name'] ?? '-') ?></dd></div>
                    <div class="flex justify-between gap-4 py-3"><dt class="text-faint">Berdiri Sejak</dt><dd class="text-ink"><?= esc($brand['founding_year'] ?? '-') ?></dd></div>
                    <div class="flex justify-between gap-4 py-3"><dt class="text-faint">Kota Workshop</dt><dd class="text-ink"><?= esc($contact['city'] ?? '-') ?></dd></div>
                    <div class="flex justify-between gap-4 py-3"><dt class="text-faint">Tagline</dt><dd class="text-ink text-right"><?= esc($brand['tagline'] ?? '-') ?></dd></div>
                </dl>
            </section>

            <!-- Cerita perusahaan (about_story_html dari admin) -->
            <?php if (!empty($about['story_html'])): ?>
                <section class="mt-10">
                    <h2 class="font-heading text-[20px] font-semibold text-ink">Cerita Kami</h2>
                    <div class="prose-pj mt-4 max-w-[68ch]"><?= $about['story_html'] ?></div>
                </section>
            <?php endif; ?>

            <!-- Visi & Misi -->
            <div class="mt-8 grid gap-4 sm:grid-cols-2">
                <div class="rounded-[var(--radius-md)] border border-line p-5">
                    <p class="font-heading text-[15px] font-semibold text-ink">Visi</p>
                    <p class="meta mt-1.5"><?= esc($about['vision'] ?? '') ?></p>
                </div>
                <div class="rounded-[var(--radius-md)] border border-line p-5">
                    <p class="font-heading text-[15px] font-semibold text-ink">Misi</p>
                    <p class="meta mt-1.5"><?= esc($about['mission'] ?? '') ?></p>
                </div>
            </div>
        </div>

        <aside class="lg:col-span-5">
            <div class="rounded-[var(--radius-lg)] border border-line p-6 lg:sticky lg:top-24">
                <p class="mono-label">Hubungi Kami</p>
                <p class="meta mt-3 leading-relaxed">Konsultasi desain gratis, sampling bahan, atau kunjungan workshop — CS kami balas cepat pada jam kerja.</p>
                <a href="https://wa.me/<?= esc($waNumber) ?>" target="_blank" hx-boost="false" class="btn-wa mt-5 w-full">Chat WhatsApp</a>
                <a href="/tentang-kami" class="btn-outline mt-2 w-full">Lihat Halaman Tentang Kami</a>
            </div>
        </aside>
    </div>
</div>
