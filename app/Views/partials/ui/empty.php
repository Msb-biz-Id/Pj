<?php
// Empty state editorial konsisten.
?>
<div class="flex flex-col items-center gap-2 rounded-[var(--radius-lg)] border border-dashed border-line bg-surface-alt/60 px-6 py-14 text-center">
  <span class="icon-btn pointer-events-none"><?= lucide_icon($icon ?? 'inbox', 'w-4 h-4') ?></span>
  <p class="font-heading text-[15px] font-semibold text-ink"><?= esc($title ?? 'Belum ada data') ?></p>
  <p class="meta max-w-sm"><?= esc($desc ?? '') ?></p>
</div>
