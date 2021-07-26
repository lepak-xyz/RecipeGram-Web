<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Recipe extends Migration
{
	public function up()
	{
		//
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'author' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false
            ],
            'caption' => [
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
                'unique' => true
            ],
            'images' => [
                'type' => 'VARCHAR',
                'constraint' => '128',
                'null' => false
            ],
            'heat' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'tags' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'ingredients' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'instructions' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'datetime',
                'null' => true,
            ],
            'created_at datetime default current_timestamp',
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('recipe');
	}

	public function down()
	{
        $this->forge->dropTable('recipe');
	}
}
