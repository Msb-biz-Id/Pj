<?php
/**
 * Bio (mobile-first): halaman ini SENGAJA beda UI dari halaman lain
 * (header magazine, badge status, list layanan, slider produk, sticky CTA).
 * Desktop pun tetap rapi via max-w-2xl, tetapi struktur & ritual visualnya mobile-first.
 *
 * Data: settings dinamis (about + brand + contact) + layanan & produk dari DB.
 * Fallback query internal dipakai HANYA bila controller tidak mengirim data.
 */
$about   = $settings['about'] ?? [];
$brand   = $settings['brand'] ?? [];
$contact = $settings['contact'] ?? [];
$stats   = is_array($about['stats'] ?? null) ? $about['stats'] : [];
$waNumber = preg_replace('/[^0-9]/', '', $contact['whatsapp'] ?? '6281234567890');

if (!isset($services)) {
    try {
        $services = (new \App\Models\ServiceModel())->where('status', 'published')->orderBy('created_at', 'DESC')->findAll(6);
    } catch (\Throwable $e) {
        $services = [];
    }
}
if (!isset($products)) {
    try {
        $products = (new \App\Models\ProductModel())->where('status', 'published')->orderBy('created_at', 'DESC')->findAll(8);
    } catch (\Throwable $e) {
        $products = [];
    }
}

