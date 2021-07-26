<?php

namespace App\Controllers\API\V1;

use App\Models\RecipeModel;
use CodeIgniter\RESTful\ResourceController;
use Config\Database;

class Users extends ResourceController
{
    protected $db;
    protected $format = 'json';

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function index()
	{
	    return $this->respond(['data' => 200]);
		//
	}

	public function recipe() {
        $model = new RecipeModel();
        $data = $model->where('author', $this->request->getGet('id'))->findAll();

        return $this->respond(['status' => 200, 'data' => $data]);
    }

	public function favourites() {
        $action = $this->request->getGet("action");

        $builder = $this->db->table('user_favourites');
        switch ($action) {
            case "add":
                // TODO
                // ONLY TOKEN USER CAN ADD
                try {
                    $data = [
                        'user_id' => $this->request->getGet('uid'),
                        'recipe_id' => $this->request->getGet('rid')
                    ];
                    $builder->insert($data);
                    return $this->respond(['message' => 'Successfully added!']);
                } catch (\Exception $e) {
                    return $this->respond(['error' => ['server' => $e->getMessage()]]);
                }
                break;

            case "get":
                $builder->select('*');
                $builder->join('recipe', 'recipe.id = user_favourites.recipe_id');
                $builder->where('user_id', $this->request->getGet('uid'));
                $query = $builder->get();

                return $this->respond(['status' => 200, 'data' => $query->getCustomResultObject(\App\Entities\Recipe::class)]);
                break;

            case "remove":
                // TODO
                // ONLY TOKEN USER CAN ADD
                try {
                    $builder->delete(['user_id' => $this->request->getGet('uid'), 'recipe_id' => $this->request->getGet('rid')]);
                    return $this->respond(['message' => 'Successfully deleted!']);
                } catch (\Exception $e) {
                    return $this->respond(['error' => ['server' => $e->getMessage()]]);
                }
                break;

            default:
                return $this->respond(['error' => ['server' => 'Invalid request.']]);
        }
    }
}
