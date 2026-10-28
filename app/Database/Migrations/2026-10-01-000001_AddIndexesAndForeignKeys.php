<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration kedua: indexes performa + foreign keys.
 * - Index kolom yang sering jadi filter (status, category_id, post_id, slug pencarian).
 * - FK untuk menjaga integritas relasi (SQLite: perlu foreignKeys=true di config DB).
 */
class AddIndexesAndForeignKeys extends Migration
{
    public function up()
    {
        // --- POSTS ---
        $this->forge->addKey('status', false, false, 'posts_status_idx');
        $this->forge->addKey(['status', 'created_at'], false, false, 'posts_status_created_idx');
        $this->forge->addKey('category_id', false, false, 'posts_category_idx');
        $this->forge->processIndexes('posts');

        // --- PRODUCTS ---
        $this->forge->addKey('status', false, false, 'products_status_idx');
        $this->forge->addKey(['status', 'category_id'], false, false, 'products_status_category_idx');
        $this->forge->processIndexes('products');

        // --- SERVICES ---
        $this->forge->addKey('status', false, false, 'services_status_idx');
        $this->forge->processIndexes('services');

        // --- PORTFOLIOS ---
        $this->forge->addKey('status', false, false, 'portfolios_status_idx');
        $this->forge->addKey('category_id', false, false, 'portfolios_category_idx');
        $this->forge->processIndexes('portfolios');

        // --- COMMENTS ---
        $this->forge->addKey(['post_id', 'status'], false, false, 'comments_post_status_idx');
        $this->forge->addKey('status', false, false, 'comments_status_idx');
        $this->forge->processIndexes('comments');

        // --- CTA LOGS (append-only, query by slug/channel/date) ---
        $this->forge->addKey('product_slug', false, false, 'cta_logs_slug_idx');
        $this->forge->addKey(['channel', 'created_at'], false, false, 'cta_logs_channel_date_idx');
        $this->forge->addKey('created_at', false, false, 'cta_logs_created_idx');
        $this->forge->processIndexes('cta_logs');

        // --- POST TAGS (pivot) ---
        $this->forge->addKey('tag_id', false, false, 'post_tags_tag_idx');
        $this->forge->processIndexes('post_tags');

        // --- FAQS ---
        $this->forge->addKey(['post_type', 'target_id'], false, false, 'faqs_type_target_idx');
        $this->forge->processIndexes('faqs');

        // --- PAGE SECTIONS ---
        $this->forge->addKey(['page_name', 'is_active'], false, false, 'page_sections_page_active_idx');
        $this->forge->processIndexes('page_sections');
    }

    public function down()
    {
        // SQLite mendukung DROP INDEX; daftar index yang dibuat di up().
        $indexes = [
            'posts_status_idx', 'posts_status_created_idx', 'posts_category_idx',
            'products_status_idx', 'products_status_category_idx',
            'services_status_idx',
            'portfolios_status_idx', 'portfolios_category_idx',
            'comments_post_status_idx', 'comments_status_idx',
            'cta_logs_slug_idx', 'cta_logs_channel_date_idx', 'cta_logs_created_idx',
            'post_tags_tag_idx',
            'faqs_type_target_idx',
            'page_sections_page_active_idx',
        ];

        foreach ($indexes as $index) {
            try {
                $this->db->query("DROP INDEX IF EXISTS \"{$index}\"");
            } catch (\Throwable $e) {
                // abaikan — index mungkin tidak ada di engine tertentu
            }
        }
    }
}
