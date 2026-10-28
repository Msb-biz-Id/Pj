<?php
$partners = $settings['partners'] ?? [];
$items = array_values(array_filter($partners, static fn ($p) => !empty($p['name'])));
if (empty($items)) {
    return;
}
$pTitle = $title ?? 'Mitra & Partner Kami';
$pLede  = $lede ?? 'Kolaborasi bersama klub, komunitas, dan mitra bisnis di seluruh Indonesia.';
?>
<?= view('partials/ui/section_head', ['eyebrow' => 'Mitra', 'title' => $pTitle, 'lede' => $pLede, 'align' => 'center']) ?>
<div class="swiper swiper-container-init mt-8 overflow-hidden">
    <div class="swiper-wrapper">
        <?php foreach ($items as $p): ?>
            <div class="swiper-slide">
                <?php $inner = '<figure class="card flex h-full flex-col items-center justify-center gap-3 p-6 text-center">'
                    . (!empty($p['logo'])
                        ? '<img src="' . esc($p['logo'], 'attr') . '" alt="' . esc($p['name'], 'attr') . '" loading="lazy" class="h-12 w-auto max-w-[140px] object-contain">'
                        : '<span class="grid h-12 w-12 place-items-center rounded-full bg-ink font-heading text-[13px] font-bold text-[var(--color-bg)]">' . esc(mb_substr($p['name'], 0, 2, 'UTF-8')) . '</span>')
                    . '<figcaption class="text-[13.5px] font-semibold text-ink">' . esc($p['name']) . '</figcaption>'
                    . '</figure>'; ?>
                <?php if (!empty($p['url'])): ?>
                    <a href="<?= esc($p['url'], 'attr') ?>" target="_blank" hx-boost="false" class="block h-full"><?= $inner ?></a>
                <?php else: ?>
                    <?= $inner ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
