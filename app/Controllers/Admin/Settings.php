<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingsModel;

class Settings extends BaseController
{
    public function index()
    {
        $settingsModel = new SettingsModel();
        $allSettings = $settingsModel->findAll();

        $grouped = [];
        foreach ($allSettings as $s) {
            $grouped[$s['group_name']][$s['key_name']] = $s['key_value'];
        }

        $data = [
            'metaTitle' => 'Pengaturan Brand & Sistem — Admin Pusat Jersey',
            'grouped'   => $grouped,
        ];

        return $this->renderAdminView('admin/settings/index', $data);
    }

    public function update()
    {
        $settingsModel = new SettingsModel();
        $posts = $this->request->getPost('settings') ?? [];

        // Whitelist key yang boleh ditulis lewat form (mencegah penimpaan kredensial admin & key internal)
        $allowedKeys = [
            'brand_name', 'brand_legal_name', 'brand_tagline', 'brand_logo', 'brand_favicon', 'brand_founding_year',
            'contact_phone', 'contact_whatsapp', 'contact_email', 'contact_address', 'contact_city',
            'contact_maps_embed', 'contact_opening_hours_json', 'social_links_json',
            'partners_json',
            'about_title', 'about_short_desc', 'about_story_html', 'about_vision', 'about_mission', 'about_stats_json',
            'theme_color_primary', 'theme_color_secondary', 'theme_color_accent',
            'theme_color_bg_light', 'theme_color_surface_light', 'theme_color_surface_alt_light',
            'theme_color_text_light', 'theme_color_text_muted_light', 'theme_color_text_faint_light', 'theme_color_border_light',
            'theme_color_bg_dark', 'theme_color_surface_dark', 'theme_color_surface_alt_dark',
            'theme_color_text_dark', 'theme_color_text_muted_dark', 'theme_color_text_faint_dark', 'theme_color_border_dark',
            'theme_color_cta_wa', 'theme_font_body', 'theme_font_heading', 'theme_font_google_import',
        ];

        foreach ($posts as $group => $fields) {
            foreach ($fields as $key => $val) {
                if (!in_array($key, $allowedKeys, true)) {
                    continue; // abaikan key di luar whitelist
                }
                if (is_array($val)) {
                    $val = json_encode($val);
                }
                $settingsModel->setSetting($key, $val, $group);
            }
        }

        // Flush cache settings structured agar perubahan langsung terlihat di frontend
        cache()->delete('settings_structured_v1');

        return '<div class="p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 rounded-2xl text-xs flex items-center space-x-2">
            ' . lucide_icon('check-circle', 'w-5 h-5 shrink-0') . '
            <span>Pengaturan berhasil diperbarui secara instan!</span>
        </div>';
    }
}
