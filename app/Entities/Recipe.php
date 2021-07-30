<?php

namespace App\Entities;

use App\Models\UserModel;
use CodeIgniter\Entity\Entity;
use Config\Database;

class Recipe extends Entity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
	    'tags' => 'json',
        'ingredients' => 'json',
        'instructions' => 'json'
    ];
	protected $db;

    public function __construct(array $data = null)
    {
        parent::__construct($data);

        $this->db = Database::connect();
    }

    public function addHeat($user): bool
    {
        $builder = $this->db->table('user_heats');
        if(!$builder->insert(['user_id' => $user, 'recipe_id' => $this->attributes['id']])) {
            return false;
        }

        return true;
    }

    public function removeHeat($user): bool
    {
        $builder = $this->db->table('user_heats');
        if(!$builder->delete(['user_id' => $user, 'recipe_id' => $this->attributes['id']])) {
            return false;
        }

        return true;
    }


    public function getAuthor() {
        $builder = $this->db->table('users');
        $query = $builder->select('id, full_name, username, profileImgUrl')->where('id', $this->attributes['author'])->get();
        $data = $query->getCustomResultObject(User::class);

	    return count($data) > 0 ? $data[0] : null;
    }
}
