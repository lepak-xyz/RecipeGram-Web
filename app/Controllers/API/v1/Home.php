<?php

namespace App\Controllers\API\V1;

use App\Controllers\BaseController;

class Home extends BaseController
{
	public function index()
	{
		//
        return view('api_v1/welcome');
	}
}
