<?php $processed = generate_auto_toc($post['content']); $contentHtml = $processed['html']; ?>
<div class="shell py-8">
  <?= view('partials/ui/crumbs', ['trail' => [['label' => 'Beranda', 'url' => '/'], ['label' => 'Blog', 'url' => '/blog'], ['label' => $post['title']]]]) ?>
  <div class="mt-6 grid gap-10 lg:grid-cols-12">
    <article class="lg:col-span-8">
      <p class="meta text-[11px] uppercase tracking-[0.14em]"><span class="font-semibold text-ink"><?= esc($post['category_name'] ?? 'Edukasi') ?></span> · <?= date('d M Y', strtotime($post['created_at'])) ?> · <?= number_format((int)($post['views_count'] ?? 0), 0, ',', '.') ?> dibaca</p>
      <h1 class="font-heading mt-3 text-[28px] font-semibold leading-[1.2] tracking-tight text-ink sm:text-[34px]"><?= esc($post['title']) ?></h1>
      <?php if (!empty($post['featured_image'])): ?>
        <figure class="mt-6 overflow-hidden rounded-[var(--radius-lg)] border border-line bg-surface-alt"><img src="<?= esc($post['featured_image']) ?>" alt="<?= esc($post['title']) ?>" class="aspect-[16/9] w-full object-cover"></figure>
      <?php endif; ?>
      <div class="prose-pj mt-7 max-w-[68ch]"><?= $contentHtml ?></div>
      <?php if (!empty($tags)): ?>
        <div class="mt-8 flex flex-wrap gap-1.5"><?php foreach ($tags as $tag): ?><a href="/blog?tag=<?= esc($tag['slug']) ?>" class="tag">#<?= esc($tag['name']) ?></a><?php endforeach; ?></div>
      <?php endif; ?>
      <div class="mt-6 border-t border-line pt-4"><?= view('partials/social_share', ['shareUrl' => current_url(), 'shareTitle' => $post['title']]) ?></div>
      <?php if (!empty($faqs)): ?>
        <section class="mt-8">
          <h2 class="font-heading text-[19px] font-semibold text-ink">Pertanyaan umum</h2>
          <div class="mt-4"><?= view('partials/ui/faq', ['id' => 'blog-faq', 'items' => $faqs]) ?></div>
        </section>
      <?php endif; ?>
      <div class="mt-4"><?= view('partials/blog_comments', ['postId' => $post['id'], 'postSlug' => $post['slug'], 'comments' => $comments ?? []]) ?></div>
    </article>
    <aside class="lg:col-span-4">
      <div class="space-y-5 lg:sticky lg:top-24">
        <?= view('partials/auto_toc', ['contentHtml' => $post['content']]) ?>
        <div class="rounded-[var(--radius-lg)] border border-line p-5">
          <p class="font-heading text-[15px] font-semibold text-ink">Butuh jersey seperti di artikel?</p>
          <p class="meta mt-1">Konsultasi gratis, desain dibantu sampai cocok.</p>
          <a href="https://wa.me/<?= esc($settings['contact']['whatsapp'] ?? '6281234567890') ?>" target="_blank" hx-boost="false" class="btn-wa btn-sm mt-4 w-full">Chat WhatsApp</a>
        </div>
      </div>
    </aside>
  </div>
</div>