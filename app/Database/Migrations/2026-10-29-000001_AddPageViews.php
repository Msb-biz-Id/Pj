<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPageViews extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => true],
            'path'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'source'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'utm_source' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'utm_medium' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'utm_campaign' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'sid'        => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => true],  // hash, bukan session mentah
            'device'     => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true], // desktop|mobile|tablet|bot
            'referer'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'is_ajax'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('path');
        $this->forge->addKey('created_at');
        $this->forge->createTable('page_views');
    }

    public function down()
    {
        $this->forge->dropTable('page_views', true);
    }
}
