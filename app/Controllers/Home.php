<?php namespace App\Controllers;

class Home extends BaseController
{
  /**
   * @return string
   */
  public function index()
	{
	  $data = [
	    'posts' => $this->postModel->findAllActive(),
    ];

		return $this->view('home/index', $data);
	}
}
