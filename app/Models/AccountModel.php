<?php namespace App\Models;

use \App\Models\BaseModel;

class AccountModel extends BaseModel
{
  protected $table      = 'accounts';
  protected $primaryKey = 'uuid';

  protected $returnType     = 'array';
  protected $useSoftDeletes = true;

  protected $allowedFields = ['login', 'nid', 'status', 'tag_ids', 'last_parse'];

  protected $useTimestamps = false;
  protected $createdField  = 'created_at';
  protected $updatedField  = 'updated_at';
  protected $deletedField  = 'deleted_at';

  protected $beforeInsert = ['generateUUID'];
  protected $beforeUpdate = ['beforeUpdate'];

  /**
   * Возвращает все активные аккаунты
   * @param int $uuid
   * @return array
   */
  public function findAccounts($uuid = 0)
  {
    $this
      ->select('accounts.*')
      ->select('CONCAT(networks.link, accounts.login) link')
      ->join('networks', 'networks.id = accounts.nid', 'LEFT')
      ->where('status', 1)
    ;

    if (!empty($uuid))
      $this->where('accounts.uuid', $uuid);

    $result = $this->findAll();

    // Добавление информации по количеству постов стоящих на модерации
    if (!empty($result)) {
      $postModel = new PostModel();
      foreach ($result as $key => $item) {
        $posts_in_mod = $postModel->getCountPosts(0, $item['uuid']);
        $posts = $postModel->getCountPosts(1, $item['uuid']);
        $posts_in_del = $postModel->getCountPosts(2, $item['uuid']);

        $result[$key]['tag_ids'] = explode(',', $item['tag_ids']);
        $result[$key]['posts_in_mod'] = $posts_in_mod;
        $result[$key]['posts'] = $posts;
        $result[$key]['posts_in_del'] = $posts_in_del;

        $result[$key]['posts_ok'] = !empty($posts) ? round(($posts / ($posts + $posts_in_del) * 100), 2) : 0;
      }
    }

    return !empty($result) ? $result : [];
  }

  /**
   * Обновить дату последнего парсинга страницы
   * @param $uuid
   */
  public function updateDateLastParse($uuid)
  {
    $this->set('last_parse', date('Y-m-d H:i:s'))->where('uuid', $uuid)->update();
  }

  /**
   * Устанавливает значение tag_ids
   * @param $uuid
   * @param $tag_id
   */
  public function setTagsAccount($uuid, $tag_id)
  {
    $result = $this->select('tag_ids')->where('uuid', $uuid)->first();
    $ids = !empty($result['tag_ids']) ? explode(',', $result['tag_ids']) : [];
    if (in_array($tag_id, $ids)) {
      unset($ids[array_search($tag_id, $ids)]);
    } else {
      $ids[] = $tag_id;
    }
    $this->set('tag_ids', implode(',', $ids))->where('uuid', $uuid)->update();
  }
}