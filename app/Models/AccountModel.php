<?php namespace App\Models;

use \App\Models\BaseModel;

class AccountModel extends BaseModel
{
  protected $table      = 'accounts';
  protected $primaryKey = 'uuid';

  protected $returnType     = 'array';
  protected $useSoftDeletes = true;

  protected $allowedFields = ['login', 'nid', 'status'];

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
    $result = $this
      ->select('accounts.*')
      ->select('CONCAT(networks.link, accounts.login) link')
      ->join('networks', 'networks.id = accounts.nid', 'LEFT')
      ->where('status', 1)
      ->findAll()
    ;

    // Добавление информации по количеству постов стоящих на модерации
    if (!empty($result)) {
      $contentModel = new ContentModel();
      foreach ($result as $key => $item) {
        $result[$key]['in_mod'] = $contentModel->getCountNewContents($item['uuid']);
      }
    }

    return !empty($result) ? $result : [];
  }
}