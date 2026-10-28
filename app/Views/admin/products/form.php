<?php $isEdit = !empty($product);

// Tampilkan data JSON sebagai format teks ramah-admin
$galleryLines = '';
if (!empty($product['gallery_json'])) {
    $decodedGallery = json_decode($product['gallery_json'], true);
    if (is_array($decodedGallery)) $galleryLines = implode("\n", $decodedGallery);
}
$specLines = '';
if (!empty($product['specs_json'])) {
    $decodedSpecs = json_decode($product['specs_json'], true);
    if (is_array($decodedSpecs)) {
        foreach ($decodedSpecs as $sk => $sv) $specLines .= $sk . ' = ' . $sv . "\n";
    }
}
?>

<div class="space-y-6 max-w-4xl">

    <div class="flex items-center justify-between">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="mono-label text-[10px]">02 · KATALOG</span>
                <span class="text-faint text-xs">/</span>
                <a href="<?= site_url('admin/products') ?>" class="text-xs text-muted hover:text-ink">Produk</a>
                <span class="text-faint text-xs">/</span>
                <span class="text-xs text-ink font-medium"><?= $isEdit ? 'Edit Produk' : 'Tambah Baru' ?></span>
            </div>
            <h1 class="font-heading text-2xl font-bold tracking-tight text-ink">
                <?= $isEdit ? 'Edit Produk: ' . esc($product['name']) : 'Tambah Produk Baru' ?>
            </h1>
        </div>
        <a href="<?= site_url('admin/products') ?>" class="btn-outline btn-sm gap-1.5 text-xs">
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

    <form action="<?= $isEdit ? site_url('admin/products/update/' . $product['id']) : site_url('admin/products/store') ?>" method="POST" class="space-y-6">
        <?= csrf_field() ?>

        <!-- Basic Info -->
        <div class="card p-6 sm:p-8 space-y-5">
            <div class="flex items-center justify-between border-b border-line pb-4">
                <span class="mono-label">INFORMASI DASAR</span>
                <span class="text-[11px] text-faint font-mono">STEP 01</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                <div class="md:col-span-2">
                    <label class="lbl flex justify-between" for="prod-name">
                        <span>Nama Produk *</span>
                        <span class="text-faint font-normal text-[11px]">Wajib diisi</span>
                    </label>
                    <input type="text" id="prod-name" name="name" required value="<?= esc($product['name'] ?? '') ?>"
                           class="field text-xs"
                           placeholder="Contoh: Jersey Custom Full Sublim Tim Futsal Pro">
                </div>

                <div>
                    <label class="lbl flex justify-between" for="prod-slug">
                        <span>Slug URL</span>
                        <span class="text-faint font-normal text-[11px]">Auto / Kustom</span>
                    </label>
                    <input type="text" id="prod-slug" name="slug" value="<?= esc($product['slug'] ?? '') ?>"
                           class="field text-xs font-mono"
                           placeholder="jersey-custom-full-sublim">
                </div>

                <div>
                    <label class="lbl" for="prod-sku">SKU / Kode Produk</label>
                    <input type="text" id="prod-sku" name="sku" value="<?= esc($product['sku'] ?? '') ?>"
                           class="field text-xs font-mono"
                           placeholder="PJ-FSL-001">
                </div>

                <div>
                    <label class="lbl" for="prod-cat">Kategori</label>
                    <select id="prod-cat" name="category_id" class="field text-xs">
                        <option value="">— Pilih Kategori —</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($product['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                <?= esc($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="lbl" for="prod-status">Status Publikasi</label>
                    <select id="prod-status" name="status" class="field text-xs">
                        <option value="published" <?= ($product['status'] ?? 'published') === 'published' ? 'selected' : '' ?>>Tayang (Published)</option>
                        <option value="draft" <?= ($product['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft (Konsep)</option>
                        <option value="archived" <?= ($product['status'] ?? '') === 'archived' ? 'selected' : '' ?>>Arsip</option>
                    </select>
                </div>

                <div>
                    <label class="lbl" for="prod-price">Harga Normal (Rp) *</label>
                    <input type="number" id="prod-price" name="price" required min="0" step="1000" value="<?= esc($product['price'] ?? '0') ?>"
                           class="field text-xs tabular-nums">
                </div>

                <div>
                    <label class="lbl" for="prod-disc">Harga Coret / Diskon (Rp)</label>
                    <input type="number" id="prod-disc" name="discount_price" min="0" step="1000" value="<?= esc($product['discount_price'] ?? '') ?>"
                           class="field text-xs tabular-nums"
                           placeholder="Kosongkan bila tidak ada diskon">
                </div>

                <div class="md:col-span-2">
                    <label class="lbl" for="prod-img">URL Gambar Utama</label>
                    <input type="text" id="prod-img" name="featured_image" value="<?= esc($product['featured_image'] ?? '') ?>"
                           class="field text-xs"
                           placeholder="https://... atau /uploads/produk.jpg">
                </div>
            </div>

            <!-- Checkboxes -->
            <div class="flex flex-wrap gap-5 pt-3 border-t border-line">
                <label class="flex items-center space-x-2.5 cursor-pointer text-xs font-medium text-ink select-none">
                    <input type="checkbox" name="is_custom" value="1" <?= ($product['is_custom'] ?? 0) ? 'checked' : '' ?>
                           class="w-4 h-4 rounded accent-ink border-line">
                    <span>Produk Custom Sublimasi (Pre-Order / Desain Bebas)</span>
                </label>
                <label class="flex items-center space-x-2.5 cursor-pointer text-xs font-medium text-ink select-none">
                    <input type="checkbox" name="is_affiliate" value="1" <?= ($product['is_affiliate'] ?? 0) ? 'checked' : '' ?>
                           class="w-4 h-4 rounded accent-ink border-line">
                    <span>Produk Ready Stock / Authentic Marketplace</span>
                </label>
            </div>
        </div>

        <!-- Multi-Channel Links -->
        <div class="card p-6 sm:p-8 space-y-5">
            <div class="flex items-center justify-between border-b border-line pb-4">
                <span class="mono-label">LINK MARKETPLACE & AFFILIATE</span>
                <span class="text-[11px] text-faint font-mono">STEP 02</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                <div>
                    <label class="lbl flex items-center gap-1.5" for="link-shopee">
                        <?= lucide_icon('shopping-bag', 'w-3.5 h-3.5 text-orange-500') ?>
                        <span>Shopee URL</span>
                    </label>
                    <input type="text" id="link-shopee" name="shopee_link" value="<?= esc($product['shopee_link'] ?? '') ?>"
                           class="field text-xs"
                           placeholder="https://shopee.co.id/...">
                </div>
                <div>
                    <label class="lbl flex items-center gap-1.5" for="link-tokped">
                        <?= lucide_icon('shopping-bag', 'w-3.5 h-3.5 text-emerald-500') ?>
                        <span>Tokopedia URL</span>
                    </label>
                    <input type="text" id="link-tokped" name="tokopedia_link" value="<?= esc($product['tokopedia_link'] ?? '') ?>"
                           class="field text-xs"
                           placeholder="https://tokopedia.com/...">
                </div>
                <div>
                    <label class="lbl flex items-center gap-1.5" for="link-tiktok">
                        <?= lucide_icon('video', 'w-3.5 h-3.5 text-rose-500') ?>
                        <span>TikTok Shop URL</span>
                    </label>
                    <input type="text" id="link-tiktok" name="tiktok_link" value="<?= esc($product['tiktok_link'] ?? '') ?>"
                           class="field text-xs"
                           placeholder="https://shop.tiktok.com/...">
                </div>
                <div>
                    <label class="lbl flex items-center gap-1.5" for="link-aff">
                        <?= lucide_icon('link-2', 'w-3.5 h-3.5 text-sky-500') ?>
                        <span>Link Affiliate Eksternal</span>
                    </label>
                    <input type="text" id="link-aff" name="affiliate_link" value="<?= esc($product['affiliate_link'] ?? '') ?>"
                           class="field text-xs"
                           placeholder="https://...">
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="card p-6 sm:p-8 space-y-4">
            <div class="flex items-center justify-between border-b border-line pb-4">
                <span class="mono-label">DESKRIPSI LENGKAP PRODUK</span>
                <span class="text-[11px] text-faint font-mono">STEP 03</span>
            </div>
            <textarea name="description" rows="6"
                      class="field text-xs font-mono leading-relaxed"
                      placeholder="Tuliskan spesifikasi, keunggulan bahan, dan panduan ukuran..."><?= esc($product['description'] ?? '') ?></textarea>
        </div>

        <!-- Gallery & Specs -->
        <div class="card p-6 sm:p-8 space-y-5">
            <div class="flex items-center justify-between border-b border-line pb-4">
                <span class="mono-label">GALERI TAMBAHAN & SPESIFIKASI</span>
                <span class="text-[11px] text-faint font-mono">STEP 04</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="lbl">Galeri URL (1 baris per URL)</label>
                    <textarea name="gallery" rows="4"
                              class="field text-xs font-mono"
                              placeholder="https://.../img1.jpg&#10;https://.../img2.jpg"><?= esc($galleryLines) ?></textarea>
                    <p class="meta mt-1.5">Muncul di carousel thumbnail halaman detail.</p>
                </div>
                <div>
                    <label class="lbl">Spesifikasi (<code class="font-mono text-ink">Label = Nilai</code> per baris)</label>
                    <textarea name="specs" rows="4"
                              class="field text-xs font-mono"
                              placeholder="Bahan = Drifit Milano&#10;Cetak = Sublimasi Jepang&#10;Minimal Order = 12 Stel"><?= esc($specLines) ?></textarea>
                    <p class="meta mt-1.5">Ditampilkan pada tabel atribut produk.</p>
                </div>
            </div>
        </div>

        <!-- SEO Meta -->
        <div class="card p-6 sm:p-8 space-y-4">
            <div class="flex items-center justify-between border-b border-line pb-4">
                <span class="mono-label">OPTIMASI SEO (META TAGS)</span>
                <span class="text-[11px] text-faint font-mono">OPSIONAL</span>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="lbl flex justify-between">
                        <span>Meta Title</span>
                        <span class="text-faint text-[11px]">Max 70 char</span>
                    </label>
                    <input type="text" name="meta_title" maxlength="70" value="<?= esc($product['meta_title'] ?? '') ?>"
                           class="field text-xs"
                           placeholder="Otomatis dari nama produk jika dikosongkan">
                </div>
                <div>
                    <label class="lbl flex justify-between">
                        <span>Meta Description</span>
                        <span class="text-faint text-[11px]">Max 160 char</span>
                    </label>
                    <textarea name="meta_description" maxlength="160" rows="2"
                              class="field text-xs"
                              placeholder="Ringkasan produk untuk Google Search snippet..."><?= esc($product['meta_description'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="btn-primary btn-sm px-6 gap-2">
                <?= lucide_icon('save', 'w-4 h-4') ?>
                <span><?= $isEdit ? 'Simpan Perubahan' : 'Simpan Produk' ?></span>
            </button>
            <a href="<?= site_url('admin/products') ?>" class="btn-outline btn-sm px-5">
                Batal
            </a>
        </div>

    </form>
</div>
