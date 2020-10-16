<?php namespace App\Controllers;

class Home extends BaseController
{
	public function index()
	{
    //$this->profileModel->insert(['account' => 'account' . rand(1000,9999)]);
    $profiles = $this->profileModel->findAllActive();

	  $data = [
	    'profiles' => $profiles,
    ];

	  print_array($data);
		return $this->view('home/index', $data);
	}
}
