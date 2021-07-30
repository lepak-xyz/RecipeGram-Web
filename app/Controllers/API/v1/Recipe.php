<?php

namespace App\Controllers\API\v1;

use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Config\Database;

class Recipe extends ResourceController
{
    protected $db;
    protected $modelName = 'App\Models\RecipeModel';
    protected $format = 'json';

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function index()
	{
	    return $this->respond(['status' => 200, 'data' => $this->model->getAllRecipes()]);
	    //return $this->respond($this->model->orderBy('created_at', 'desc')->findAll());
	}

	public function show($id = null)
    {
        if (is_numeric($id)) {
            $data = [$this->model->find($id)];
        } else {
            $data = $this->model->getAllRecipes($id);
        }

        if (!empty($data)) {
            return $this->respond(['status' => 200, 'data' => $data]);
        } else {
            return $this->respond(['status' => 404, 'error' => 'No recipes found.'], ResponseInterface::HTTP_BAD_REQUEST);
        }
    }

    public function update($id = null) {
        $data = json_decode($this->request->getBody(), true);

        switch ($data['type']) {
            case "heat":
                $recipe = $this->model->find($id);

                if (is_null($recipe)) {
                    return $this->respond(['status' => 404, 'error' => 'Recipe not found.'], ResponseInterface::HTTP_BAD_REQUEST);
                }

                if (!isset($data['user_id']) || empty($data['user_id'])) {
                    return $this->respond(['status' => 400, 'error' => 'Please specify a user..'], ResponseInterface::HTTP_BAD_REQUEST);
                }

                if (!isset($data['remove'])) {
                    return $this->respond(['status' => 400, 'error' => 'Please specify request type..'], ResponseInterface::HTTP_BAD_REQUEST);
                }

                try {
                    if ($data['remove']) {
                        $recipe->removeHeat($data['user_id']);
                    } else {
                        $recipe->addHeat($data['user_id']);
                    }
                } catch (\Exception $e) {
                    return $this->respond(['status' => 500, 'error' => 'Could not add/remove heat.'], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
                }
                break;

            case "recipe":

                break;

            default:
                return $this->respond(null, ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->respondUpdated(['status' => 200, 'data' => $data]);
    }

    public function create()
    {
        $data = $this->request->getPost();
        $recipe = new \App\Entities\Recipe;
        $recipe->fill($data);

        if ($this->model->save($recipe)) {
            return $this->respondCreated(['status' => 200, 'message' => 'Successfully added.']);
        } else {
            return $this->respond(['status' => 500, 'message' => 'Could not save to database.'], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
