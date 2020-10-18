<?php namespace App\Controllers;

class Manage extends BaseController
{
  /**
   * Страница управления
   * @return string
   */
  public function index()
  {
    $data = [
      'accounts' => $this->accountModel->findAllActive(),
    ];

    return $this->view('manage/index', $data);
  }

  /**
   * Добавление провился из соц.сети
   * @return string
   */
  public function AddAccount()
  {
    $data = [
      'networks' => $this->baseModel->getNetworks(),
    ];

    return $this->view('manage/add-account', $data);
  }

  /**
   * Сохранение аккаунта
   * @return \CodeIgniter\HTTP\RedirectResponse
   */
  public function SaveAccount()
  {
    if (!empty($_POST)) {
      $data = [
        'login' => $this->request->getPost('login'),
        'nid' => $this->request->getPost('nid'),
      ];

      $this->accountModel->insert($data);
    }

    return redirect()->to('/manage/add-account');
  }

  /**
   * Получение постов по всем акканутам
   * @return string
   */
  public function GetPosts()
  {
    $accounts = $this->accountModel->findAllActive();

    if (!empty($accounts)) {
      ini_set('max_execution_time', 900);

      foreach ($accounts as $id => $account) {
        $accounts[$id]['new'] = 0;
        $posts = $this->baseModel->getPostsAccount($account);

        if (!empty($posts)) {
          foreach ($posts as $post) {
            // Если контент с таким идентификатором уже числится в БД, пропускаем его
            if ($this->postModel->isStockPost($post['pid'], $account['nid'])) continue;

            $data = [
              'uuid' => gen_uuid(),
              'uuid_account' => $account['uuid'],
              'pid' => $post['pid'],
              'nid' => $account['nid'],
              'timestamp' => $post['timestamp'],
              'shortcode' => $post['shortcode'],
              'desc' => $post['desc'],
              'comments' => $post['comments'],
              'likes' => $post['likes'],
              'media_count' => count($post['medias']),
            ];

            $this->postModel->insert($data);
            $accounts[$id]['new']++;

            foreach ($post['medias'] as $item) {
              $item['uuid'] = gen_uuid();
              $item['uuid_post'] = $data['uuid'];
              $this->postModel->insertMediaTemp($item);
            }
          }
        }

        sleep(1);
      }
    }

    $data = [
      'accounts' => $accounts,
    ];

    return $this->view('manage/get-posts', $data);
  }

  /**
   * Вывод постов на модерацию
   * @return string
   */
  public function SelectPosts()
  {
    $posts = $this->postModel->findPostModeration();

    $data = [
      'posts' => $posts,
    ];

    return $this->view('manage/select-posts', $data);
  }

  /**
   * Удаление временного медиа-файла
   * @param $uuid
   */
  public function RemoveTempMedia($uuid)
  {
    if (empty($uuid)) ajax(0);
    $this->postModel->deleteMediaTemp($uuid);
    ajax(1);
  }

  /**
   * Удаление поста
   * @param $uuid
   */
  public function RemovePost($uuid)
  {
    if (empty($uuid)) ajax(0);
    $this->postModel->deletePost($uuid);
    ajax(1);
  }

  /**
   * Загрузить пост на сайт со всеми имеющимися медиа-данными
   * @param $uuid
   */
  public function UploadPost($uuid)
  {
    if (empty($uuid)) ajax(0);
    $this->postModel->uploadPost($uuid);
    ajax(1);
  }
}