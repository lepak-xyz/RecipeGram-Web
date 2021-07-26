<?php

namespace App\Models;

use App\Entities\User;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = User::class;
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['username', 'email', 'password', 'phone'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'full_name' => 'permit_empty|min_length[3]',
        'username' => 'required|alpha_numeric_space|min_length[3]|is_unique[users.username]',
        'email' => 'required|valid_email|is_unique[users.email]',
        'password' => 'required|min_length[8]',
        'phone' => 'required|min_length[8]|is_unique[users.phone]',
    ];
    protected $validationMessages = [
        'username' => [
            'is_unique' => 'Sorry. That email has already been taken. Please choose another.'
        ],
        'email' => [
            'is_unique' => 'Sorry. That email has already been taken. Please choose another.'
        ],
        'phone' => [
            'is_unique' => 'Sorry. That email has already been taken. Please choose another.'
        ]
    ];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = ['favourite'];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    protected function favourite(array $data): array
    {
        $user = $data['data'];

        $builder = $this->db->table("user_favourites");
        $query = $builder->select('recipe_id')->where('user_id', $user->id)->get();
        $fav = [];

        foreach ($query->getResultArray() as $rid) {
            $fav[] = (int)$rid['recipe_id'];
        }

        $data['data']->favourites = $fav;
        return $data;
    }
}