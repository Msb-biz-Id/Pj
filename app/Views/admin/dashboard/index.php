<div class="space-y-8">
    
    <!-- Title & Top Breadcrumb -->
    <div class="space-y-1">
        <div class="flex items-center gap-2">
            <span class="mono-label text-[10px]">01 · RINGKASAN</span>
            <span class="text-faint text-xs">/</span>
            <span class="text-xs text-muted">Dashboard Metrik</span>
        </div>
        <h1 class="font-heading text-2xl font-bold tracking-tight text-ink">Ringkasan Sistem & Performa</h1>
        <p class="text-xs text-muted max-w-2xl leading-relaxed">
            Pantau statistik katalog produk, artikel panduan, komponen homepage yang aktif, serta interaksi komentar secara real-time.
        </p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        
        <div class="card p-6 flex flex-col justify-between hover:border-ink/40 transition-colors">
            <div class="flex items-center justify-between">
                <span class="mono-label">PRODUK</span>
                <span class="p-2 rounded-full bg-[var(--color-surface-alt)] text-ink border border-line">
                    <?= lucide_icon('shopping-bag', 'w-4 h-4') ?>
                </span>
            </div>
            <div class="mt-4">
                <div class="stat-num text-3xl"><?= $stats['products_count'] ?></div>
                <p class="meta mt-1">Item dalam katalog</p>
            </div>
            <a href="<?= site_url('admin/products') ?>" class="mt-4 pt-3 border-t border-line text-xs font-semibold text-ink flex items-center justify-between group">
                <span>Kelola Katalog</span>
                <span class="transition-transform group-hover:translate-x-0.5"><?= lucide_icon('arrow-right', 'w-3.5 h-3.5') ?></span>
            </a>
        </div>

        <div class="card p-6 flex flex-col justify-between hover:border-ink/40 transition-colors">
            <div class="flex items-center justify-between">
                <span class="mono-label">LAYANAN</span>
                <span class="p-2 rounded-full bg-[var(--color-surface-alt)] text-ink border border-line">
                    <?= lucide_icon('shirt', 'w-4 h-4') ?>
                </span>
            </div>
            <div class="mt-4">
                <div class="stat-num text-3xl"><?= $stats['services_count'] ?></div>
                <p class="meta mt-1">Kategori konveksi</p>
            </div>
            <a href="<?= site_url('admin/services') ?>" class="mt-4 pt-3 border-t border-line text-xs font-semibold text-ink flex items-center justify-between group">
                <span>Kelola Layanan</span>
                <span class="transition-transform group-hover:translate-x-0.5"><?= lucide_icon('arrow-right', 'w-3.5 h-3.5') ?></span>
            </a>
        </div>

        <div class="card p-6 flex flex-col justify-between hover:border-ink/40 transition-colors">
            <div class="flex items-center justify-between">
                <span class="mono-label">BLOG</span>
                <span class="p-2 rounded-full bg-[var(--color-surface-alt)] text-ink border border-line">
                    <?= lucide_icon('file-text', 'w-4 h-4') ?>
                </span>
            </div>
            <div class="mt-4">
                <div class="stat-num text-3xl"><?= $stats['posts_count'] ?></div>
                <p class="meta mt-1">Artikel terbit</p>
            </div>
            <a href="<?= site_url('admin/blog') ?>" class="mt-4 pt-3 border-t border-line text-xs font-semibold text-ink flex items-center justify-between group">
                <span>Tulis Artikel Baru</span>
                <span class="transition-transform group-hover:translate-x-0.5"><?= lucide_icon('arrow-right', 'w-3.5 h-3.5') ?></span>
            </a>
        </div>

        <div class="card p-6 flex flex-col justify-between hover:border-ink/40 transition-colors">
            <div class="flex items-center justify-between">
                <span class="mono-label">KOMPONEN</span>
                <span class="p-2 rounded-full bg-[var(--color-surface-alt)] text-ink border border-line">
                    <?= lucide_icon('layers', 'w-4 h-4') ?>
                </span>
            </div>
            <div class="mt-4">
                <div class="stat-num text-3xl"><?= $stats['sections_count'] ?></div>
                <p class="meta mt-1">Seksi aktif beranda</p>
            </div>
            <a href="<?= site_url('admin/sections') ?>" class="mt-4 pt-3 border-t border-line text-xs font-semibold text-ink flex items-center justify-between group">
                <span>Atur Urutan Seksi</span>
                <span class="transition-transform group-hover:translate-x-0.5"><?= lucide_icon('arrow-right', 'w-3.5 h-3.5') ?></span>
            </a>
        </div>

    </div>

    <!-- Quick Shortcuts Card -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Active Page Builder Sections -->
        <div class="card p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div class="space-y-0.5">
                    <span class="mono-label text-[10px]">PAGE BUILDER</span>
                    <h2 class="font-heading text-base font-semibold text-ink flex items-center gap-2">
                        <?= lucide_icon('layers', 'w-4 h-4') ?>
                        <span>Komponen Beranda Aktif</span>
                    </h2>
                </div>
                <a href="<?= site_url('admin/sections') ?>" class="btn-outline btn-sm text-xs">
                    Kelola Urutan
                </a>
            </div>

            <div class="space-y-2">
                <?php
                $sectionLabels = [
                    'hero' => 'Hero Banner', 'trust_badges' => 'Trust Badges',
                    'services' => 'Layanan Custom', 'products' => 'Katalog Produk', 'portfolio_gallery' => 'Galeri Portofolio',
                    'testimonials' => 'Testimoni Klien', 'partners' => 'Slider Mitra', 'blog_latest' => 'Artikel Terbaru', 'locations_grid' => 'Jangkauan Kota',
                    'cta_wa' => 'CTA WhatsApp'
                ];
                ?>
                <?php foreach ($sections as $sec): ?>
                    <div class="p-3 bg-[var(--color-surface-alt)] rounded-[10px] border border-line flex items-center justify-between text-xs transition-colors hover:border-ink/20">
                        <div class="flex items-center space-x-3">
                            <span class="w-6 h-6 rounded-md bg-[var(--color-surface)] text-ink font-mono font-bold flex items-center justify-center text-[10px] border border-line">
                                <?= $sec['sort_order'] ?>
                            </span>
                            <span class="font-semibold text-ink"><?= esc($sectionLabels[$sec['section_key']] ?? ucfirst($sec['section_key'])) ?></span>
                            <code class="text-[10px] text-faint font-mono hidden sm:inline"><?= esc($sec['section_key']) ?></code>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-mono font-semibold <?= $sec['is_active'] ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/30' : 'bg-[var(--color-surface)] text-faint border border-line' ?>">
                            <?= $sec['is_active'] ? 'AKTIF' : 'NONAKTIF' ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Recent Comments -->
        <div class="card p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div class="space-y-0.5">
                    <span class="mono-label text-[10px]">MODERASI</span>
                    <h2 class="font-heading text-base font-semibold text-ink flex items-center gap-2">
                        <?= lucide_icon('message-square', 'w-4 h-4') ?>
                        <span>Komentar Blog Terbaru</span>
                    </h2>
                </div>
                <a href="<?= site_url('admin/comments') ?>" class="btn-outline btn-sm text-xs">
                    Semua Komentar
                </a>
            </div>

            <div class="space-y-2.5">
                <?php if (!empty($recentComments)): ?>
                    <?php foreach ($recentComments as $cm): ?>
                        <div class="p-3.5 bg-[var(--color-surface-alt)] rounded-[10px] border border-line space-y-1.5 transition-colors hover:border-ink/20">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-semibold text-ink"><?= esc($cm['author_name']) ?></span>
                                <span class="text-[11px] text-faint font-mono"><?= date('d M Y · H:i', strtotime($cm['created_at'])) ?></span>
                            </div>
                            <p class="text-xs text-muted line-clamp-2 leading-relaxed">“<?= esc($cm['content']) ?>”</p>
                            <span class="text-[11px] text-faint block truncate">
                                pada artikel: <strong class="text-ink font-normal"><?= esc($cm['post_title']) ?></strong>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="py-8 text-center text-muted text-xs border border-dashed border-line rounded-[10px]">
                        <?= lucide_icon('message-square', 'w-6 h-6 mx-auto text-faint mb-2 opacity-50') ?>
                        Belum ada komentar baru yang masuk.
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

</div>
