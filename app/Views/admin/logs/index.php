<?php
// Halaman viewer log backend. Semua data memakai prefix "log" (merge view-data CI4).
$logFiles       = $logFiles ?? [];
$logActiveFile  = $logActiveFile ?? '';
$logLevelFilter = $logLevelFilter ?? '';
$logStats       = $logStats ?? [];
$logTotal       = $logTotal ?? 0;

$levelChips = [
    ''        => 'Semua',
    'ERROR'   => 'Error',
    'WARNING' => 'Warning',
    'INFO'    => 'Info',
    'DEBUG'   => 'Debug',
];
$qs = static function (string $level) use ($logActiveFile): string {
    return site_url('admin/logs') . '?file=' . urlencode($logActiveFile) . ($level !== '' ? '&level=' . urlencode($level) : '');
};
?>
<div class="space-y-6">
    <div class="space-y-1">
        <div class="flex items-center gap-2">
            <span class="mono-label text-[10px]">01 · SISTEM</span>
            <span class="text-faint text-xs">/</span>
            <span class="text-xs text-muted">Log Error Backend</span>
        </div>
        <h1 class="font-heading text-2xl font-bold tracking-tight text-ink">Log Error Sistem</h1>
        <p class="text-xs text-muted">Pembacaan langsung <span class="font-mono">writable/logs/*.log</span> — diperbarui otomatis tiap 15 detik.</p>
    </div>

    <!-- Toolbar -->
    <div class="card p-4 flex flex-wrap items-center gap-3">
        <label class="lbl mb-0" for="log-file">File Log</label>
        <select id="log-file" class="field !w-auto text-xs" onchange="if (this.value) window.location.href = <?= esc(json_encode(site_url('admin/logs') . '?file=')) ?> + encodeURIComponent(this.value)">
            <?php foreach ($logFiles as $f): ?>
                <option value="<?= esc($f['name']) ?>" <?= $f['name'] === $logActiveFile ? 'selected' : '' ?>>
                    <?= esc($f['name']) ?> (<?= number_format($f['size'] / 1024, 1, ',', '.') ?> KB)
                </option>
            <?php endforeach; ?>
            <?php if (empty($logFiles)): ?><option value="">— belum ada log —</option><?php endif; ?>
        </select>

        <div class="flex flex-wrap items-center gap-1.5">
            <?php foreach ($levelChips as $lv => $label): ?>
                <a href="<?= esc($qs($lv)) ?>"
                   class="chip !h-7 !px-2.5 text-[11px] <?= $logLevelFilter === $lv ? 'border-ink/40 font-semibold text-ink' : '' ?>">
                    <?= esc($label) ?>
                    <?php if ($lv !== '' && ($logStats[$lv] ?? 0) > 0): ?>
                        <span class="font-mono text-[10px] text-faint"><?= (int) $logStats[$lv] ?></span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>

        <button type="button"
                hx-get="<?= esc($qs($logLevelFilter)) ?>"
                hx-target="#log-entries"
                hx-swap="innerHTML"
                class="icon-btn !h-8 !w-8 ml-auto"
                title="Muat ulang sekarang">
            <?= lucide_icon('rotate-ccw', 'w-3.5 h-3.5') ?>
        </button>
    </div>

    <!-- Ringkasan level -->
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="card p-4"><p class="mono-label text-[10px]">TOTAL ENTRI</p><p class="font-heading text-xl font-bold text-ink mt-1"><?= number_format($logTotal) ?></p></div>
        <div class="card p-4"><p class="mono-label text-[10px]">ERROR</p><p class="font-heading text-xl font-bold text-rose-400 mt-1"><?= number_format($logStats['ERROR'] ?? 0) ?></p></div>
        <div class="card p-4"><p class="mono-label text-[10px]">WARNING</p><p class="font-heading text-xl font-bold text-amber-500 mt-1"><?= number_format($logStats['WARNING'] ?? 0) ?></p></div>
        <div class="card p-4"><p class="mono-label text-[10px]">INFO + DEBUG</p><p class="font-heading text-xl font-bold text-sky-400 mt-1"><?= number_format(($logStats['INFO'] ?? 0) + ($logStats['DEBUG'] ?? 0)) ?></p></div>
    </div>

    <!-- Entri (auto-refresh 15 detik) -->
    <div id="log-entries"
         hx-get="<?= esc($qs($logLevelFilter)) ?>"
         hx-trigger="every 15s"
         hx-swap="innerHTML">
        <?= $logEntriesHtml ?? '' ?>
    </div>
</div>
