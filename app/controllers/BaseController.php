<?php

namespace App\Controllers;

use App\Classes\Config;
use App\Classes\Db;
use App\Classes\Session;
use App\Classes\User;

class BaseController {

	protected $router;
	protected $section;
	protected $page;
	protected $loggedIn;
	protected $url;
	protected $adminSection;

	//-------------------------------------//

	public function __construct(\Klein\Klein $router = null)
	{
		$this->router = $router;

		$request = $this->router->request()->uri();
		$request = parse_url($request)['path'];
		$request = explode("/", $request);

		if (array_key_exists(1, $request)) {
			$this->section = $request[1];
		}
		if (array_key_exists(2, $request)) {
			$this->page = $request[2];
		}

		// Set login status
		$this->loggedIn = User::check();
		$this->router->service()->loggedIn = $this->loggedIn;

		// Set url https://site.com/section/page
		$this->url = Config::c('APP_URL');
		$this->url .= _exists([$this->section]) ? '/' . $this->section : '';
		$this->url .= _exists([$this->page]) ? '/' . $this->page : '';
		$this->router->service()->url = $this->url;

		// If Admin section
		$this->adminSection = $this->section == 'admin';
		$this->router->service()->adminSection = $this->adminSection;

		// Clear alert
		$this->setAlert('', '');
		// Load alert set on the previous page
		$this->loadAlert();

		// View helper method
		$this->router->service()->escape = function (?string $string) {
			return htmlentities($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
		};
	}

	//-------------------------------------//

	public function throw404(): void
	{
		$this->router->response()->sendHeaders(true, true);
		$service = $this->router->service();
		$service->pageTitle = 'Error 404 (Not Found)';
		$service->render('../app/views/errors/404.php');
		exit();
	}

	/**
	 * Set alert for the current page
	 *
	 * @param string $type Color of the message (success/danger/warning/info)
	 * @param string $message The message to display
	 *
	 * @return void
	 */
	public function setAlert(string $type, string $message): void
	{
		$this->router->service()->type = $type;
		$this->router->service()->message = $message;
	}

	/**
	 * Set alert for the next page
	 *
	 * @param string $type Color of the message (success/danger/warning/info)
	 * @param string $message The message to display
	 *
	 * @return void
	 */
	public function setAlertNext(string $type, string $message): void
	{
		Session::put('type', $type);
		Session::put('message', $message);
	}

	/**
	 * Load alert set on the previous page
	 *
	 * @return void
	 */
	public function loadAlert(): void
	{
		if (Session::exists('type') && Session::exists('message')) {
			$this->setAlert(Session::get('type'), Session::get('message'));
			Session::delete(['type', 'message']);
		}
	}

	//-------------------------------------//

	public function getSection(): string
	{
		return $this->section;
	}

	public function getLoggedIn(): bool
	{
		return $this->loggedIn;
	}

	public function getAdminSection(): bool
	{
		return $this->adminSection;
	}

}

// @Todo
// - Image lazy loading
