<?php namespace App\Models;

use \App\Models\BaseModel;

class PostModel extends BaseModel
{
  protected $table      = 'posts';
  protected $primaryKey = 'uuid';

  protected $returnType     = 'array';
  protected $useSoftDeletes = true;

  protected $allowedFields = ['uuid', 'uuid_account', 'pid', 'nid', 'timestamp', 'shortcode', 'desc', 'comments', 'likes', 'medias', 'status'];

  protected $useTimestamps = false;
  protected $createdField  = 'created_at';
  protected $updatedField  = 'updated_at';
  protected $deletedField  = 'deleted_at';

  protected $beforeInsert = ['generateUUID'];
  protected $beforeUpdate = ['beforeUpdate'];

  /**
   * Проверяет наличие записи по Id-контента первоисточника
   * @param $pid
   * @param $nid
   * @return mixed
   */
  public function isStockPost($pid, $nid)
  {
    if (empty($pid) || empty($nid)) return false;

    $result = $this
      ->where('pid', $pid)
      ->where('nid', $nid)
      ->countAllResults()
    ;

    return $result;
  }

  /**
   * Запись данных о медиа
   * @param $data
   */
  public function insertMediaTemp($data)
  {
    $this->db->table('medias_tmp')->insert($data);
  }

  /**
   * Получение всех временных медиа данных
   * @param $uuid
   * @return int
   */
  public function getMediaTemp($uuid)
  {
    $result = [];
    if (!empty($uuid))
      $result = $this->db->table('medias_tmp')->where('uuid_post', $uuid)->get()->getResultArray();

    return $result;
  }

  /**
   * Возвращает все активные аккаунты
   * @return array
   */
  public function findAllModeration()
  {
    $result = $this
      ->where('status', 0)
      ->findAll()
    ;

    if (!empty($result)) {
      foreach ($result as $key => $item) {
        $result[$key]['medias'] = $this->getMediaTemp($item['uuid']);
      }
    }

    return !empty($result) ? $result : [];
  }

  /**
   * Получение количества постов, стоящих на модерацию
   * @param $uuid
   * @return int|string
   */
  public function getCountNewPosts($uuid = false)
  {
    $this->where('status', 0);
    if (!empty($uuid)) $this->where('uuid_account', $uuid);

    $result = $this->countAllResults();

    return !empty($result) ? $result : 0;
  }
}