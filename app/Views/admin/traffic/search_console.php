<?php
/**
 * Laporan Google Search Console (klik, impression, CTR, posisi).
 * Data: $gscConfigured, $gscSite, $days, $gscPages, $gscQueries, $gscTotals, $gscError
 */
$configured = $gscConfigured ?? false;
$pages   = $gscPages ?? [];
$queries = $gscQueries ?? [];
$totals  = $gscTotals ?? ['clicks' => 0, 'impr' => 0];
?>
<div class="space-y-6">
    <div class="space-y-1">
        <div class="flex items-center gap-2">
            <span class="mono-label text-[10px]">01 · ANALITIK</span>
            <span class="text-faint text-xs">/</span>
            <span class="text-xs text-muted">Google Search Console</span>
        </div>
        <h1 class="font-heading text-2xl font-bold tracking-tight text-ink">Laporan Search Console</h1>
        <p class="text-xs text-muted max-w-2xl leading-relaxed">
            Data diambil langsung dari Google Search Console API (service account).
            pemilik properti <span class="font-mono"><?= esc($gscSite ?? '-') ?></span>.
        </p>
    </div>

    <div class="card p-4 flex flex-wrap items-center gap-2">
        <span class="mono-label text-[10px]">PERIODE</span>
        <?php foreach ([7, 28, 90] as $d): ?>
            <a href="<?= esc(site_url('admin/traffic/search-console') . '?days=' . $d) ?>"
               class="chip !h-7 !px-3 text-[11px] <?= (int) ($days ?? 28) === $d ? 'border-ink/40 font-semibold text-ink' : '' ?>">
                <?= $d ?> hari
            </a>
        <?php endforeach; ?>
        <a href="<?= esc(site_url('admin/traffic')) ?>" class="chip !h-7 !px-3 text-[11px] ml-auto">← Kembali ke Analitik</a>
    </div>

    <?php if (!$configured): ?>
        <?= view('partials/ui/empty', [
            'icon'  => 'plug',
            'title' => 'Search Console belum dikonfigurasi',
            'desc'  => 'Isi GA4/Search Console di Admin → Pengaturan → SEKSI 06: properti Search Console, token verifikasi, dan path service account JSON.',
        ]) ?>
    <?php else: ?>
        <?php if (!empty($gscError)): ?>
            <div class="card border-rose-500/40 bg-rose-500/5 p-4 text-[12.5px] text-rose-400">
                <p class="font-semibold">Gagal mengambil data Search Console</p>
                <p class="mt-1 text-muted"><?= esc($gscError) ?></p>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div class="card p-4">
                <p class="mono-label text-[10px]">TOTAL KLIK</p>
                <p class="stat-num mt-1 text-2xl"><?= number_format((int) $totals['clicks']) ?></p>
            </div>
            <div class="card p-4">
                <p class="mono-label text-[10px]">IMPRESSION</p>
                <p class="stat-num mt-1 text-2xl"><?= number_format((int) $totals['impr']) ?></p>
            </div>
            <div class="card p-4">
                <p class="mono-label text-[10px]">CTR</p>
                <p class="stat-num mt-1 text-2xl"><?= $totals['impr'] > 0 ? number_format(($totals['clicks'] / $totals['impr']) * 100, 2, ',', '.') : '0,00' ?>%</p>
            </div>
            <div class="card p-4">
                <p class="mono-label text-[10px]">PERIODE</p>
                <p class="mt-1 text-[13px] text-ink"><?= (int) ($days ?? 28) ?> hari terakhir</p>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <!-- Per halaman -->
            <div class="card overflow-hidden">
                <div class="border-b border-line p-5"><p class="mono-label text-[10px]">PER HALAMAN</p></div>
                <?php if (empty($pages)): ?>
                    <div class="p-5"><?= view('partials/ui/empty', ['icon' => 'search', 'title' => 'Belum ada data', 'desc' => 'Data muncul setelah Search Console mengindeks halaman.']) ?></div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-[var(--color-surface-alt)] text-muted font-semibold border-b border-line">
                                    <th class="p-4 mono-label text-[10px]">HALAMAN</th>
                                    <th class="p-4 mono-label text-[10px]">KLIK</th>
                                    <th class="p-4 mono-label text-[10px]">IMPR</th>
                                    <th class="p-4 mono-label text-[10px]">CTR</th>
                                    <th class="p-4 mono-label text-[10px]">POSISI</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-line">
                                <?php foreach ($pages as $row): ?>
                                    <tr class="hover:bg-[var(--color-surface-alt)]/50 transition-colors">
                                        <td class="p-4 max-w-[260px] truncate text-muted" title="<?= esc($row['label']) ?>"><?= esc($row['label']) ?></td>
                                        <td class="p-4 font-mono tabular-nums text-ink"><?= number_format($row['clicks']) ?></td>
                                        <td class="p-4 font-mono tabular-nums text-muted"><?= number_format($row['impr']) ?></td>
                                        <td class="p-4 font-mono tabular-nums text-muted"><?= number_format($row['ctr'] * 100, 2, ',', '.') ?>%</td>
                                        <td class="p-4 font-mono tabular-nums text-muted"><?= number_format($row['position'], 1, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Per query -->
            <div class="card overflow-hidden">
                <div class="border-b border-line p-5"><p class="mono-label text-[10px]">PER QUERY PENCARIAN</p></div>
                <?php if (empty($queries)): ?>
                    <div class="p-5"><?= view('partials/ui/empty', ['icon' => 'search', 'title' => 'Belum ada query', 'desc' => 'Data query muncul setelah ada pencarian dari Google.']) ?></div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-[var(--color-surface-alt)] text-muted font-semibold border-b border-line">
                                    <th class="p-4 mono-label text-[10px]">QUERY</th>
                                    <th class="p-4 mono-label text-[10px]">KLIK</th>
                                    <th class="p-4 mono-label text-[10px]">IMPR</th>
                                    <th class="p-4 mono-label text-[10px]">CTR</th>
                                    <th class="p-4 mono-label text-[10px]">POSISI</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-line">
                                <?php foreach ($queries as $row): ?>
                                    <tr class="hover:bg-[var(--color-surface-alt)]/50 transition-colors">
                                        <td class="p-4 max-w-[260px] truncate text-muted" title="<?= esc($row['label']) ?>"><?= esc($row['label']) ?></td>
                                        <td class="p-4 font-mono tabular-nums text-ink"><?= number_format($row['clicks']) ?></td>
                                        <td class="p-4 font-mono tabular-nums text-muted"><?= number_format($row['impr']) ?></td>
                                        <td class="p-4 font-mono tabular-nums text-muted"><?= number_format($row['ctr'] * 100, 2, ',', '.') ?>%</td>
                                        <td class="p-4 font-mono tabular-nums text-muted"><?= number_format($row['position'], 1, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
