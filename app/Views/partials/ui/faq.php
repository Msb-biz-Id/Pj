<?php
// FAQ accordion editorial. $items = [['question','answer']], $id unik per halaman.
$id = $id ?? 'faq';
?>
<div class="divide-y divide-line rounded-[var(--radius-lg)] border border-line bg-surface">
  <?php foreach (($items ?? []) as $i => $fq): ?>
    <div x-data="{ open: false }" class="px-5 sm:px-6">
      <button type="button" @click="open = !open" :aria-expanded="open" aria-controls="<?= $id ?>-<?= $i ?>" class="flex w-full items-center justify-between gap-4 py-4 text-left">
        <span class="text-[14px] font-semibold text-ink"><?= esc($fq['question']) ?></span>
        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full border border-line text-muted transition-transform duration-200" :class="open && 'rotate-45 border-ink/40 text-ink'"><?= lucide_icon('plus', 'w-4 h-4') ?></span>
      </button>
      <div id="<?= $id ?>-<?= $i ?>" x-show="open" x-cloak class="pb-5 text-[13.5px] leading-relaxed text-muted"><?= esc($fq['answer']) ?></div>
    </div>
  <?php endforeach; ?>
</div>
