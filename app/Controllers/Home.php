<?php namespace App\Controllers;

class Home extends BaseController
{
	public function index()
	{
	  $data = [
	    'page_title' => 'Main',
    ];
		return $this->view('home/index', $data);
	}
}
