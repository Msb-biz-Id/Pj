<?php
/**
 * Analitik traffic & konversi (data lokal, tanpa vendor eksternal).
 * Data: $range, $ranges, $since, $ctaTotal, $ctaByChannel, $ctaTopSources,
 *       $ctaTopProducts, $ctaDaily, $ctaRecent, $pageViews*, $trafficTop*, $trafficSources*, $trafficDaily*
 */
$range = $range ?? 30;
$ranges = $ranges ?? [7, 30, 90];

$channelLabel = [
    'whatsapp'  => 'WhatsApp',
    'shopee'    => 'Shopee',
    'tokopedia' => 'Tokopedia',
    'tiktok'    => 'TikTok',
    'affiliate' => 'Affiliate',
];
$channelIcon = [
    'whatsapp'  => 'message-circle',
    'shopee'    => 'shopping-bag',
    'tokopedia' => 'store',
    'tiktok'    => 'music-2',
    'affiliate' => 'link',
];
$maxChannel = 0;
foreach ($ctaByChannel as $row) {
    $maxChannel = max($maxChannel, (int) $row['total']);
}
$maxSource = 0;
foreach ($ctaTopSources as $row) {
    $maxSource = max($maxSource, (int) $row['total']);
}
?>
<div class="space-y-6">
    <div class="space-y-1">
        <div class="flex items-center gap-2">
            <span class="mono-label text-[10px]">01 · ANALITIK</span>
            <span class="text-faint text-xs">/</span>
            <span class="text-xs text-muted">Traffic & Konversi</span>
        </div>
        <h1 class="font-heading text-2xl font-bold tracking-tight text-ink">Analitik Traffic & Konversi</h1>
        <p class="text-xs text-muted max-w-2xl leading-relaxed">
            Data dihitung dari pencatatan lokal: <span class="font-mono">cta_logs</span> (klik tombol CTA) dan
            <span class="font-mono">page_views</span> (kunjungan halaman). Bot & crawler tidak dihitung.
        </p>
    </div>

    <!-- Filter periode -->
    <div class="card p-4 flex flex-wrap items-center gap-2">
        <span class="mono-label text-[10px]">PERIODE</span>
        <?php foreach ($ranges as $r): ?>
            <a href="<?= esc(site_url('admin/traffic') . '?range=' . $r) ?>"
               class="chip !h-7 !px-3 text-[11px] <?= (int) $range === $r ? 'border-ink/40 font-semibold text-ink' : '' ?>">
                <?= $r ?> hari
            </a>
        <?php endforeach; ?>
        <a href="<?= esc(site_url('admin/traffic/search-console')) ?>" class="chip !h-7 !px-3 text-[11px]"><?= lineicon('search', 'w-3.5 h-3.5') ?> Google Search Console</a>
        <span class="meta ml-auto">Sejak <?= esc(date('d M Y', strtotime($since))) ?></span>
    </div>

    <!-- Ringkasan -->
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="card p-4">
            <p class="mono-label text-[10px]">KUNJUNGAN</p>
            <p class="stat-num mt-1 text-2xl"><?= number_format((int) ($pageViewsTotal ?? 0)) ?></p>
            <p class="meta mt-1">halaman dilayani</p>
        </div>
        <div class="card p-4">
            <p class="mono-label text-[10px]">PENGUNJUNG UNIK</p>
            <p class="stat-num mt-1 text-2xl"><?= number_format((int) ($pageViewsVisitors ?? 0)) ?></p>
            <p class="meta mt-1">berdasar sid</p>
        </div>
        <div class="card p-4">
            <p class="mono-label text-[10px]">KLIK CTA</p>
            <p class="stat-num mt-1 text-2xl"><?= number_format((int) $ctaTotal) ?></p>
            <p class="meta mt-1">tombolwa/marketplace</p>
        </div>
        <div class="card p-4">
            <p class="mono-label text-[10px]">CTR</p>
            <p class="stat-num mt-1 text-2xl"><?= $pageViewsTotal > 0 ? number_format(($ctaTotal / $pageViewsTotal) * 100, 2, ',', '.') : '0,00' ?>%</p>
            <p class="meta mt-1">klik ÷ kunjungan</p>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        <!-- Klik per channel -->
        <div class="card p-5">
            <p class="mono-label text-[10px]">KLIK CTA PER KANAL</p>
            <?php if (empty($ctaByChannel)): ?>
                <div class="mt-4"><?= view('partials/ui/empty', ['icon' => 'mouse-pointer-click', 'title' => 'Belum ada klik', 'desc' => 'Data muncul setelah pengunjung menekan tombol CTA.']) ?></div>
            <?php else: ?>
                <ul class="mt-4 space-y-3">
                    <?php foreach ($ctaByChannel as $row): ?>
                        <?php $ch = (string) $row['channel']; $pct = $maxChannel > 0 ? ((int) $row['total'] / $maxChannel) * 100 : 0; ?>
                        <li>
                            <div class="flex items-center justify-between text-[12.5px]">
                                <span class="flex items-center gap-2 text-ink">
                                    <?= lineicon($channelIcon[$ch] ?? 'link', 'w-3.5 h-3.5 text-faint') ?>
                                    <?= esc($channelLabel[$ch] ?? ucfirst($ch)) ?>
                                </span>
                                <span class="font-mono tabular-nums text-muted"><?= (int) $row['total'] ?></span>
                            </div>
                            <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-[var(--color-surface-alt)]">
                                <div class="h-full rounded-full bg-ink" style="width: <?= round($pct, 1) ?>%"></div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <!-- Sumber teratas -->
        <div class="card p-5">
            <p class="mono-label text-[10px]">SUMBER KLIK TERATAS</p>
            <?php if (empty($ctaTopSources)): ?>
                <div class="mt-4"><?= view('partials/ui/empty', ['icon' => 'route', 'title' => 'Belum ada sumber', 'desc' => 'Sumber diambil dari UTM, halaman asal, atau referer.']) ?></div>
            <?php else: ?>
                <ul class="mt-4 divide-y divide-line">
                    <?php foreach ($ctaTopSources as $row): ?>
                        <li class="flex items-center justify-between gap-3 py-2 text-[12.5px]">
                            <span class="truncate text-muted"><?= esc($row['utm_source']) ?></span>
                            <span class="font-mono tabular-nums text-ink"><?= (int) $row['total'] ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <!-- Halaman teratas -->
        <div class="card p-5">
            <p class="mono-label text-[10px]">HALAMAN PALING BANYAK DIKUNJUNGI</p>
            <?php if (empty($trafficTopPages)): ?>
                <div class="mt-4"><?= view('partials/ui/empty', ['icon' => 'file-text', 'title' => 'Belum ada data', 'desc' => 'Kunjungan akan tercatat setelah tracker aktif.']) ?></div>
            <?php else: ?>
                <ul class="mt-4 divide-y divide-line">
                    <?php foreach ($trafficTopPages as $row): ?>
                        <li class="flex items-center justify-between gap-3 py-2 text-[12.5px]">
                            <a href="<?= esc(site_url($row['path'])) ?>" target="_blank" rel="noopener" class="truncate text-muted hover:text-ink"><?= esc($row['path']) ?></a>
                            <span class="font-mono tabular-nums text-ink"><?= (int) $row['total'] ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <!-- Sumber traffic -->
        <div class="card p-5">
            <p class="mono-label text-[10px]">SUMBER TRAFFIC</p>
            <?php if (empty($trafficSources)): ?>
                <div class="mt-4"><?= view('partials/ui/empty', ['icon' => 'globe', 'title' => 'Belum ada data', 'desc' => 'Referer & parameter UTM dicatat per kunjungan.']) ?></div>
            <?php else: ?>
                <ul class="mt-4 divide-y divide-line">
                    <?php foreach ($trafficSources as $row): ?>
                        <li class="flex items-center justify-between gap-3 py-2 text-[12.5px]">
                            <span class="truncate text-muted"><?= esc($row['source']) ?></span>
                            <span class="font-mono tabular-nums text-ink"><?= (int) $row['total'] ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <!-- Produk teratas -->
    <?php if (!empty($ctaTopProducts)): ?>
        <div class="card p-5">
            <p class="mono-label text-[10px]">PRODUK PALING SERING DI-KLIK</p>
            <ul class="mt-4 divide-y divide-line">
                <?php foreach ($ctaTopProducts as $row): ?>
                    <li class="flex items-center justify-between gap-3 py-2 text-[12.5px]">
                        <a href="<?= esc(site_url('produk/' . $row['product_slug'])) ?>" target="_blank" rel="noopener" class="truncate text-muted hover:text-ink"><?= esc($row['product_slug']) ?></a>
                        <span class="font-mono tabular-nums text-ink"><?= (int) $row['total'] ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Klik terbaru -->
    <div class="card overflow-hidden">
        <div class="border-b border-line p-5">
            <p class="mono-label text-[10px]">KLIK TERBARU</p>
        </div>
        <?php if (empty($ctaRecent)): ?>
            <div class="p-5"><?= view('partials/ui/empty', ['icon' => 'mouse-pointer-click', 'title' => 'Belum ada klik', 'desc' => 'Riwayat klik CTA akan tampil di sini.']) ?></div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-[var(--color-surface-alt)] text-muted font-semibold border-b border-line">
                            <th class="p-4 mono-label text-[10px]">WAKTU</th>
                            <th class="p-4 mono-label text-[10px]">KANAL</th>
                            <th class="p-4 mono-label text-[10px]">PRODUK</th>
                            <th class="p-4 mono-label text-[10px]">SUMBER</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        <?php foreach ($ctaRecent as $row): ?>
                            <tr class="hover:bg-[var(--color-surface-alt)]/50 transition-colors">
                                <td class="p-4 font-mono text-[11px] text-faint whitespace-nowrap"><?= esc(date('d M Y · H:i', strtotime((string) $row['created_at']))) ?></td>
                                <td class="p-4">
                                    <span class="inline-flex items-center gap-1.5 text-ink">
                                        <?= lineicon($channelIcon[$row['channel']] ?? 'link', 'w-3.5 h-3.5 text-faint') ?>
                                        <?= esc($channelLabel[$row['channel']] ?? ucfirst((string) $row['channel'])) ?>
                                    </span>
                                </td>
                                <td class="p-4">
                                    <a href="<?= esc(site_url('produk/' . $row['product_slug'])) ?>" target="_blank" rel="noopener" class="link-quiet max-w-[200px] truncate block"><?= esc($row['product_slug']) ?></a>
                                </td>
                                <td class="p-4 text-muted max-w-[200px] truncate"><?= esc($row['utm_source'] ?: '—') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
