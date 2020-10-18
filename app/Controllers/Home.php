<?php namespace App\Controllers;

class Home extends BaseController
{
  protected $page_url = '/';

  /**
   * @param int $uuid_account
   * @return string
   */
  public function index($uuid_account = 0)
	{
	  $page = !empty($_GET['p']) ? $this->request->getGet('p') : 1;
    $offset = $this->limit * ($page - 1);

	  $data = [
	    'posts' => $this->postModel->findPostActive($uuid_account, $this->limit, $offset),
      'page' => $page,
      'url' => $this->page_url . '?p=' . ($page + 1),
      'ajax' => !empty($_GET['ajax']),
    ];

	  $template = empty($_GET['ajax']) ? 'home/index' : 'home/items';
		return $this->view($template, $data);
	}

	public function PostsAccount($uuid_account)
  {
    $this->page_url = "/posts/account/{$uuid_account}/";
    return self::index($uuid_account);
  }
}
