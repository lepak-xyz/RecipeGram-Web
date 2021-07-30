<?php

namespace App\Models;

use App\Entities\Recipe;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use CodeIgniter\Validation\ValidationInterface;

class RecipeModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'recipe';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = Recipe::class;
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['author', 'name', 'caption', 'heat', 'tags', 'ingredients', 'instructions', 'images'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = ['heat'];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    public function getAllRecipes($keyword = null)
    {
        $this->orderBy('created_at', 'desc');
        $this->select(['recipe.*', 'COUNT(user_heats.recipe_id) as heat']);
        $this->join('user_heats', 'user_heats.recipe_id = recipe.id', 'left');
        if (!is_null($keyword)) {
            if (is_numeric($keyword)) {
                $this->where('recipe.author', $keyword);
            } else {
                $this->like('recipe.name', $keyword);
            }
        }
        $this->groupBy('recipe.id');

        return $this->get()->getCustomResultObject(Recipe::class);
    }

    protected function heat(array $data): array
    {
        if (!isset($data['data']->id)) return $data;

        $builder = $this->db->table('user_heats');
        $builder->where('recipe_id', $data['data']->id);
        $query = $builder->selectCount('recipe_id', 'heat');
        $data['data']->heat = $query->get()->getFirstRow()->heat;

        return $data;
    }
}
