<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Users extends Migration
{
	public function up()
	{
	    $this->forge->addField([
	        'id' => [
	            'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'full_name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => false,
                'unique' => true
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
                'unique' => true
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false,
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
                'unique' => true
            ],
            'bio' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'profileImgUrl' => [
                'type' => 'VARCHAR',
                'constraint' => '256',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'datetime',
                'null' => true,
            ],
            'created_at datetime default current_timestamp'
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('users');
		//
	}

	public function down()
	{
		//
        $this->forge->dropTable('users');
	}
}
