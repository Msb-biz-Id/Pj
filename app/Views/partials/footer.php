<?php
$brand = $settings['brand'] ?? [];
$contact = $settings['contact'] ?? [];
$openingHours = $contact['opening_hours'] ?? [];
$socialLinks = $contact['social_links'] ?? [];
$waNumber = preg_replace('/[^0-9]/', '', $contact['whatsapp'] ?? '6281234567890');

$footerNav = [
    ['label' => 'Katalog Produk', 'url' => '/produk'],
    ['label' => 'Layanan Custom', 'url' => '/layanan'],
    ['label' => 'Portofolio', 'url' => '/portofolio'],
    ['label' => 'Blog & Tips', 'url' => '/blog'],
    ['label' => 'Tentang Kami', 'url' => '/tentang-kami'],
    ['label' => 'Kontak', 'url' => '/kontak'],
];
?>

<footer class="border-t border-line">
    <div class="shell py-12">
        <div class="flex flex-col gap-4 pb-10 md:flex-row md:items-end md:justify-between">
            <div class="max-w-md">
                <p class="eyebrow">Mulai pesanan</p>
                <h2 class="h-section mt-3" style="font-size:1.4rem">Siap membuat jersey tim Anda?</h2>
                <p class="meta mt-2">Konsultasi desain gratis. Balasan cepat pada jam kerja.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="https://wa.me/<?= esc($waNumber) ?>" target="_blank" hx-boost="false" class="btn-wa btn-sm">
                    <?= lucide_icon('message-circle', 'w-4 h-4') ?>
                    <span>Chat WhatsApp</span>
                </a>
                <a href="/kontak" class="btn-outline btn-sm">Halaman Kontak</a>
            </div>
        </div>

        <div class="grid gap-10 border-t border-line py-10 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <a href="/" class="flex items-center gap-3">
                    <span class="grid h-9 w-9 place-items-center rounded-full bg-ink font-heading text-[12px] font-bold text-[var(--color-bg)]">PJ</span>
                    <span class="font-heading text-[15px] font-semibold tracking-tight text-ink"><?= esc($brand['name'] ?? 'Pusat Jersey') ?></span>
                </a>
                <p class="meta mt-4 max-w-[26ch]"><?= esc($brand['tagline'] ?? 'Produsen custom jersey & apparel olahraga di Indonesia.') ?></p>
            </div>
            <nav aria-label="Navigasi footer">
                <h3 class="mono-label">Navigasi</h3>
                <ul class="mt-4 space-y-2.5">
                    <?php foreach ($footerNav as $item): ?>
                        <li><a href="<?= esc($item['url']) ?>" class="text-[13.5px] text-muted hover:text-ink"><?= esc($item['label']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>
            <div>
                <h3 class="mono-label">Operasional</h3>
                <ul class="meta mt-4 space-y-2">
                    <?php if (!empty($openingHours)): ?>
                        <?php foreach ($openingHours as $oh): ?>
                            <li class="flex justify-between gap-4"><span><?= esc($oh['days']) ?></span><span><?= esc($oh['hours']) ?></span></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="flex justify-between gap-4"><span>Senin – Sabtu</span><span>08.00 – 17.00</span></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div>
                <h3 class="mono-label">Kontak</h3>
                <ul class="meta mt-4 space-y-2.5">
                    <li><?= esc($contact['address'] ?? 'Jl. Raya Jersey No. 88') ?>, <?= esc($contact['city'] ?? 'Jakarta') ?></li>
                    <li><a href="mailto:<?= esc($contact['email'] ?? 'info@pusatjersey.com') ?>" class="hover:text-ink"><?= esc($contact['email'] ?? 'info@pusatjersey.com') ?></a></li>
                </ul>
                <?php if (!empty($socialLinks)): ?>
                    <div class="mt-4 flex items-center gap-2">
                        <?php foreach ($socialLinks as $sl): ?>
                            <a href="<?= esc($sl['url']) ?>" target="_blank" hx-boost="false" title="<?= esc($sl['platform']) ?>" aria-label="<?= esc($sl['platform']) ?>" class="icon-btn !h-9 !w-9"><?= brand_icon($sl['icon'] ?? 'share-2', 'w-4 h-4') ?></a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="flex flex-col gap-2 border-t border-line pt-5 text-[12px] text-faint sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; <?= esc($brand['founding_year'] ?? '2018') ?> – <?= date('Y') ?> <?= esc($brand['legal_name'] ?? 'PT Pusat Jersey Indonesia') ?></p>
            <a href="/admin/dashboard" class="hover:text-ink">Admin</a>
        </div>
    </div>
</footer>
