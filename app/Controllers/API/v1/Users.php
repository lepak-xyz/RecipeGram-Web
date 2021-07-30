<?php

namespace App\Controllers\API\V1;

use App\Entities\User;
use App\Models\RecipeModel;
use CodeIgniter\Database\Query;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Config\Database;

class Users extends ResourceController
{
    protected $db;
    protected $modelName = 'App\Models\UserModel';
    protected $format = 'json';

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function index()
    {
        try {
            $user = $this->getCurrentUser();

            return $this->respond(['status' => 200, 'data' => $user]);
        } catch (\Exception $e) {
            return $this->respond(['error' => $e->getMessage()], ResponseInterface::HTTP_BAD_REQUEST);
        }
    }

    public function update($id = null)
    {
        try {
            $user = $this->getCurrentUser();
            $rules = $this->model->getValidationRules(['only' => ['full_name', 'username', 'bio']]);
            $data = json_decode($this->request->getBody(), true);

            if (!$this->validate($rules)) {
                return $this->respond(["error" => implode(", ", $this->validator->getErrors())], ResponseInterface::HTTP_BAD_REQUEST);
            }

            if ($this->model->update($user->id, $data)) {
                return $this->respondUpdated(['status' => 200]);
            } else {
                return $this->respond(['status' => 500, "error" => "No data to be updated."], ResponseInterface::HTTP_BAD_REQUEST);
            }
        } catch (\Exception $e) {
            return $this->respond(['error' => $e->getMessage()], ResponseInterface::HTTP_BAD_REQUEST);
        }
    }

    public function updateImage()
    {
        try {
            if ($this->request->getServer('REQUEST_METHOD') != 'POST' || !$this->request->getFile('image')) {
                throw new \Exception("Invalid request");
            }

            $user = $this->getCurrentUser();
            $file = $this->request->getFile('image');
            $profile_img = $file->getName();

            $temp = explode(".", $profile_img);
            $newfilename = round(microtime(true)) . '.' . end($temp);

            if ($file->move("images", $newfilename)) {
                $data = [
                    'profileImgUrl' => base_url("images/{$newfilename}"),
                ];

                $oldFile = "images/" . basename($user->profileImgUrl);
                if ($user->profileImgUrl != null && !empty($user->profileImgUrl) && file_exists($oldFile)) {
                    @unlink($oldFile);
                }

                if ($this->model->update($user->id, $data)) {
                    $response = [
                        'status' => 200,
                        'message' => 'File uploaded successfully',
                    ];
                } else {
                    $response = [
                        'status' => 500,
                        'error' => 'Failed to save image',
                    ];
                }
            } else {
                $response = [
                    'status' => 500,
                    'error' => 'Failed to upload image',
                ];
            }

            return $this->respondCreated($response);
        } catch (\Exception $e) {
            return $this->respond(['error' => $e->getMessage()], ResponseInterface::HTTP_BAD_REQUEST);
        }
    }

    public function recipe()
    {
        try {
            $user = $this->getCurrentUser();

            $model = new RecipeModel();

            // RETRIEVE
            if ($this->request->getServer('REQUEST_METHOD') == "GET") {
                //$data = $model->where('author', $user->id)->findAll();
                $data = $model->getAllRecipes($user->id);

                return $this->respond(['status' => 200, 'data' => $data]);
            } elseif ($this->request->getServer('REQUEST_METHOD') == 'POST' && $this->request->getFile('image')) {
                // IMAGE
                $file = $this->request->getFile('image');
                $profile_img = $file->getName();

                $temp = explode(".", $profile_img);
                $newfilename = round(microtime(true)) . '.' . end($temp);

                // DB
                if ($file->move("images/recipe/", $newfilename)) {
                    $data = $this->request->getPost();
                    $data = json_decode($data['data'], true);
                    $data['ingredients'] = $this->formatToJson($data['ingredients']);
                    $data['instructions'] = $this->formatToJson($data['instructions']);

                    $repEt = new \App\Entities\Recipe($data);
                    $repEt->author = $user->id;
                    $repEt->images = base_url('images/recipe/' . $newfilename);

                    $model->save($repEt);

                    return $this->respondCreated(['status' => 200]);
                } else {
                    return $this->respond(['status' => 500, 'error' => 'Server error. Failed to upload image.'], ResponseInterface::HTTP_BAD_REQUEST);
                }
            }
        } catch (\Exception $e) {
            return $this->respond(['error' => $e->getMessage()], ResponseInterface::HTTP_BAD_REQUEST);
        }
    }

    private function formatToJson($text)
    {
        $res = [];
        $tmp = explode("\n", $text);

        foreach ($tmp as $idx => $val) {
            $idx++;
            $res["{$idx}"] = $val;
        }

        return $res;
    }

    public function favourites()
    {
        $action = $this->request->getGet("action");

        try {
            $user = $this->getCurrentUser();

            $builder = $this->db->table('user_favourites');
            switch ($action) {
                case "add":
                    // TODO
                    // ONLY TOKEN USER CAN ADD
                    try {
                        $data = [
                            'user_id' => $user->id,
                            'recipe_id' => $this->request->getGet('rid')
                        ];
                        $builder->insert($data);
                        return $this->respondCreated(['message' => 'Successfully added!']);
                    } catch (\Exception $e) {
                        return $this->respond(['error' => ['server' => $e->getMessage()]]);
                    }

                case "remove":
                    // TODO
                    // ONLY TOKEN USER CAN ADD
                    try {
                        $builder->delete(['user_id' => $user->id, 'recipe_id' => $this->request->getGet('rid')]);
                        return $this->respondDeleted(['message' => 'Successfully deleted!']);
                    } catch (\Exception $e) {
                        return $this->respond(['error' => ['server' => $e->getMessage()]], ResponseInterface::HTTP_BAD_REQUEST);
                    }

                case "get":
                default:
                    /*
                    $builder->groupStart()->select(['*', 'COUNT(user_heats.recipe_id) as heat'])->from('recipe')
                        ->join('user_heats', 'recipe.id = user_heats.recipe_id', 'left')->groupEnd()->;

                    $builder->join('recipe', 'recipe.id = user_favourites.recipe_id');
                    $builder->join('user_heats', 'user_heats.recipe_id = recipe.id', 'left');
                    $builder->groupBy('recipe.id');

                        $builder->where('user_favourites.user_id', $user->id);
                    */

                    $pQuery = $this->db->prepare(function ($db) {
                        $sql = "SELECT c.* FROM user_favourites a INNER JOIN (SELECT b.*, COUNT(c.recipe_id) AS heat FROM recipe b LEFT JOIN user_heats c ON c.recipe_id = b.id GROUP BY b.id) as c ON c.id = a.recipe_id WHERE a.user_id = ?;";
                        return (new Query($db))->setQuery($sql);
                    });

                    return $this->respond(['status' => 200, 'data' => $pQuery->execute($user->id)->getCustomResultObject(\App\Entities\Recipe::class)]);
            }
        } catch (\Exception $e) {
            return $this->respond(['error' => $e->getMessage()], ResponseInterface::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @throws \Exception
     */
    private function getCurrentUser(): User
    {
        try {
            helper('jwt');

            $token = $this->request->getServer('HTTP_AUTHORIZATION');
            $encodedToken = getJWTFromRequest($token);
            return validateJWTFromRequest($encodedToken);
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}
