<?php
// UI Kit — satu sumber gaya publik. Jangan duplikasi markup di tiap view.
// $eyebrow, $title, $lede, $align(left|center), $action (html opsional)
$align = $align ?? 'left';
$wrap = $align === 'center' ? 'mx-auto text-center max-w-2xl' : 'max-w-2xl';
?>
<div class="<?= $wrap ?>" data-reveal>
  <?php if (!empty($eyebrow)): ?>
    <p class="eyebrow <?= $align === 'center' ? 'justify-center' : '' ?>"><?= esc($eyebrow) ?></p>
  <?php endif; ?>
  <h2 class="h-section mt-4"><?= esc($title ?? '') ?></h2>
  <?php if (!empty($lede)): ?>
    <p class="lede <?= $align === 'center' ? 'mx-auto' : '' ?>"><?= esc($lede) ?></p>
  <?php endif; ?>
  <?php if (!empty($action)): ?>
    <div class="mt-5 <?= $align === 'center' ? 'flex justify-center' : '' ?>"><?= $action ?></div>
  <?php endif; ?>
</div>
