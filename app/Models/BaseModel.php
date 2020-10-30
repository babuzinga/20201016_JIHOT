<?php namespace App\Models;

use CodeIgniter\Model;

class BaseModel extends Model
{
  /**
   * Возвращает объекта по uuid
   * @param $uuid
   * @return array|bool|object
   */
  public function getByUUID($uuid)
  {
    if (empty($uuid)) return false;
    $object = $this->where('uuid', $uuid)->first();

    return !empty($object) ? $object : false;
  }

  /**
   * @param array $data
   * @return array
   */
  protected function beforeUpdate(array $data)
  {
    $data['data']['updated_at'] = date('Y-m-d H:i:s');
    return $data;
  }

  /**
   * @param array $data
   * @return array
   */
  protected function generateUUID(array $data)
  {
    $data['data']['uuid'] = empty($data['data']['uuid']) ? gen_uuid() : $data['data']['uuid'];
    return $data;
  }

  /**
   * Возвращает список всех социальных сетей
   * @return array|array[]|object[]
   */
  public function getNetworks()
  {
    $networks = $this->db->query('SELECT id, name, link FROM networks')->getResult('array');
    return !empty($networks) ? $networks : [];
  }

  /**
   * Возвращает все теги пользоватея
   * @return array|array[]|object[]
   */
  public function getTagsAccount()
  {
    $tags = $this->db->query('SELECT id, title FROM tags_account')->getResult('array');
    return !empty($tags) ? $tags : [];
  }

  /**
   * Парсер страниц - получение данных
   * @param $profile
   * @return bool|mixed
   */
  public function getDataAccount($profile)
  {
    $result = false;
    if (empty($profile)) return $result;

    switch ($profile['nid']) {
      // Instagram
      case 1:
        $link = $profile['link'] . '?__a=1';
        $responce = @file_get_contents($link);
        $result = (!empty($responce)) ? json_decode($responce, 1) : $result;
        break;
    }

    return $result;
  }

  /**
   * Получения массива постов пользователя
   * @param $profile
   * @return bool
   */
  public function getPostsAccount($profile)
  {
    $result = false;
    if (empty($profile)) return $result;

    switch ($profile['nid']) {
      // Instagram
      case 1:
        $data = $this->getDataAccount($profile);

        if ($data && $posts = $data['graphql']['user']['edge_owner_to_timeline_media']['edges']) {
          // Перебираются все данные по медиа
          foreach ($posts as $post) {
            // В массив данных заносятся основные значения
            $data = $post['node'];

            try {
              // Если набор медиа отстутсвует (бывает) пропускаем этот пост
              $edges = $data['edge_media_to_caption']['edges'];
              if (empty($edges)) continue;

              $media = [
                'pid' => $data['id'],
                'type' => $data['__typename'],
                'shortcode' => $data['shortcode'],
                'timestamp' => $data['taken_at_timestamp'],
                'desc' => $edges[0]['node']['text'],
                'comments' => $data['edge_media_to_comment']['count'],
                'likes' => $data['edge_liked_by']['count'],

                'medias' => [],
              ];
            } catch (\Exception $e) {
              echo '<h1>ОШИБКА ПАРСЕРА!!!</h1>';
              print_array($data, 1);
            }

            // Сохранение медио в зависимости от типа
            switch ($media['type']) {
              // Видело
              case 'GraphVideo':
                $media['medias'][] = [
                  'type' => 2,
                  'url' => $data['video_url'],
                  'preview' => $data['display_url'],
                  'video_view_count' => $data['video_view_count'],
                ];
                break;

              // Одно изображений
              case 'GraphImage':
                $media['medias'][] = [
                  'type' => 1,
                  'url' => $data['display_url'],
                  'preview' => $data['thumbnail_src'],
                ];
                break;

              // Галерея
              case 'GraphSidecar':
                $sidebar = $data['edge_sidecar_to_children']['edges'];

                foreach ($sidebar as $item) {
                  $m = $item['node'];

                  if ($m['__typename'] == 'GraphVideo') {
                    $media['medias'][] = [
                      'type' => 2,
                      'url' => $m['video_url'],
                      'preview' => $m['display_url'],
                      'video_view_count' => $m['video_view_count'],
                    ];
                  } elseif ($m['__typename'] == 'GraphImage') {
                    $media['medias'][] = [
                      'type' => 1,
                      'url' => $m['display_url'],
                      'preview' => $m['display_url'], // media_preview
                    ];
                  }
                }
                break;
            }

            $result[] = $media;
          }
        }
        break;
    }

    return $result;
  }

  /**
   * Возвращает данные по тегам
   * @param $ids
   * @return array
   */
  public function getTagsName($ids)
  {
    $result = [];
    if (!empty($ids)) {
      $data = $this->db->table('tags_account')->whereIn('id', explode(',', $ids))->get()->getResultArray();
      $result = !empty($data) ? $data : $result;
    }

    return $result;
  }
}