<?php
// $btnClass: kelas tambahan untuk tombol (mis. '!h-8 !w-8' di admin).
$btnClass = $btnClass ?? '';
$modes = [
    ['id' => 'auto', 'label' => 'Auto', 'hint' => 'Ikuti sistem', 'icon' => 'monitor'],
    ['id' => 'light', 'label' => 'Normal', 'hint' => 'Mode terang', 'icon' => 'sun'],
    ['id' => 'dark', 'label' => 'Dark', 'hint' => 'Mode gelap', 'icon' => 'moon'],
];
?>
<div x-data="themeSwitcher()" class="relative" @click.outside="open = false">
    <button @click="open = !open" :aria-expanded="open" aria-haspopup="true" aria-label="Pilih mode tema" class="icon-btn <?= esc($btnClass) ?>">
        <template x-if="mode === 'auto'"><?= lucide_icon('monitor', 'w-4 h-4') ?></template>
        <template x-if="mode === 'light'"><?= lucide_icon('sun', 'w-4 h-4') ?></template>
        <template x-if="mode === 'dark'"><?= lucide_icon('moon', 'w-4 h-4') ?></template>
    </button>
    <div x-show="open" x-cloak x-transition.origin.top.right
         class="absolute right-0 z-50 mt-2 w-44 rounded-[var(--radius-md)] border border-line bg-surface p-1 shadow-xl">
        <?php foreach ($modes as $m): ?>
            <button type="button" @click="setMode('<?= $m['id'] ?>')"
                    :class="mode === '<?= $m['id'] ?>' ? 'bg-surface-alt font-semibold text-ink' : 'text-muted hover:text-ink'"
                    class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-[13px] transition-colors">
                <?= lucide_icon($m['icon'], 'h-3.5 w-3.5 shrink-0') ?>
                <span class="flex-1"><?= esc($m['label']) ?></span>
                <span class="meta text-[11px]"><?= esc($m['hint']) ?></span>
                <span x-show="mode === '<?= $m['id'] ?>'" class="shrink-0"><?= lucide_icon('check', 'h-3.5 w-3.5') ?></span>
            </button>
        <?php endforeach; ?>
    </div>
</div>
