<div class="space-y-6">
    <div class="space-y-1">
        <div class="flex items-center gap-2">
            <span class="mono-label text-[10px]">02 · MODERASI</span>
            <span class="text-faint text-xs">/</span>
            <span class="text-xs text-muted">Komentar Blog</span>
        </div>
        <h1 class="font-heading text-2xl font-bold tracking-tight text-ink">Moderasi Komentar Blog</h1>
        <p class="text-xs text-muted">Tinjau, setujui, atau tandai sebagai spam komentar yang dikirim oleh pengunjung.</p>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[var(--color-surface-alt)] text-muted font-semibold border-b border-line">
                        <th class="p-4 mono-label text-[10px]">PENGIRIM</th>
                        <th class="p-4 mono-label text-[10px]">ISI KOMENTAR</th>
                        <th class="p-4 mono-label text-[10px]">ARTIKEL</th>
                        <th class="p-4 mono-label text-[10px]">STATUS</th>
                        <th class="p-4 mono-label text-[10px]">WAKTU</th>
                        <th class="p-4 text-right mono-label text-[10px]">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    <?php if (!empty($comments)): ?>
                        <?php foreach ($comments as $cm): ?>
                            <tr class="hover:bg-[var(--color-surface-alt)]/50 transition-colors <?= $cm['status'] === 'pending' ? 'bg-amber-500/5' : '' ?>">
                                <td class="p-4">
                                    <span class="font-heading font-semibold text-ink text-[13px] block"><?= esc($cm['author_name']) ?></span>
                                    <span class="font-mono text-[10px] text-faint"><?= esc($cm['author_email']) ?></span>
                                </td>
                                <td class="p-4 max-w-sm">
                                    <p class="text-muted leading-relaxed line-clamp-2">“<?= esc($cm['content']) ?>”</p>
                                </td>
                                <td class="p-4">
                                    <a href="<?= base_url('blog/' . esc($cm['post_slug'])) ?>" target="_blank" hx-boost="false" class="link-quiet text-xs block truncate max-w-[160px]" title="<?= esc($cm['post_title'] ?? '') ?>">
                                        <?= esc($cm['post_title'] ?? '—') ?>
                                    </a>
                                </td>
                                <td class="p-4">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-semibold
                                        <?php if ($cm['status'] === 'approved'): ?>bg-emerald-500/10 text-emerald-500 border border-emerald-500/30
                                        <?php elseif ($cm['status'] === 'pending'): ?>bg-amber-500/10 text-amber-500 border border-amber-500/30
                                        <?php else: ?>bg-rose-500/10 text-rose-400 border border-rose-500/30<?php endif; ?>">
                                        <span class="w-1.5 h-1.5 rounded-full <?= $cm['status'] === 'approved' ? 'bg-emerald-500' : ($cm['status'] === 'pending' ? 'bg-amber-500' : 'bg-rose-400') ?>"></span>
                                        <?= strtoupper(esc($cm['status'])) ?>
                                    </span>
                                </td>
                                <td class="p-4 text-faint font-mono text-[11px] whitespace-nowrap">
                                    <?= date('d M Y · H:i', strtotime($cm['created_at'])) ?>
                                </td>
                                <td class="p-4 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <?php if ($cm['status'] !== 'approved'): ?>
                                            <form action="<?= site_url('admin/comments/approve/' . $cm['id']) ?>" method="POST" class="inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="icon-btn !h-8 !w-8 hover:border-emerald-500/50 hover:text-emerald-500" title="Setujui Tayang">
                                                    <?= lucide_icon('check', 'w-3.5 h-3.5') ?>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if ($cm['status'] !== 'spam'): ?>
                                            <form action="<?= site_url('admin/comments/spam/' . $cm['id']) ?>" method="POST" class="inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="icon-btn !h-8 !w-8 hover:border-amber-500/50 hover:text-amber-500" title="Tandai Spam">
                                                    <?= lucide_icon('eye-off', 'w-3.5 h-3.5') ?>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <form action="<?= site_url('admin/comments/delete/' . $cm['id']) ?>" method="POST" class="inline" onsubmit="return confirm('Hapus komentar ini?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="icon-btn !h-8 !w-8 hover:border-rose-500/50 hover:text-rose-400" title="Hapus Permanen">
                                                <?= lucide_icon('trash-2', 'w-3.5 h-3.5') ?>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="p-12 text-center text-muted">
                                <?= lucide_icon('message-square', 'w-8 h-8 mx-auto text-faint mb-3 opacity-50') ?>
                                <p class="text-sm">Belum ada komentar yang memerlukan moderasi.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
