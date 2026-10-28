<?php $contact = $settings['contact'] ?? []; ?>
<?= view('partials/ui/page_head', [
  'eyebrow' => 'Kontak',
  'title' => 'Hubungi workshop secara langsung',
  'lede' => 'Isi form singkat — pesan Anda langsung terhubung ke WhatsApp CS.',
]) ?>
<div class="shell grid gap-8 py-10 lg:grid-cols-12">
  <div class="lg:col-span-7">
    <div class="rounded-[var(--radius-lg)] border border-line p-6 sm:p-8">
      <div id="contact-response-container">
        <form hx-post="/kontak/send" hx-target="#contact-response-container" hx-swap="outerHTML" hx-boost="false" hx-select="unset" class="space-y-4">
          <?= csrf_field() ?>
          <div class="grid gap-4 sm:grid-cols-2">
            <div><label class="lbl" for="c-nama">Nama</label><input id="c-nama" type="text" name="name" required class="field" placeholder="Nama Anda"></div>
            <div><label class="lbl" for="c-wa">WhatsApp</label><input id="c-wa" type="text" name="phone" required class="field" placeholder="08xx xxxx xxxx"></div>
          </div>
          <div><label class="lbl" for="c-msg">Kebutuhan</label><textarea id="c-msg" name="message" rows="4" required class="field" placeholder="Contoh: 24 stel futsal, bahan Milano, kirim ke Bandung..."></textarea></div>
          <button type="submit" class="btn-wa w-full">Kirim & lanjut ke WhatsApp</button>
        </form>
      </div>
    </div>
  </div>
  <aside class="lg:col-span-5">
    <div class="rounded-[var(--radius-lg)] border border-line p-6 lg:sticky lg:top-24">
      <p class="mono-label">Workshop</p>
      <p class="mt-3 text-[14px] font-semibold text-ink"><?= esc($contact['address'] ?? '') ?>, <?= esc($contact['city'] ?? '') ?></p>
      <dl class="meta mt-4 space-y-2.5 border-t border-line pt-4">
        <div class="flex justify-between"><dt>WhatsApp</dt><dd class="text-ink"><?= esc($contact['phone'] ?? '') ?></dd></div>
        <div class="flex justify-between"><dt>Email</dt><dd class="text-ink"><?= esc($contact['email'] ?? '') ?></dd></div>
      </dl>
      <?php if (!empty($contact['maps_embed'])): ?>
        <div class="mt-4 overflow-hidden rounded-[var(--radius-md)] border border-line"><iframe src="<?= esc($contact['maps_embed']) ?>" width="100%" height="220" style="border:0" loading="lazy"></iframe></div>
      <?php endif; ?>
    </div>
  </aside>
</div>