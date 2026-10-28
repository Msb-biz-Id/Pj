<?php $shareUrl = rawurlencode($shareUrl ?? current_url()); $shareTitle = rawurlencode($shareTitle ?? 'Pusat Jersey'); ?>
<div x-data="{ copied: false, copyLink() { navigator.clipboard.writeText(decodeURIComponent('<?= $shareUrl ?>')); this.copied = true; setTimeout(() => this.copied = false, 2000); } }" class="flex flex-wrap items-center gap-2">
  <span class="meta mr-1">Bagikan:</span>
  <a href="https://api.whatsapp.com/send?text=<?= $shareTitle ?>%20<?= $shareUrl ?>" target="_blank" hx-boost="false" class="chip !h-8 !px-3 gap-1.5"><?= brand_icon('whatsapp', 'h-3.5 w-3.5') ?><span>WhatsApp</span></a>
  <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $shareUrl ?>" target="_blank" hx-boost="false" class="chip !h-8 !px-3 gap-1.5"><?= brand_icon('facebook', 'h-3.5 w-3.5') ?><span>Facebook</span></a>
  <a href="https://twitter.com/intent/tweet?text=<?= $shareTitle ?>&url=<?= $shareUrl ?>" target="_blank" hx-boost="false" class="chip !h-8 !px-3 gap-1.5"><?= brand_icon('x', 'h-3.5 w-3.5') ?><span>X</span></a>
  <button @click="copyLink()" class="chip !h-8 !px-3"><span x-text="copied ? 'Tersalin' : 'Salin link'"></span></button>
</div>