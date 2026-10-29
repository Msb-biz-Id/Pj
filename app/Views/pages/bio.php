<?php
/**
 * Bio (mobile-first, mandiri): tanpa navbar & footer.
 * Semua link internal membuka tab baru (target="_blank").
 * Layanan & produk dimuat via AJAX (HTMX load-more).
 *
 * Data: settings dinamis (about + brand + contact) + social links.
 */
$about   = $settings['about'] ?? [];
$brand   = $settings['brand'] ?? [];
$contact = $settings['contact'] ?? [];
$stats   = is_array($about['stats'] ?? null) ? $about['stats'] : [];
$socials = is_array($contact['social_links'] ?? null) ? $contact['social_links'] : [];
$waNumber = preg_replace('/[^0-9]/', '', $contact['whatsapp'] ?? '6281234567890');

$initial = mb_strtoupper(mb_substr($brand['name'] ?? 'Pusat Jersey', 0, 2, 'UTF-8'));
$handle  = strtolower(preg_replace('/[^A-Za-z0-9]+/', '', $brand['name'] ?? 'pusatjersey'));
?>
<div class="mx-auto w-full max-w-2xl space-y-10 pb-24 lg:pb-10">

    <!-- Header gaya profil -->
    <header class="px-5 pt-6">
        <div class="flex items-center gap-4">
            <span class="grid h-16 w-16 shrink-0 place-items-center rounded-2xl bg-ink font-heading text-lg font-bold text-[var(--color-bg)]"><?= esc($initial) ?></span>
            <div class="min-w-0">
                <h1 class="truncate font-heading text-[19px] font-bold tracking-tight text-ink"><?= esc($brand['name'] ?? 'Pusat Jersey') ?></h1>
                <p class="meta mt-0.5 truncate">@<?= esc($handle) ?></p>
            </div>
        </div>

        <p class="mt-4 text-[13.5px] leading-relaxed text-muted"><?= esc($about['short_desc'] ?? ($brand['tagline'] ?? '')) ?></p>

        <ul class="mt-4 flex flex-wrap gap-2">
            <li class="chip !h-7 !px-2.5 text-[11px]"><?= lucide_icon('shirt', 'w-3.5 h-3.5') ?><span><?= esc($brand['tagline'] ?? 'Custom Apparel') ?></span></li>
            <li class="chip !h-7 !px-2.5 text-[11px]"><?= lucide_icon('map-pin', 'w-3.5 h-3.5') ?><span><?= esc($contact['city'] ?? '-') ?></span></li>
            <li class="chip !h-7 !px-2.5 text-[11px]"><?= lucide_icon('badge-check', 'w-3.5 h-3.5') ?><span>Sejak <?= esc($brand['founding_year'] ?? '-') ?></span></li>
        </ul>

        <!-- Media sosial (dari settings social_links_json) -->
        <?php if (!empty($socials)): ?>
            <div class="mt-4">
                <p class="mono-label">Media Sosial</p>
                <div class="mt-2 flex flex-wrap items-center gap-2">
                    <?php foreach ($socials as $sl): ?>
                        <a href="<?= esc($sl['url'] ?? '#') ?>" target="_blank" rel="noopener"
                           title="<?= esc($sl['platform'] ?? '') ?>" aria-label="<?= esc($sl['platform'] ?? '') ?>"
                           class="icon-btn !h-9 !w-9">
                            <?= brand_icon($sl['icon'] ?? 'share-2', 'w-4 h-4') ?>
                        </a>
                    <?php endforeach; ?>
                    <a href="https://wa.me/<?= esc($waNumber) ?>" target="_blank" rel="noopener"
                       title="WhatsApp" aria-label="WhatsApp" class="icon-btn !h-9 !w-9">
                        <?= brand_icon('whatsapp', 'w-4 h-4') ?>
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </header>

    <!-- Stats horizontal-scroll (mobile) -->
    <?php if (!empty($stats)): ?>
        <section class="sm:hidden">
            <div class="flex gap-3 overflow-x-auto px-5 pb-1">
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

    <!-- List layanan (AJAX load-more) -->
    <section class="px-5">
        <div class="flex items-baseline justify-between">
            <h2 class="font-heading text-[16px] font-semibold text-ink">Layanan</h2>
            <a href="/layanan" target="_blank" rel="noopener" class="meta flex items-center gap-1 hover:text-ink">Semua <?= lucide_icon('arrow-right', 'w-3.5 h-3.5') ?></a>
        </div>
        <ul id="bio-list-services" class="mt-3 divide-y divide-line overflow-hidden rounded-2xl border border-line bg-surface"
            hx-get="<?= esc(site_url('bio/items') . '?type=services&offset=0&limit=6') ?>"
            hx-trigger="load"
            hx-swap="innerHTML">
            <li class="flex items-center gap-3 px-4 py-3.5">
                <span class="htmx-indicator flex items-center gap-2 text-[12.5px] text-muted">
                    <?= lucide_icon('loader-2', 'w-4 h-4 animate-spin') ?> Memuat layanan…
                </span>
            </li>
        </ul>
    </section>

    <!-- Slider produk (AJAX load-more) -->
    <section>
        <div class="flex items-baseline justify-between px-5">
            <h2 class="font-heading text-[16px] font-semibold text-ink">Produk Pilihan</h2>
            <a href="/produk" target="_blank" rel="noopener" class="meta flex items-center gap-1 hover:text-ink">Katalog <?= lucide_icon('arrow-right', 'w-3.5 h-3.5') ?></a>
        </div>
        <div id="bio-list-products"
             class="mt-3 -mx-5 flex snap-x snap-mandatory gap-4 overflow-x-auto px-5 pb-2 sm:mx-0 sm:grid sm:grid-cols-2 sm:gap-5 sm:overflow-visible sm:px-0"
             hx-get="<?= esc(site_url('bio/items') . '?type=products&offset=0&limit=6') ?>"
             hx-trigger="load"
             hx-swap="innerHTML">
            <span class="htmx-indicator flex items-center gap-2 px-1 text-[12.5px] text-muted">
                <?= lucide_icon('loader-2', 'w-4 h-4 animate-spin') ?> Memuat produk…
            </span>
        </div>
    </section>

    <!-- Cerita -->
    <?php if (!empty($about['story_html'])): ?>
        <section class="px-5">
            <h2 class="font-heading text-[16px] font-semibold text-ink">Cerita Kami</h2>
            <div class="prose-pj mt-3 max-w-[68ch]"><?= $about['story_html'] ?></div>
        </section>
    <?php endif; ?>

    <!-- Visi & Misi -->
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

    <!-- Info perusahaan -->
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
        <a href="/tentang-kami" target="_blank" rel="noopener" class="btn-outline mt-3 w-full">Tentang Kami Lengkap</a>
    </section>

    <!-- Sticky bottom action bar: mobile-only -->
    <div class="fixed inset-x-0 bottom-0 z-30 border-t border-line bg-[var(--color-bg)]/95 p-3 backdrop-blur-md lg:hidden">
        <div class="mx-auto flex max-w-2xl items-center gap-2 px-2">
            <a href="https://wa.me/<?= esc($waNumber) ?>" target="_blank" rel="noopener" class="btn-wa flex-1 justify-center"><?= lucide_icon('message-circle', 'w-4 h-4') ?><span>Chat WhatsApp</span></a>
            <a href="/kontak" target="_blank" rel="noopener" class="btn-outline shrink-0">Kontak</a>
        </div>
    </div>
</div>
