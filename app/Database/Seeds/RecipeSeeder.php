<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RecipeSeeder extends Seeder
{
	public function run()
	{
		//

        for ($i=0; $i < 10; $i++) {
            $this->generateRecipe();
        }
	}

    private function generateRecipe()
    {
        $model = model('RecipeModel');

        $model->insert([
            'author' => rand(1, 10),
            'name' => static::faker()->text(32),
            'caption' => static::faker()->text(50),
            'heat' => rand(0, 1000),
            'tags' => json_encode(static::faker()->randomElements(['1', '2', '3'], rand(1, 2))),
            'images' => static::faker()->imageUrl(120, 120),
            'ingredients' => $this->generateRandomArray(),
            'instructions' => $this->generateRandomArray(),
        ]);
    }

    private function generateRandomArray() {
	    $ingredients = Array();

	    for ($i = 1; $i < rand(4, 10); $i++)
	        $ingredients[(string)$i] = static::faker()->text(15);
	        //array_push($ingredients, Array("$i" => static::faker()->text(15)));

	    return json_encode($ingredients);
    }
}
