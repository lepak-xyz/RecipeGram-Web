<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Faker\Factory;

class UsersSeeder extends Seeder
{
	public function run()
	{
		//
        for ($i=0; $i < 10; $i++) {
            $this->db->table('users')->insert($this->generateUsers());
        }
	}

    private function generateUsers(): array
    {
        return [
            'full_name' => static::faker()->name(),
            'username' => static::faker()->userName(),
            'email' => static::faker()->email(),
            'password' => static::faker()->password(12),
            'phone' => static::faker()->phoneNumber(),
            'bio' => static::faker()->text(20),
        ];
    }
}
