<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserFavourite extends Migration
{
	public function up()
	{
		//
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'null' => false
            ],
            'recipe_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'null' => false,
                'unique' => true
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['user_id', 'recipe_id'], false, true);
        $this->forge->createTable('user_favourites');
	}

	public function down()
	{
		//
        $this->forge->dropTable('user_favourites');
    }
}
