<?php

namespace App\Controllers;

use App\Models\FaqModel;

class Page extends BaseController
{
    public function about(): string
    {
        $faqModel = new FaqModel();
        $faqs = $faqModel->where('post_type', 'global')->findAll();

        $about = $this->settings['about'];
        $brand = $this->settings['brand'];

        $seo = [
            'meta_title' => $about['title'] . ' | ' . $brand['name'],
            'meta_description' => $about['short_desc'],
            'breadcrumbs' => [
                ['name' => 'Beranda', 'url' => '/'],
                ['name' => 'Tentang Kami', 'url' => '/tentang-kami']
            ],
            'faqs' => $faqs
        ];

        // AboutPage + Organization JSON-LD (BRD E1)
        $extraSchemas = [
            [
                '@context' => 'https://schema.org',
                '@type'    => 'AboutPage',
                'name'     => $about['title'],
                'description' => $about['short_desc'],
                'url'      => current_url(),
                'about'    => [
                    '@type' => 'Organization',
                    'name'  => $brand['name'],
                    'foundingDate' => $brand['founding_year'] ?? null,
                ],
                'mainEntityOfPage' => current_url(),
            ]
        ];

        return $this->renderView('pages/about', ['faqs' => $faqs, 'extraSchemas' => $extraSchemas], $seo);
    }

    public function contact(): string
    {
        $contact = $this->settings['contact'];
        $brand = $this->settings['brand'];

        $seo = [
            'meta_title' => 'Hubungi Kami & Alamat Workshop | ' . $brand['name'],
            'meta_description' => 'Kontak customer service WhatsApp ' . $contact['phone'] . ', email ' . $contact['email'] . ', dan alamat workshop ' . $contact['address'],
            'breadcrumbs' => [
                ['name' => 'Beranda', 'url' => '/'],
                ['name' => 'Kontak', 'url' => '/kontak']
            ]
        ];

        // ContactPage JSON-LD (BRD E1)
        $extraSchemas = [
            [
                '@context' => 'https://schema.org',
                '@type'    => 'ContactPage',
                'name'     => 'Hubungi Kami',
                'url'      => current_url(),
                'mainEntity' => [
                    '@type' => 'Organization',
                    'name'  => $brand['name'],
                    'telephone' => $contact['phone'],
                    'email' => $contact['email'],
                    'address' => [
                        '@type' => 'PostalAddress',
                        'streetAddress' => $contact['address'],
                        'addressLocality' => $contact['city'],
                        'addressCountry' => 'ID'
                    ],
                ],
            ]
        ];

        return $this->renderView('pages/contact', ['extraSchemas' => $extraSchemas], $seo);
    }

    public function sendContactForm()
    {
        // --- Validasi input ---
        $rules = [
            'name'    => 'required|string|min_length[2]|max_length[100]',
            'phone'   => 'required|string|max_length[25]|regex_match[/^[0-9+\-() ]+$/]',
            'message' => 'required|string|min_length[5]|max_length[2000]',
        ];
        if (!$this->validateData($this->request->getPost() ?? [], $rules)) {
            $errors = $this->validator->getErrors();
            $firstError = $errors[array_key_first($errors)] ?? 'Input tidak valid.';
            if ($this->isHtmx) {
                $rawPost = $this->request->getPost() ?? [];
                $safeName = esc($rawPost['name'] ?? '');
                $safePhone = esc($rawPost['phone'] ?? '');
                $safeMsg = esc($rawPost['message'] ?? '');
                return '<div id="contact-response-container">
                    <form hx-post="/kontak/send" hx-target="#contact-response-container" hx-swap="outerHTML" hx-boost="false" hx-select="unset" class="space-y-4">
                      ' . csrf_field() . '
                      <div class="p-3.5 bg-rose-500/10 border border-rose-500/30 text-rose-400 rounded-xl text-xs flex items-center gap-2">
                        ' . lucide_icon('alert-circle', 'w-4 h-4 shrink-0') . '
                        <span>' . esc($firstError) . '</span>
                      </div>
                      <div class="grid gap-4 sm:grid-cols-2">
                        <div><label class="lbl" for="c-nama">Nama</label><input id="c-nama" type="text" name="name" value="' . $safeName . '" required class="field" placeholder="Nama Anda"></div>
                        <div><label class="lbl" for="c-wa">WhatsApp</label><input id="c-wa" type="text" name="phone" value="' . $safePhone . '" required class="field" placeholder="08xx xxxx xxxx"></div>
                      </div>
                      <div><label class="lbl" for="c-msg">Kebutuhan</label><textarea id="c-msg" name="message" rows="4" required class="field" placeholder="Contoh: 24 stel futsal, bahan Milano, kirim ke Bandung...">' . $safeMsg . '</textarea></div>
                      <button type="submit" class="btn-wa w-full">Kirim & lanjut ke WhatsApp</button>
                    </form>
                </div>';
            }
            return redirect()->back()->with('error', $firstError);
        }

        $name    = trim($this->request->getPost('name'));
        $phone   = trim($this->request->getPost('phone'));
        $message = trim($this->request->getPost('message'));

        // Build WA Link
        $waMsg = "Halo Admin " . $this->settings['brand']['name'] . ",\n\nNama: " . $name . "\nNo. HP: " . $phone . "\nPesan: " . $message;
        $waUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $this->settings['contact']['whatsapp']) . "?text=" . rawurlencode($waMsg);

        if ($this->isHtmx) {
            return '<div id="contact-response-container" class="p-6 bg-emerald-500/10 border border-emerald-500/30 rounded-[var(--radius-lg)] text-ink">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-emerald-500">' . lucide_icon('check-circle', 'w-6 h-6') . '</span>
                    <span class="font-bold text-base text-ink">Pesan Terkirim!</span>
                </div>
                <p class="text-xs text-muted mb-4 leading-relaxed">Terima kasih telah menghubungi kami. Tim CS kami akan merespons pesan Anda secara instan via WhatsApp.</p>
                <a href="' . $waUrl . '" target="_blank" hx-boost="false" class="btn-wa inline-flex">
                    ' . lucide_icon('message-circle', 'w-4 h-4') . '
                    <span>Lanjutkan ke WhatsApp</span>
                </a>
            </div>';
        }

        return redirect()->to($waUrl);
    }
}
