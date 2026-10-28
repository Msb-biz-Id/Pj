<?php
$settingsJson = !empty($section['settings_json']) ? json_decode($section['settings_json'], true) : [];
?>
<section class="section border-b border-line bg-surface-alt/50">
    <div class="shell">
        <?= view('partials/partners_slider', [
            'settings' => $settings,
            'title'    => $settingsJson['title'] ?? 'Mitra & Partner Kami',
            'lede'     => $settingsJson['lede'] ?? 'Kolaborasi bersama klub, komunitas, dan mitra bisnis di seluruh Indonesia.',
        ]) ?>
    </div>
</section>
