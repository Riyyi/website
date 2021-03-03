<?php

namespace App\Controllers;

use App\Classes\Config;
use App\Classes\Media;

use App\Model\LogModel;
use App\Model\MediaModel;
use App\Model\UserModel;

class MediaController extends PageController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return void
	 */
	public function indexAction(): void
	{
		$this->mediaPage();
		parent::view();
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return void
	 */
	public function storeAction(): void
	{
		$name = ucfirst($this->page);

		$overwrite = _exists($_POST, 'overwrite');

		$error = Media::uploadMedia($overwrite);
		if (!$error) {
			$this->setAlert('success', "$name successfully created.");
		}
		else {
			$this->setAlert('danger', Media::errorMessage($error));
		}

		$this->mediaPage();
		parent::view();
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id
	 *
	 * @return void
	 */
	public function destroyAction(int $id): void
	{
		$name = ucfirst($this->page);

		if (Media::deleteMedia($id)) {
			$this->setAlertNext('success', "$name successfully deleted.");
		}
		else {
			$this->setAlertNext('danger', "$name could not be deleted!");
		}
	}

	//-------------------------------------//

	protected function mediaPage(): void {
		// ?page=x
		$page = 1;
		if (_exists($_GET, 'page') && is_numeric($_GET['page'])) {
			$page = $_GET['page'];
		}

		$mediaModel = new MediaModel;

		// Get all Media of the page
		$media = $mediaModel->all($page, Media::$pagination);

		// Get all the connected Logs
		$log = null;
		if (_exists($media)) {
			$logId = array_column($media, 'log_id');
			$log = LogModel::findAll($logId);
		}

		// Get all the connected Users
		$user = null;
		if (_exists($log)) {
			$uploaderId = array_column($log, 'user_id');
			$user = UserModel::findAll($uploaderId);
		}

		// Set empty values
		if (!_exists($media) || !_exists($log) || !_exists($user)) {
			$media = [];
			$log = [["user_id" => "0"]];
			$user = [["username" => "Could not load users.."]];
		}

		// Set view Media variables
		$this->router->service()->media = $media;
		$this->router->service()->log = $log;
		$this->router->service()->user = $user;

		// Set view Page variables
		$this->router->service()->page = $page;
		$pages = ceil($mediaModel->count() / Media::$pagination);
		$pages > 1
			? $this->router->service()->pages = $pages
			: $this->router->service()->pages = 1;

		$this->router->service()->fileUrl = Config::c('APP_URL') . '/' . Media::$directory . '/';
	}

}
