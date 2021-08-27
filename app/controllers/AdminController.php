<?php

namespace App\Controllers;

use App\Classes\User;

class AdminController extends PageController {

	public function indexAction(): void {
		$this->router->service()->user = User::getUser();

		parent::view('', 'Admin');
	}

	public function toggleAction(): void {
		User::toggle();
		echo User::getToggle() ? '1' : '0';
	}

	public function syntaxAction(): void {
		parent::view();
	}

}
