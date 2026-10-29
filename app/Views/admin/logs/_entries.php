<?php
// Fragment entri log (dipakai HTMX refresh: hx-target #log-entries).
// Semua data memakai prefix "log" — lihat catatan merge view-data di Admin\Logs.
$entries     = $logEntries ?? [];
$activeFile  = $logActiveFile ?? '';
$levelFilter = $logLevelFilter ?? '';

$levelColor = [
    'EMERGENCY' => 'bg-rose-500/15 text-rose-500 border-rose-500/30',
    'ALERT'     => 'bg-rose-500/15 text-rose-500 border-rose-500/30',
    'CRITICAL'  => 'bg-rose-500/15 text-rose-500 border-rose-500/30',
    'ERROR'     => 'bg-rose-500/10 text-rose-400 border-rose-500/30',
    'WARNING'   => 'bg-amber-500/10 text-amber-500 border-amber-500/30',
    'NOTICE'    => 'bg-sky-500/10 text-sky-400 border-sky-500/30',
    'INFO'      => 'bg-sky-500/10 text-sky-400 border-sky-500/30',
    'DEBUG'     => 'bg-slate-500/10 text-slate-400 border-slate-500/30',
];
?>
<?php if (empty($entries)): ?>
    <?= view('partials/ui/empty', ['icon' => 'file-text', 'title' => 'Tidak ada log', 'desc' => $activeFile === '' ? 'Belum ada file log di writable/logs.' : 'Tidak ada entri untuk filter ini pada ' . $activeFile . '.']) ?>
<?php else: ?>
    <div class="card divide-y divide-line">
        <?php foreach ($entries as $e): ?>
            <div class="p-4 hover:bg-[var(--color-surface-alt)]/40 transition-colors">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center rounded-full border px-2 py-0.5 font-mono text-[10px] font-semibold <?= $levelColor[$e['level']] ?? 'bg-slate-500/10 text-slate-400 border-slate-500/30' ?>">
                        <?= esc($e['level']) ?>
                    </span>
                    <span class="font-mono text-[11px] text-faint"><?= esc($e['time']) ?></span>
                </div>
                <pre class="mt-2 max-w-full overflow-x-auto whitespace-pre-wrap break-words font-mono text-[11.5px] leading-relaxed text-muted"><?= esc($e['message']) ?></pre>
            </div>
        <?php endforeach; ?>
    </div>
    <p class="meta mt-3 text-center">Menampilkan <?= number_format(count($entries)) ?> entri terbaru<?= $levelFilter !== '' ? ' (filter: ' . esc($levelFilter) . ')' : '' ?> dari <?= esc($activeFile) ?></p>
<?php endif; ?>
