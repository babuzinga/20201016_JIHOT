<?php namespace App\Controllers;

class Home extends BaseController
{
	public function index()
	{
	  $data = [];
		return $this->view('home/index', $data);
	}
}
