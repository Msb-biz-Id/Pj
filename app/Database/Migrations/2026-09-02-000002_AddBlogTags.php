<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBlogTags extends Migration
{
    public function up()
    {
        // TAGS
        $this->forge->addField([
            'id' => ['type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'slug' => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tags');

        // POST TAGS (Pivot)
        $this->forge->addField([
            'id' => ['type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => true],
            'post_id' => ['type' => 'INTEGER', 'constraint' => 11],
            'tag_id' => ['type' => 'INTEGER', 'constraint' => 11],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['post_id', 'tag_id']);
        $this->forge->createTable('post_tags');
    }

    public function down()
    {
        $this->forge->dropTable('post_tags', true);
        $this->forge->dropTable('tags', true);
    }
}
