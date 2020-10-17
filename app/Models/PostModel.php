<?php namespace App\Models;

use \App\Models\BaseModel;

class PostModel extends BaseModel
{
  protected $table      = 'posts';
  protected $primaryKey = 'uuid';

  protected $returnType     = 'array';
  protected $useSoftDeletes = true;

  protected $allowedFields = ['uuid', 'uuid_account', 'pid', 'nid', 'timestamp', 'shortcode', 'desc', 'comments', 'likes', 'media_count', 'status'];

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
   * @return array
   */
  public function isStockPost($pid, $nid)
  {
    if (empty($pid) || empty($nid)) return false;

    $result = $this->db->table($this->table)
      ->where('pid', $pid)
      ->where('nid', $nid)
      ->get()
      ->getResultArray()
    ;

    return !empty($result) ? $result : [];
  }

  /**
   * Временная запись о медиа-данных
   * @param $data
   */
  public function insertMediaTemp($data)
  {
    if (!empty($data))
      $this->db->table('medias_tmp')->insert($data);
  }

  /**
   * Запись о медиа-данных
   * @param $data
   */
  public function insertMedia($data)
  {
    if (!empty($data))
      $this->db->table('medias')->insert($data);
  }

  /**
   * Удаление временной записи о медиа
   * @param $uuid
   */
  public function deleteMediaTemp($uuid)
  {
    if (!empty($uuid))
      $this->db->table('medias_tmp')->delete("uuid = '{$uuid}'");
  }

  /**
   * Удаление всех временных медиа файлов относящихся в посту
   * @param $uuid_post
   */
  public function deleteMediasTemp($uuid_post)
  {
    if (!empty($uuid_post)) {
      $this->db->table('medias_tmp')->delete("uuid_post = '{$uuid_post}'");
      $this->updateMediaCount($uuid_post);
    }
  }

  /**
   * Удаление поста и всех медиа которые с ним связаны
   * @param $uuid
   * @return bool
   */
  public function deletePost($uuid)
  {
    if (empty($uuid)) return false;

    $this->set('status', 2)->where('uuid', $uuid)->update();
    $this->where('uuid', $uuid)->delete();
    $this->deleteMediasTemp($uuid);
  }

  /**
   * Получение всех временных медиа файлов
   * @param $uuid_post
   * @return array
   */
  public function getMediaTempByPost($uuid_post)
  {
    if (!empty($uuid_post))
      $result = $this->db->table('medias_tmp')->where('uuid_post', $uuid_post)->get()->getResultArray();

    return !empty($result) ? $result : [];
  }

  /**
   * Получение всех медиа файлов
   * @param $uuid_post
   * @return array
   */
  public function getMediaByPost($uuid_post)
  {
    if (!empty($uuid_post))
      $result = $this->db->table('medias')->where('uuid_post', $uuid_post)->get()->getResultArray();

    return !empty($result) ? $result : [];
  }

  /**
   * Обновление количества медиа файлов поста
   * @param $uuid_post
   */
  public function updateMediaCount($uuid_post)
  {
    $media = $this->getMediaByPost($uuid_post);
    $media_tmp = $this->getMediaTempByPost($uuid_post);
    $media_count = count($media) + count($media_tmp);

    $this->set('media_count', $media_count)->where('uuid', $uuid_post)->update();
  }

  /**
   * Загрузка всех временных файлов для поста
   * @param $uuid_post
   * @return bool
   */
  public function uploadMedias($uuid_post)
  {
    $medias_temp = $this->getMediaTempByPost($uuid_post);
    if (!empty($medias_temp)) {
      foreach ($medias_temp as $temp) {
        $name = $temp['uuid'] . ($temp['type'] == 1 ? '.jpg' : '.mp4');
        uploadMediaByUrl($name, $temp['url']);
        // Для видео файлов загружаются превью роликов
        if ($temp['type'] == 2) uploadMediaByUrl($temp['uuid'] . '_p.jpg', $temp['preview']);

        unset($temp['url']);
        unset($temp['preview']);

        // Запись в постоянную таблицу, удаление из временой
        $this->insertMedia($temp);
        $this->deleteMediaTemp($temp['uuid']);
      }
    }

    return true;
  }

  /**
   * Возвращает все посты на модерации
   * @return array
   */
  public function findAllModeration()
  {
    $result = $this->findByStatus(0);
    return !empty($result) ? $result : [];
  }

  /**
   * Возвращает все посты на модерации
   * @return array
   */
  public function findAllActive()
  {
    $result = $this->findByStatus(1);
    return !empty($result) ? $result : [];
  }

  /**
   * Получение всех постов по значению статуса
   * @param int $status
   * @return array
   */
  function findByStatus($status = 1)
  {
    $result = $this
      ->select('posts.*')
      ->select('accounts.login')
      ->select('networks.name AS social, networks.link')
      ->join('accounts', 'accounts.uuid = posts.uuid_account', 'LEFT')
      ->join('networks', 'networks.id = accounts.nid', 'LEFT')
      ->where('posts.status', $status)
      ->orderBy('timestamp DESC')
      ->findAll()
    ;

    if (!empty($result)) {
      foreach ($result as $key => $item) {
        $result[$key]['medias_temp'] = $this->getMediaTempByPost($item['uuid']);
        $result[$key]['medias'] = $this->getMediaByPost($item['uuid']);
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
    if (!empty($uuid))
      $this->where('uuid_account', $uuid);

    $result = $this->countAllResults();

    return !empty($result) ? $result : 0;
  }

  /**
   * Загрузка всех медиа данных к посту
   * @param $uuid
   * @return bool
   */
  public function uploadPost($uuid)
  {
    if (empty($uuid)) return false;

    $this->set('status', 1)->where('uuid', $uuid)->update();
    $this->uploadMedias($uuid);
    $this->deleteMediasTemp($uuid);
  }
}