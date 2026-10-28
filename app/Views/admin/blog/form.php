<?php $isEdit = !empty($post); ?>

<div class="space-y-6 max-w-4xl">
    <div class="flex items-center justify-between">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="mono-label text-[10px]">02 · ARTIKEL</span>
                <span class="text-faint text-xs">/</span>
                <a href="<?= site_url('admin/blog') ?>" class="text-xs text-muted hover:text-ink">Blog</a>
                <span class="text-faint text-xs">/</span>
                <span class="text-xs text-ink font-medium"><?= $isEdit ? 'Edit Tulisan' : 'Tulis Baru' ?></span>
            </div>
            <h1 class="font-heading text-2xl font-bold tracking-tight text-ink">
                <?= $isEdit ? 'Edit Artikel: ' . esc($post['title']) : 'Tulis Artikel Blog Baru' ?>
            </h1>
        </div>
        <a href="<?= site_url('admin/blog') ?>" class="btn-outline btn-sm gap-1.5 text-xs">
            <?= lucide_icon('arrow-left', 'w-3.5 h-3.5') ?>
            <span>Kembali</span>
        </a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="p-4 bg-rose-500/10 border border-rose-500/30 text-rose-400 rounded-[12px] text-xs flex items-center gap-2.5">
            <?= lucide_icon('alert-circle', 'w-4 h-4 shrink-0') ?>
            <span><?= esc(session()->getFlashdata('error')) ?></span>
        </div>
    <?php endif; ?>

    <form action="<?= $isEdit ? site_url('admin/blog/update/' . $post['id']) : site_url('admin/blog/store') ?>" method="POST" class="space-y-6">
        <?= csrf_field() ?>

        <!-- Main Info -->
        <div class="card p-6 sm:p-8 space-y-5">
            <div class="flex items-center justify-between border-b border-line pb-4">
                <span class="mono-label">INFORMASI ARTIKEL</span>
                <span class="text-[11px] text-faint font-mono">STEP 01</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                <div class="md:col-span-2">
                    <label class="lbl flex justify-between" for="post-title">
                        <span>Judul Artikel *</span>
                        <span class="text-faint font-normal text-[11px]">Wajib diisi</span>
                    </label>
                    <input type="text" id="post-title" name="title" required value="<?= esc($post['title'] ?? '') ?>"
                           class="field text-xs"
                           placeholder="Contoh: Cara Memilih Bahan Jersey Futsal Terbaik untuk Tim">
                </div>

                <div>
                    <label class="lbl flex justify-between" for="post-slug">
                        <span>Slug URL</span>
                        <span class="text-faint font-normal text-[11px]">Auto / Kustom</span>
                    </label>
                    <input type="text" id="post-slug" name="slug" value="<?= esc($post['slug'] ?? '') ?>"
                           class="field text-xs font-mono"
                           placeholder="cara-memilih-bahan-jersey-futsal">
                </div>

                <div>
                    <label class="lbl" for="post-cat">Kategori</label>
                    <select id="post-cat" name="category_id" class="field text-xs">
                        <option value="">— Pilih Kategori —</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($post['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                <?= esc($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="lbl" for="post-status">Status Publikasi</label>
                    <select id="post-status" name="status" class="field text-xs">
                        <option value="draft" <?= ($post['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft (Konsep)</option>
                        <option value="published" <?= ($post['status'] ?? '') === 'published' ? 'selected' : '' ?>>Tayang (Published)</option>
                        <option value="archived" <?= ($post['status'] ?? '') === 'archived' ? 'selected' : '' ?>>Arsip</option>
                    </select>
                </div>

                <div>
                    <label class="lbl" for="post-img">URL Gambar Featured</label>
                    <input type="text" id="post-img" name="featured_image" value="<?= esc($post['featured_image'] ?? '') ?>"
                           class="field text-xs"
                           placeholder="https://... atau /uploads/artikel.jpg">
                </div>

                <div class="md:col-span-2">
                    <label class="lbl" for="post-tags">Tag (pisahkan dengan koma)</label>
                    <input type="text" id="post-tags" name="tags" value="<?= esc($tagList ?? '') ?>"
                           class="field text-xs font-mono"
                           placeholder="jersey futsal, bahan drifit, sublimasi, desain">
                    <p class="meta mt-1.5">Tag muncul di bawah artikel dan dapat difilter di arsip blog.</p>
                </div>

                <div class="md:col-span-2">
                    <label class="lbl" for="post-excerpt">Excerpt / Ringkasan Singkat</label>
                    <textarea id="post-excerpt" name="excerpt" rows="2"
                              class="field text-xs leading-relaxed"
                              placeholder="Ringkasan singkat artikel yang muncul di listing kartu dan cuplikan media sosial..."><?= esc($post['excerpt'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="card p-6 sm:p-8 space-y-4">
            <div class="flex items-center justify-between border-b border-line pb-4">
                <span class="mono-label">KONTEN LENGKAP ARTIKEL</span>
                <span class="text-[11px] text-faint font-mono">STEP 02</span>
            </div>
            <p class="meta">Gunakan heading <code class="font-mono text-ink">&lt;h2&gt;</code> dan <code class="font-mono text-ink">&lt;h3&gt;</code> untuk otomatis menghasilkan Daftar Isi (Auto TOC) di halaman artikel.</p>
            <textarea name="content" rows="18"
                      class="field text-xs font-mono leading-relaxed"
                      placeholder="<h2>Judul Bagian Utama</h2>&#10;<p>Tuliskan ulasan atau panduan di sini...</p>"><?= esc($post['content'] ?? '') ?></textarea>
        </div>

        <!-- SEO -->
        <div class="card p-6 sm:p-8 space-y-4">
            <div class="flex items-center justify-between border-b border-line pb-4">
                <span class="mono-label">OPTIMASI SEO META</span>
                <span class="text-[11px] text-faint font-mono">OPSIONAL</span>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="lbl flex justify-between">
                        <span>Meta Title</span>
                        <span class="text-faint text-[11px]">Max 70 char</span>
                    </label>
                    <input type="text" name="meta_title" maxlength="70" value="<?= esc($post['meta_title'] ?? '') ?>"
                           class="field text-xs"
                           placeholder="Otomatis dari judul artikel jika dikosongkan">
                </div>
                <div>
                    <label class="lbl flex justify-between">
                        <span>Meta Description</span>
                        <span class="text-faint text-[11px]">Max 160 char</span>
                    </label>
                    <textarea name="meta_description" maxlength="160" rows="2"
                              class="field text-xs"
                              placeholder="Deskripsi ringkas untuk snippet mesin pencari Google..."><?= esc($post['meta_description'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="btn-primary btn-sm px-6 gap-2">
                <?= lucide_icon('save', 'w-4 h-4') ?>
                <span><?= $isEdit ? 'Simpan Perubahan' : 'Simpan Artikel' ?></span>
            </button>
            <a href="<?= site_url('admin/blog') ?>" class="btn-outline btn-sm px-5">
                Batal
            </a>
        </div>
    </form>
</div>
