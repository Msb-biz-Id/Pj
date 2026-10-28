<div x-data="{ show: false }"
     @open-search-overlay.window="show = true; $nextTick(() => $refs.searchInput.focus())"
     @keydown.window.ctrl.k.prevent="show = true; $nextTick(() => $refs.searchInput.focus())"
     @keydown.escape.window="show = false"
     x-show="show" x-cloak class="fixed inset-0 z-50 p-4 sm:p-10" role="dialog" aria-modal="true">
  <div @click="show = false" class="fixed inset-0 bg-black/40"></div>
  <div class="relative mx-auto max-w-xl overflow-hidden rounded-[var(--radius-lg)] border border-line bg-surface">
    <div class="flex items-center gap-3 border-b border-line px-5">
      <?= lucide_icon('search', 'w-4 h-4 text-faint shrink-0') ?>
      <input x-ref="searchInput" type="text" name="q" hx-get="/search/suggest" hx-trigger="keyup changed delay:250ms, search" hx-target="#search-results" hx-select="unset" hx-swap="innerHTML" hx-indicator="#search-loading"
        class="h-[52px] w-full bg-transparent py-4 text-[14px] text-ink placeholder:text-faint focus:outline-none" placeholder="Cari produk, layanan, artikel..." />
      <span id="search-loading" class="htmx-indicator meta">Mencari…</span>
      <button @click="show = false" class="icon-btn !h-8 !w-8" aria-label="Tutup"><?= lucide_icon('x', 'w-4 h-4') ?></button>
    </div>
    <div id="search-results" class="max-h-96 overflow-y-auto p-3">
      <p class="meta px-2 py-6 text-center">Ketik minimal 2 huruf untuk mencari.</p>
    </div>
    <div class="meta flex items-center justify-between border-t border-line px-5 py-3"><span>Ctrl + K untuk membuka</span><span>ESC menutup</span></div>
  </div>
</div>