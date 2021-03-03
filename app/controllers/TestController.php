<?php

namespace App\Controllers;

use App\Classes\Config;
use App\Classes\Db;
use App\Classes\User;
use App\Classes\Mail;
use App\Classes\Media;
use App\Classes\Session;

use App\Model\MediaModel;
use App\Model\Model;
use App\Model\SectionModel;
use App\Model\PageModel;
use App\Model\UserModel;
use App\Model\LogModel;

class TestController extends PageController {

	public function indexAction(): void {
		parent::throw404();
	}

}
