<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInitialSchema extends Migration
{
    public function up()
    {
        // 1. SETTINGS
        $this->forge->addField([
            'id' => ['type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => true],
            'key_name' => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'key_value' => ['type' => 'TEXT', 'null' => true],
            'group_name' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'general'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('settings');

        // 2. CATEGORIES
        $this->forge->addField([
            'id' => ['type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'slug' => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'type' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'blog'], // blog, product, portfolio
            'description' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('categories');

        // 3. POSTS (Blog)
        $this->forge->addField([
            'id' => ['type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => true],
            'category_id' => ['type' => 'INTEGER', 'constraint' => 11, 'null' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug' => ['type' => 'VARCHAR', 'constraint' => 255, 'unique' => true],
            'excerpt' => ['type' => 'TEXT', 'null' => true],
            'content' => ['type' => 'TEXT'],
            'featured_image' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'meta_title' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'meta_description' => ['type' => 'TEXT', 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'published'],
            'views_count' => ['type' => 'INTEGER', 'constraint' => 11, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('posts');

        // 4. PRODUCTS
        $this->forge->addField([
            'id' => ['type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => true],
            'category_id' => ['type' => 'INTEGER', 'constraint' => 11, 'null' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug' => ['type' => 'VARCHAR', 'constraint' => 255, 'unique' => true],
            'sku' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'price' => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'discount_price' => ['type' => 'DECIMAL', 'constraint' => '12,2', 'null' => true],
            'is_custom' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'is_affiliate' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'affiliate_link' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'shopee_link' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'tokopedia_link' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'tiktok_link' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'featured_image' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'gallery_json' => ['type' => 'TEXT', 'null' => true],
            'description' => ['type' => 'TEXT', 'null' => true],
            'specs_json' => ['type' => 'TEXT', 'null' => true],
            'meta_title' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'meta_description' => ['type' => 'TEXT', 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'published'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('products');

        // 5. SERVICES
        $this->forge->addField([
            'id' => ['type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug' => ['type' => 'VARCHAR', 'constraint' => 255, 'unique' => true],
            'short_desc' => ['type' => 'TEXT', 'null' => true],
            'content' => ['type' => 'TEXT'],
            'icon' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'shirt'],
            'featured_image' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'starting_price' => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'meta_title' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'meta_description' => ['type' => 'TEXT', 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'published'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('services');

        // 6. PORTFOLIOS
        $this->forge->addField([
            'id' => ['type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => true],
            'category_id' => ['type' => 'INTEGER', 'constraint' => 11, 'null' => true],
            'client_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'title' => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug' => ['type' => 'VARCHAR', 'constraint' => 255, 'unique' => true],
            'featured_image' => ['type' => 'VARCHAR', 'constraint' => 255],
            'mockup_before_image' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'real_after_image' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'gallery_json' => ['type' => 'TEXT', 'null' => true],
            'description' => ['type' => 'TEXT', 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'published'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('portfolios');

        // 7. LOCATIONS
        $this->forge->addField([
            'id' => ['type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 100], // e.g. Jakarta, Surabaya
            'slug' => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'province' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'type' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'kota'], // kota, kabupaten
            'latitude' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'longitude' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('locations');

        // 8. LOCATION SERVICES (Pivot)
        $this->forge->addField([
            'id' => ['type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => true],
            'service_id' => ['type' => 'INTEGER', 'constraint' => 11],
            'location_id' => ['type' => 'INTEGER', 'constraint' => 11],
            'custom_title' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'custom_content' => ['type' => 'TEXT', 'null' => true],
            'meta_title' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'meta_description' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['service_id', 'location_id']);
        $this->forge->createTable('location_services');

        // 9. FAQS
        $this->forge->addField([
            'id' => ['type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => true],
            'question' => ['type' => 'TEXT'],
            'answer' => ['type' => 'TEXT'],
            'post_type' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'global'], // global, service, product, portfolio
            'target_id' => ['type' => 'INTEGER', 'constraint' => 11, 'null' => true],
            'sort_order' => ['type' => 'INTEGER', 'constraint' => 5, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('faqs');

        // 10. TESTIMONIALS
        $this->forge->addField([
            'id' => ['type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => true],
            'client_name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'team_name' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'city' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'avatar' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'comment' => ['type' => 'TEXT'],
            'rating' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 5],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'approved'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('testimonials');

        // 11. PAGE SECTIONS (Page Builder)
        $this->forge->addField([
            'id' => ['type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => true],
            'page_name' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'home'],
            'section_key' => ['type' => 'VARCHAR', 'constraint' => 50], // hero, services, products, etc.
            'layout_style' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'default'],
            'sort_order' => ['type' => 'INTEGER', 'constraint' => 5, 'default' => 0],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'settings_json' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('page_sections');

        // 12. CTA LOGS
        $this->forge->addField([
            'id' => ['type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => true],
            'channel' => ['type' => 'VARCHAR', 'constraint' => 30], // whatsapp, shopee, tokopedia, tiktok
            'product_slug' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'source_page' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'utm_source' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'user_agent' => ['type' => 'TEXT', 'null' => true],
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('cta_logs');

        // 13. COMMENTS
        $this->forge->addField([
            'id' => ['type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => true],
            'post_id' => ['type' => 'INTEGER', 'constraint' => 11],
            'parent_id' => ['type' => 'INTEGER', 'constraint' => 11, 'null' => true],
            'author_name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'author_email' => ['type' => 'VARCHAR', 'constraint' => 100],
            'author_website' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'content' => ['type' => 'TEXT'],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending'], // pending, approved, spam
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'user_agent' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('comments');
    }

    public function down()
    {
        $this->forge->dropTable('comments', true);
        $this->forge->dropTable('cta_logs', true);
        $this->forge->dropTable('page_sections', true);
        $this->forge->dropTable('testimonials', true);
        $this->forge->dropTable('faqs', true);
        $this->forge->dropTable('location_services', true);
        $this->forge->dropTable('locations', true);
        $this->forge->dropTable('portfolios', true);
        $this->forge->dropTable('services', true);
        $this->forge->dropTable('products', true);
        $this->forge->dropTable('posts', true);
        $this->forge->dropTable('categories', true);
        $this->forge->dropTable('settings', true);
    }
}
