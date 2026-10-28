<?php
$brand = $settings['brand'] ?? [];
$contact = $settings['contact'] ?? [];
$waNumber = preg_replace('/[^0-9]/', '', $contact['whatsapp'] ?? '6281234567890');
$navItems = [
    ['label' => 'Katalog', 'url' => '/produk'],
    ['label' => 'Layanan', 'url' => '/layanan'],
    ['label' => 'Portofolio', 'url' => '/portofolio'],
    ['label' => 'Blog', 'url' => '/blog'],
    ['label' => 'Tentang', 'url' => '/tentang-kami'],
    ['label' => 'Kontak', 'url' => '/kontak'],
];
$uri = current_url(true)->getPath() ?? '/';
?>

<header x-data="{ open:false }"
        class="sticky top-0 z-40 border-b border-line bg-[var(--color-bg)]/90 backdrop-blur-md">

    <!-- Main bar -->
    <div class="shell flex h-[68px] items-center justify-between gap-4">
        <a href="/" class="flex items-center gap-3">
            <span class="grid h-9 w-9 place-items-center rounded-full bg-ink font-heading text-[12px] font-bold text-[var(--color-bg)]">PJ</span>
            <span class="leading-none">
                <span class="block font-heading text-[15px] font-semibold tracking-tight text-ink"><?= esc($brand['name'] ?? 'Pusat Jersey') ?></span>
                <span class="mt-1 block text-[10px] uppercase tracking-[0.2em] text-faint">Custom Apparel · ID</span>
            </span>
        </a>
        <nav class="hidden items-center gap-7 lg:flex" aria-label="Navigasi utama">
            <?php foreach ($navItems as $item):
              $active = rtrim($uri,'/') === rtrim($item['url'],'/') || ($item['url'] !== '/' && str_starts_with($uri, $item['url']));
            ?>
              <a href="<?= esc($item['url']) ?>" <?= $active ? 'aria-current="page"' : '' ?> class="nav-link text-[13.5px] <?= $active ? 'font-semibold text-ink' : 'text-muted hover:text-ink' ?>"><?= esc($item['label']) ?></a>
            <?php endforeach; ?>
        </nav>
        <div class="flex items-center gap-2">
            <button @click="$dispatch('open-search-overlay')" aria-label="Cari" class="icon-btn"><?= lucide_icon('search', 'w-4 h-4') ?></button>
            <?= view('partials/theme_switcher') ?>
            <a href="https://wa.me/<?= esc($waNumber) ?>" target="_blank" hx-boost="false" class="btn-wa btn-sm hidden sm:inline-flex"><?= lucide_icon('message-circle', 'w-4 h-4') ?><span>Konsultasi</span></a>
            <button @click="open = !open" :aria-expanded="open" aria-label="Menu" class="icon-btn lg:hidden">
                <template x-if="!open"><?= lucide_icon('menu', 'w-4 h-4') ?></template>
                <template x-if="open"><?= lucide_icon('x', 'w-4 h-4') ?></template>
            </button>
        </div>
    </div>
    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="border-t border-line lg:hidden">
        <nav class="shell flex flex-col py-2" aria-label="Navigasi seluler">
            <a href="/" @click="open=false" class="flex items-center justify-between py-3 text-[14px] text-ink">Beranda <?= lucide_icon('arrow-right','w-4 h-4 text-faint') ?></a>
            <?php foreach ($navItems as $item): ?>
              <a href="<?= esc($item['url']) ?>" @click="open=false" class="flex items-center justify-between border-t border-line py-3 text-[14px] text-ink"><?= esc($item['label']) ?> <?= lucide_icon('arrow-right','w-4 h-4 text-faint') ?></a>
            <?php endforeach; ?>
            <a href="https://wa.me/<?= esc($waNumber) ?>" target="_blank" hx-boost="false" class="btn-wa my-3 w-full">Konsultasi WhatsApp</a>
        </nav>
    </div>
</header>
