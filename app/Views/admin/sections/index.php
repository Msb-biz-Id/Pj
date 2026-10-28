<div class="space-y-6">
    
    <div class="space-y-1">
        <div class="flex items-center gap-2">
            <span class="mono-label text-[10px]">01 · BUILDER</span>
            <span class="text-faint text-xs">/</span>
            <span class="text-xs text-muted">Page Builder Homepage</span>
        </div>
        <h1 class="font-heading text-2xl font-bold tracking-tight text-ink">Page Builder Komponen Beranda</h1>
        <p class="text-xs text-muted">Tarik dan geser (drag & drop) untuk mengatur urutan section, atau nonaktifkan section secara instan tanpa reload.</p>
    </div>

    <?= view('admin/sections/_table', ['sections' => $sections]) ?>

</div>
