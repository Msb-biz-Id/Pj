<!-- HTMX Out-of-Band Swaps for SEO -->
<title id="page-title" hx-swap-oob="outerHTML"><?= esc($metaTitle) ?></title>
<meta name="description" id="page-meta-desc" content="<?= esc($metaDesc) ?>" hx-swap-oob="outerHTML">
<link rel="canonical" id="page-canonical" href="<?= esc($canonical) ?>" hx-swap-oob="outerHTML">
<meta name="X-CSRF-TOKEN" id="page-csrf-token" content="<?= csrf_hash() ?>" hx-swap-oob="outerHTML">
<meta property="og:title" id="og-title" content="<?= esc($metaTitle) ?>" hx-swap-oob="outerHTML">
<meta property="og:description" id="og-desc" content="<?= esc($metaDesc) ?>" hx-swap-oob="outerHTML">
<meta property="og:url" id="og-url" content="<?= esc($canonical) ?>" hx-swap-oob="outerHTML">

<?php if (!empty($schemaJsonLd)): ?>
<script type="application/ld+json" id="page-jsonld" hx-swap-oob="outerHTML">
<?= json_encode($schemaJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>
</script>
<?php endif; ?>
