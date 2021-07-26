<?php

namespace App\Controllers\API\v1;

use App\Entities\User;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Exception;

class Auth extends ResourceController
{
    protected $modelName = UserModel::class;
    protected $format = 'json';

    public function index()
    {
        //
    }

    public function register() {
        $rules = $this->model->getValidationRules(['except' => ['login_email']]);

        if(!$this->validate($rules)) {
            return $this->respond([
                'error' => $this->validator->getErrors(),
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        try {
            $uModel = new UserModel();
            $user = new User($this->request->getGet());
            $uModel->save($user);
        } catch (Exception $e) {
            return $this->respond(['error' => $e->getMessage()]);
        }

        return $this->getJWTForUser($this->request->getGet('email'), $this->request->getGet('password'), ResponseInterface::HTTP_CREATED);
    }

    public function login()
    {
        $rules = $this->model->getValidationRules(['only' => ['password']]);
        $rules['email'] = 'required|valid_email';

        if (!$this->validate($rules)) {
            return $this->respond([
                'error' => $this->validator->getErrors(),
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        return $this->getJWTForUser($this->request->getGet('email'), $this->request->getGet('password'));
    }

    private function getJWTForUser(string $emailAddress, string $password, int $responseCode = ResponseInterface::HTTP_OK)
    {
        try {
            $user = $this->model->where(['email' => $emailAddress, 'password' => $password])->first();
            if (!$user)
                return $this->respond(['error' => ['account' => 'Invalid username or password']]);

            unset($user->password);

            helper('jwt');

            return $this->respond([
                'message' => 'User authenticated successfully',
                'user' => $user,
                'access_token' => getSignedJWTForUser($emailAddress)
            ]);
        } catch (Exception $exception) {
            return $this->respond(['error' => $exception->getMessage()], $responseCode);
        }
    }
}
