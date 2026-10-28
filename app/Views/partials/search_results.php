<?php if ($isEmpty): ?>
  <p class="meta px-2 py-6 text-center">Tidak ada hasil untuk “<span class="text-ink"><?= esc($q) ?></span>”.</p>
<?php else: ?>
  <div class="divide-y divide-line">
    <?php foreach (['products' => 'Produk', 'services' => 'Layanan', 'posts' => 'Artikel', 'portfolios' => 'Portofolio', 'locations' => 'Kota'] as $key => $lbl): ?>
      <?php if (!empty($$key)): ?>
        <div class="py-2">
          <p class="mono-label px-2 py-1.5"><?= $lbl ?></p>
          <?php foreach ($$key as $it): ?>
            <?php
              $url = $key === 'products' ? '/produk/' . $it['slug'] : ($key === 'services' ? '/layanan/' . $it['slug'] : ($key === 'posts' ? '/blog/' . $it['slug'] : ($key === 'portfolios' ? '/portofolio/' . $it['slug'] : '/layanan/pusat-jersey-custom-' . $it['slug'])));
              $ttl = $it['name'] ?? $it['title'] ?? '';
            ?>
            <a href="<?= esc($url) ?>" class="flex items-center justify-between rounded-[var(--radius-sm)] px-2 py-2.5 hover:bg-surface-alt"><span class="text-[13.5px] text-ink"><?= esc($ttl) ?></span><span class="meta">Buka →</span></a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
<?php endif; ?>