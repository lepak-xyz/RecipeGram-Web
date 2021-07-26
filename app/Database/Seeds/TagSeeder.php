<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TagSeeder extends Seeder
{
	public function run()
	{
		//
        $model = model('Tags');

        $model->insert(['name' => 'Western']);
        $model->insert(['name' => 'Local']);
        $model->insert(['name' => 'Dessert']);
	}
}