$initial = mb_strtoupper(mb_substr($brand['name'] ?? 'Pusat Jersey', 0, 2, 'UTF-8'));
?>
<div class="mx-auto w-full max-w-2xl space-y-10 pb-24 lg:pb-10">

    <!-- Header gaya profil (bukan hero/eyebrow halaman lain) -->
    <header class="px-5 pt-6">
        <div class="flex items-center gap-4">
            <span class="grid h-16 w-16 shrink-0 place-items-center rounded-2xl bg-ink font-heading text-lg font-bold text-[var(--color-bg)]"><?= esc($initial) ?></span>
            <div class="min-w-0">
                <h1 class="truncate font-heading text-[19px] font-bold tracking-tight text-ink"><?= esc($brand['name'] ?? 'Pusat Jersey') ?></h1>
                <p class="meta mt-0.5 truncate">@<?= esc(strtolower(preg_replace('/[^A-Za-z0-9]+/', '', $brand['name'] ?? 'pusatjersey'))) ?></p>
            </div>
        </div>

        <p class="mt-4 text-[13.5px] leading-relaxed text-muted"><?= esc($about['short_desc'] ?? ($brand['tagline'] ?? '')) ?></p>

        <ul class="mt-4 flex flex-wrap gap-2">
            <li class="chip !h-7 !px-2.5 text-[11px]"><?= lucide_icon('shirt', 'w-3.5 h-3.5') ?><span><?= esc($brand['tagline'] ?? 'Custom Apparel') ?></span></li>
            <li class="chip !h-7 !px-2.5 text-[11px]"><?= lucide_icon('map-pin', 'w-3.5 h-3.5') ?><span><?= esc($contact['city'] ?? '-') ?></span></li>
            <li class="chip !h-7 !px-2.5 text-[11px]"><?= lucide_icon('badge-check', 'w-3.5 h-3.5') ?><span>Sejak <?= esc($brand['founding_year'] ?? '-') ?></span></li>
        </ul>
    </header>

    <!-- Stats horizontal-scroll (mobile-only) -->
    <?php if (!empty($stats)): ?>
        <section class="sm:hidden">
            <div class="-mx-0 flex gap-3 overflow-x-auto px-5 pb-1">
                <?php foreach ($stats as $st): ?>
                    <div class="card min-w-[42%] shrink-0 p-4 text-center">
                        <p class="stat-num text-xl"><?= esc($st['value'] ?? '') ?></p>
                        <p class="meta mt-1"><?= esc($st['label'] ?? '') ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        <section class="hidden grid-cols-4 gap-4 px-5 sm:grid">
            <?php foreach (array_slice($stats, 0, 4) as $st): ?>
                <div><p class="meta"><?= esc($st['label'] ?? '') ?></p><p class="stat-num mt-1 text-xl"><?= esc($st['value'] ?? '') ?></p></div>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>

    <!-- List layanan -->
    <?php if (!empty($services)): ?>
        <section class="px-5">
            <div class="flex items-baseline justify-between">
                <h2 class="font-heading text-[16px] font-semibold text-ink">Layanan</h2>
                <a href="/layanan" class="meta flex items-center gap-1 hover:text-ink">Semua <?= lucide_icon('arrow-right', 'w-3.5 h-3.5') ?></a>
            </div>
            <ul class="mt-3 divide-y divide-line overflow-hidden rounded-2xl border border-line bg-surface">
                <?php foreach ($services as $svc): ?>
                    <li>
                        <a href="/layanan/<?= esc($svc['slug']) ?>" class="flex items-center gap-3 px-4 py-3.5 transition-colors hover:bg-[var(--color-surface-alt)]/50">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[var(--color-surface-alt)] text-ink"><?= lucide_icon('shirt', 'w-4 h-4') ?></span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-[14px] font-semibold text-ink"><?= esc($svc['title']) ?></span>
                                <span class="meta mt-0.5 line-clamp-1 block"><?= esc($svc['short_desc'] ?? '') ?></span>
                            </span>
                            <span class="shrink-0 text-right">
                                <span class="meta block">mulai</span>
                                <span class="block text-[12.5px] font-semibold text-ink tabular-nums">Rp <?= number_format((float) ($svc['starting_price'] ?? 0), 0, ',', '.') ?></span>
                            </span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php endif; ?>

    <!-- Slider produk (snap-scroll, mobile-only style; desktop jadi grid) -->
    <?php if (!empty($products)): ?>
        <section>
            <div class="flex items-baseline justify-between px-5">
                <h2 class="font-heading text-[16px] font-semibold text-ink">Produk Pilihan</h2>
                <a href="/produk" class="meta flex items-center gap-1 hover:text-ink">Katalog <?= lucide_icon('arrow-right', 'w-3.5 h-3.5') ?></a>
            </div>
            <div class="mt-3 -mx-5 flex snap-x snap-mandatory gap-4 overflow-x-auto px-5 pb-2 sm:mx-0 sm:grid sm:grid-cols-2 sm:gap-5 sm:overflow-visible sm:px-0">
                <?php foreach (array_slice($products, 0, 8) as $product): ?>
                    <?php $product['slug'] = $product['slug'] ?? '#'; ?>
                    <article class="card group flex shrink-0 snap-start basis-[70%] flex-col sm:shrink sm:basis-auto">
                        <a href="/produk/<?= esc($product['slug']) ?>" class="card-media aspect-[4/3]" tabindex="-1" aria-hidden="true">
                            <img src="<?= esc(!empty($product['featured_image']) ? $product['featured_image'] : 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=800&q=80') ?>" alt="<?= esc($product['name'] ?? 'Produk') ?>" loading="lazy">
                        </a>
                        <div class="flex flex-1 flex-col gap-1.5 p-4">
                            <p class="meta text-[11px] uppercase tracking-[0.12em]"><?= esc($product['category_name'] ?? 'Jersey') ?></p>
                            <a href="/produk/<?= esc($product['slug']) ?>" class="card-title line-clamp-2 text-[13.5px]"><?= esc($product['name'] ?? 'Produk') ?></a>
                            <span class="price mt-1">Rp <?= number_format((float) ($product['discount_price'] ?? $product['price'] ?? 0), 0, ',', '.') ?></span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- Cerita -->
    <?php if (!empty($about['story_html'])): ?>
        <section class="px-5">
            <h2 class="font-heading text-[16px] font-semibold text-ink">Cerita Kami</h2>
            <div class="prose-pj mt-3 max-w-[68ch]"><?= $about['story_html'] ?></div>
        </section>
    <?php endif; ?>

    <!-- Visi & Misi (kartu bertumpuk, bukan grid 2 kolom halaman lain) -->
    <section class="space-y-3 px-5">
        <?php if (!empty($about['vision'])): ?>
            <div class="rounded-2xl border border-line bg-surface p-5">
                <p class="mono-label">Visi</p>
                <p class="mt-2 text-[13.5px] leading-relaxed text-muted"><?= esc($about['vision']) ?></p>
            </div>
        <?php endif; ?>
        <?php if (!empty($about['mission'])): ?>
            <div class="rounded-2xl border border-line bg-surface p-5">
                <p class="mono-label">Misi</p>
                <p class="mt-2 text-[13.5px] leading-relaxed text-muted"><?= esc($about['mission']) ?></p>
            </div>
        <?php endif; ?>
    </section>

    <!-- Profil legal -->
    <section class="px-5">
        <div class="rounded-2xl border border-line p-5">
            <p class="mono-label">Info Perusahaan</p>
            <dl class="mt-3 space-y-2 text-[13px]">
                <div class="flex justify-between gap-4"><dt class="text-faint">Badan hukum</dt><dd class="text-ink text-right"><?= esc($brand['legal_name'] ?? '-') ?></dd></div>
                <div class="flex justify-between gap-4"><dt class="text-faint">Kota workshop</dt><dd class="text-ink text-right"><?= esc($contact['city'] ?? '-') ?></dd></div>
                <div class="flex justify-between gap-4"><dt class="text-faint">Email</dt><dd class="text-ink text-right"><?= esc($contact['email'] ?? '-') ?></dd></div>
                <div class="flex justify-between gap-4"><dt class="text-faint">Alamat</dt><dd class="text-ink text-right"><?= esc($contact['address'] ?? '-') ?></dd></div>
            </dl>
        </div>
        <a href="/tentang-kami" class="btn-outline mt-3 w-full">Tentang Kami Lengkap</a>
    </section>

    <!-- Sticky bottom action bar: mobile-only -->
    <div class="fixed inset-x-0 bottom-0 z-30 border-t border-line bg-[var(--color-bg)]/95 p-3 backdrop-blur-md lg:hidden">
        <div class="mx-auto flex max-w-2xl items-center gap-2 px-2">
            <a href="https://wa.me/<?= esc($waNumber) ?>" target="_blank" hx-boost="false" class="btn-wa flex-1 justify-center"><?= lucide_icon('message-circle', 'w-4 h-4') ?><span>Chat WhatsApp</span></a>
            <a href="/kontak" class="btn-outline shrink-0">Kontak</a>
        </div>
    </div>
</div>
