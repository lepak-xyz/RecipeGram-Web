<?php

namespace App\Controllers\API\V1\Recipe;

use CodeIgniter\RESTful\ResourceController;

class Tags extends ResourceController
{
    protected $modelName = 'App\Models\Tags';
    protected $format = 'json';

	public function index()
	{
		//
        return $this->respond($this->model->findAll());
	}

	public function show($id = null)
    {
        if (is_numeric($id)) {
            $tag = $this->model->find($id);

            return $this->respond($tag);
        } else {
            return $this->failServerError();
        }
    }

    public function respond($data = null, int $status = null, $message = '')
    {
        return parent::respond(['status' => 200, 'data' => $data]);
    }
}
