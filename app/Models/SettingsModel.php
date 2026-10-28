<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingsModel extends Model
{
    protected $table = 'settings';
    protected $primaryKey = 'id';
    protected $allowedFields = ['key_name', 'key_value', 'group_name', 'created_at', 'updated_at'];
    protected $useTimestamps = true;

    /**
     * Get all settings mapped as key => value pair, optionally filtered by group.
     */
    public function getGroup(string $groupName = ''): array
    {
        $builder = $this->builder();
        if ($groupName !== '') {
            $builder->where('group_name', $groupName);
        }
        $rows = $builder->get()->getResultArray();
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['key_name']] = $row['key_value'];
        }
        return $settings;
    }

    /**
     * Get all settings formatted into structured brand, theme, contact arrays.
     */
    /**
     * Upsert a single setting (insert if key doesn't exist, update if it does).
     */
    public function setSetting(string $key, ?string $value, string $group = 'general')
    {
        $existing = $this->where('key_name', $key)->first();
        $now = date('Y-m-d H:i:s');

        if ($existing) {
            $this->update($existing['id'], [
                'key_value'  => $value,
                'group_name' => $group,
                'updated_at' => $now,
            ]);
        } else {
            $this->insert([
                'key_name'   => $key,
                'key_value'  => $value,
                'group_name' => $group,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function getAllStructured(): array
    {
        $all = $this->getGroup();

        return [
            'brand' => [
                'name' => $all['brand_name'] ?? 'Pusat Jersey',
                'legal_name' => $all['brand_legal_name'] ?? 'PT Pusat Jersey Indonesia',
                'tagline' => $all['brand_tagline'] ?? 'Produsen Custom Jersey & Apparel Olahraga No.1',
                'logo' => $all['brand_logo'] ?? '/favicon.ico',
                'favicon' => $all['brand_favicon'] ?? '/favicon.ico',
                'founding_year' => $all['brand_founding_year'] ?? '2018',
            ],
            'contact' => [
                'phone' => $all['contact_phone'] ?? '+6281234567890',
                'whatsapp' => $all['contact_whatsapp'] ?? '6281234567890',
                'email' => $all['contact_email'] ?? 'info@pusatjersey.com',
                'address' => $all['contact_address'] ?? 'Jl. Raya Jersey No. 88',
                'city' => $all['contact_city'] ?? 'Jakarta Selatan',
                'maps_embed' => $all['contact_maps_embed'] ?? '',
                'opening_hours' => isset($all['contact_opening_hours_json']) ? json_decode($all['contact_opening_hours_json'], true) : [],
                'social_links' => isset($all['social_links_json']) ? json_decode($all['social_links_json'], true) : [],
            ],
            'about' => [
                'title' => $all['about_title'] ?? 'Tentang Kami',
                'short_desc' => $all['about_short_desc'] ?? '',
                'story_html' => $all['about_story_html'] ?? '',
                'vision' => $all['about_vision'] ?? '',
                'mission' => $all['about_mission'] ?? '',
                'stats' => isset($all['about_stats_json']) ? json_decode($all['about_stats_json'], true) : [],
            ],
            'theme' => [
                'color_primary' => $all['theme_color_primary'] ?? '#0284c7',
                'color_secondary' => $all['theme_color_secondary'] ?? '#0f172a',
                'color_accent' => $all['theme_color_accent'] ?? '#f59e0b',
                'bg_light' => $all['theme_color_bg_light'] ?? '#ffffff',
                'surface_light' => $all['theme_color_surface_light'] ?? '#ffffff',
                'surface_alt_light' => $all['theme_color_surface_alt_light'] ?? '#f1f5f9',
                'text_light' => $all['theme_color_text_light'] ?? '#0f172a',
                'text_muted_light' => $all['theme_color_text_muted_light'] ?? '#475569',
                'text_faint_light' => $all['theme_color_text_faint_light'] ?? '#64748b',
                'border_light' => $all['theme_color_border_light'] ?? '#e2e8f0',
                'bg_dark' => $all['theme_color_bg_dark'] ?? '#0b0f19',
                'surface_dark' => $all['theme_color_surface_dark'] ?? '#1e293b',
                'surface_alt_dark' => $all['theme_color_surface_alt_dark'] ?? '#111827',
                'text_dark' => $all['theme_color_text_dark'] ?? '#f8fafc',
                'text_muted_dark' => $all['theme_color_text_muted_dark'] ?? '#cbd5e1',
                'text_faint_dark' => $all['theme_color_text_faint_dark'] ?? '#64748b',
                'border_dark' => $all['theme_color_border_dark'] ?? '#334155',
                'cta_wa' => $all['theme_color_cta_wa'] ?? '#22c55e',
                'font_body' => $all['theme_font_body'] ?? 'Inter',
                'font_heading' => $all['theme_font_heading'] ?? 'Outfit',
                'font_google_import' => $all['theme_font_google_import'] ?? 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap',
            ],
            'partners' => isset($all['partners_json']) ? (json_decode($all['partners_json'], true) ?: []) : [],
        ];
    }
}
