<?php namespace App\Models;

use \App\Models\BaseModel;

class ProfileModel extends BaseModel
{
  protected $table      = 'profiles';
  protected $primaryKey = 'uuid';

  protected $returnType     = 'array';
  protected $useSoftDeletes = true;

  protected $allowedFields = ['account', 'status'];

  protected $useTimestamps = false;
  protected $createdField  = 'created_at';
  protected $updatedField  = 'updated_at';
  protected $deletedField  = 'deleted_at';

  protected $beforeInsert = ['generateUUID'];
  protected $beforeUpdate = ['beforeUpdate'];

  /**
   * Возвращает все активные аккаунты
   * @return array
   */
  public function findAllActive()
  {
    $profiles = $this->where('status', 1)->findAll();
    return !empty($profiles) ? $profiles : [];
  }
}