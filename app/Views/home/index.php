<?php
$validComponents = [
    'hero', 'trust_badges', 'services', 'products',
    'portfolio_gallery', 'testimonials', 'partners', 'blog_latest', 'locations_grid', 'cta_wa'
];
?>
<!-- Homepage View (Editorial Minimal SPA) -->
<div>
    <?php if (!empty($sections)): ?>
        <?php foreach ($sections as $section): ?>
            <?php 
            $secKey = $section['section_key'];
            if (in_array($secKey, $validComponents, true)): ?>
                <?= view('components/' . $secKey, [
                    'section'      => $section,
                    'services'     => $services ?? [],
                    'products'     => $products ?? [],
                    'portfolios'   => $portfolios ?? [],
                    'posts'        => $posts ?? [],
                    'testimonials' => $testimonials ?? [],
                    'faqs'         => $faqs ?? [],
                    'locations'    => $locations ?? [],
                    'settings'     => $settings ?? []
                ]) ?>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
