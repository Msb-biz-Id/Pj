<?php $postId = $postId ?? 0; $comments = $comments ?? []; $postSlug = $postSlug ?? ''; ?>
<div id="comments-section" x-data="{ replyTo: 0, replyName: '' }" class="mt-10 border-t border-line pt-8">
  <div class="flex items-center justify-between">
    <h3 class="font-heading text-[17px] font-semibold text-ink">Diskusi Komunitas (<?= count($comments) ?>)</h3>
    <span class="mono-label text-[10px]">MODERASI AKTIF</span>
  </div>

  <form hx-post="/blog/<?= esc($postSlug) ?>/comment"
        hx-target="#comment-alert"
        hx-swap="innerHTML"
        hx-boost="false"
        hx-select="unset"
        hx-on::after-request="if(event.detail.successful && event.detail.xhr.responseText.includes('bg-emerald')) this.reset()"
        class="mt-4 rounded-[var(--radius-md)] border border-line bg-surface p-5 space-y-3">
    <?= csrf_field() ?>
    <input type="hidden" name="post_id" value="<?= $postId ?>">
    <input type="hidden" name="parent_id" x-model="replyTo">

    <!-- Alert Container -->
    <div id="comment-alert"></div>

    <p class="text-[13.5px] font-semibold text-ink">
      <span x-show="!replyTo">Tulis komentar Anda</span>
      <span x-show="replyTo">Membalas ke <strong class="text-ink" x-text="replyName"></strong> <button type="button" @click="replyTo = 0; replyName = ''" class="link-quiet !text-[12px] ml-2 text-rose-500 hover:underline">(batal balas)</button></span>
    </p>

    <div class="grid gap-3 sm:grid-cols-2">
      <div>
        <label class="lbl text-[11px] sr-only">Nama Anda</label>
        <input type="text" name="author_name" required class="field text-xs" placeholder="Nama Anda *">
      </div>
      <div>
        <label class="lbl text-[11px] sr-only">Email Anda</label>
        <input type="email" name="author_email" required class="field text-xs" placeholder="Alamat Email (tidak dipublikasikan) *">
      </div>
    </div>

    <div>
      <label class="lbl text-[11px] sr-only">Komentar</label>
      <textarea name="content" rows="3" required class="field text-xs" placeholder="Tuliskan pertanyaan, tanggapan, atau pengalaman Anda seputar artikel ini..."></textarea>
    </div>

    <div class="flex items-center justify-between pt-1">
      <span class="meta text-[11px]">Komentar akan ditinjau admin sebelum tampil publik.</span>
      <button type="submit" class="btn-primary btn-sm px-5 text-xs gap-1.5">
        <span>Kirim Komentar</span>
        <span class="htmx-indicator">⟳</span>
      </button>
    </div>
  </form>

  <div class="mt-6 space-y-3">
    <?php if (!empty($comments)): ?>
      <?php foreach ($comments as $cm): ?>
        <?php if (empty($cm['parent_id'])): ?>
          <div class="rounded-[var(--radius-md)] border border-line bg-surface-alt/60 p-4 space-y-2">
            <div class="flex items-center justify-between text-xs">
              <span class="font-heading font-semibold text-ink"><?= esc($cm['author_name']) ?></span>
              <span class="meta font-mono text-[10px]"><?= date('d M Y · H:i', strtotime($cm['created_at'])) ?></span>
            </div>
            <p class="text-[13px] leading-relaxed text-muted"><?= nl2br(esc($cm['content'])) ?></p>
            <div class="pt-1">
              <button type="button" @click="replyTo = <?= (int)$cm['id'] ?>; replyName = '<?= esc($cm['author_name'], 'js') ?>'; $el.closest('#comments-section').querySelector('textarea').focus()" class="link-quiet !text-[11px] font-semibold text-faint hover:text-ink">
                Balas tanggapan
              </button>
            </div>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="rounded-[var(--radius-md)] border border-dashed border-line px-4 py-8 text-center text-muted">
        <p class="text-xs">Belum ada diskusi untuk artikel ini. Jadilah yang pertama berkomentar!</p>
      </div>
    <?php endif; ?>
  </div>
</div>