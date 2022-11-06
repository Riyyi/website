<?php

namespace App\Classes;

use Klein\Klein;

use App\Classes\Db;

use App\Model\SectionModel;
use App\Model\PageModel;

class Router {

	protected static $router;
	protected static $routes = [];

	public static function _init(): void {
		self::$router = new Klein();
	}

	//-------------------------------------//

	/**
	 * Load all routes into the Klein object
	 *
	 * @return void
	 */
	public static function fire(): void
	{
		$path = parse_url($_SERVER['REQUEST_URI'])['path'];
		$check = str_replace('.', '', $path);

		// If it's a dynamic file or the file doesn't exist, go through the router.
		if ($path == $check || !file_exists(getcwd() . $path)) {

			Db::load();
			self::setDefaultLayout();
			self::loadConfigRoutes();
			self::loadDbRoutes();

			// Process basic routes
			foreach (self::$routes as $route) {
				// ["/example/my-page", "ExampleController", "action", "" : ["view", "title", "description"]],
				self::addRoute(['GET', 'POST'], $route);
			}

			self::createNavigation();
			self::setHttpError();

			self::$router->dispatch();
		}
	}

	/**
	 * Add CRUD routes
	 * Example usage: Router::resource('/example', 'CrudController');
	 *
	 * @param string $route The URL location
	 * @param string $controller Controller to handle this route
	 *
	 * @return void
	 */
	public static function resource(string $route, string $controller): void
	{
		/*
		 * HTTP Verb    Part (URL)          Action (Method)
		 *
		 * GET          /route              indexAction
		 * GET          /route/create       createAction
		 * POST         /route              storeAction
		 * GET          /route/{id}         showAction
		 * GET          /route/{id}/edit    editAction
		 * PUT/PATCH    /route/{id}         updateAction
		 * DELETE       /route/{id}         destroyAction
		 */

		self::addRoute(['GET'], [$route, $controller, 'index']);
		self::addRoute(['GET'], [$route . '/create', $controller, 'create']);
		self::addRoute(['POST'], [$route, $controller, 'store']);
		self::addRoute(['GET'], [$route . '/[i:id]', $controller, 'show']);
		self::addRoute(['GET'], [$route . '/[i:id]/edit', $controller, 'edit']);
		self::addRoute(['PUT', 'PATCH'], [$route . '/[i:id]', $controller, 'update']);
		self::addRoute(['DELETE'], [$route . '/[i:id]', $controller, 'destroy']);
	}

	//-------------------------------------//

	protected static function setDefaultLayout(): void
	{
		self::$router->respond(function ($request, $response, $service) {
			$service->layout('../app/views/layouts/default.php');
		});
	}

	protected static function loadConfigRoutes(): void
	{
		if (file_exists('../route.php')) {
			self::$routes = require_once '../route.php';
		}
	}

	/**
	 * Add all pages in the Db to self::$routes
	 *
	 * @return void
	 */
	protected static function loadDbRoutes(): void
	{
		// Load all sections from Db
		$sections = SectionModel::selectAll('*', 'WHERE `active` = 1 ORDER BY `order` ASC');

		// Return if no sections
		if (!_exists($sections)) {
			return;
		}

		// Load all pages from Db
		$pages = PageModel::selectAll('DISTINCT page.*', '
			LEFT JOIN page_has_content ON page_has_content.page_id = page.id
			LEFT JOIN content ON content.id = page_has_content.content_id
			WHERE page.active = 1 AND content.active = 1
			ORDER BY page.order ASC;
		');

		// Return if no pages
		if (!_exists($pages)) {
			return;
		}

		// Select id column
		$section = array_column($sections, 'section', 'id');

		// Loop through all pages
		foreach ($pages as $pageKey => $page) {
			// Skip if section isn't created / active
			if (!_exists($section, $page['section_id'])) { continue; }

			// url = /section/page
			$url = '/' . $section[$page['section_id']] . '/' . $page['page'];

			// Add route
			self::$routes[] = [$url, 'PageController', 'route', [$page['id']]];
		}

		// Cache sections and pages
		Db::setSections($sections);
		Db::setPages($pages);
	}

	protected static function addRoute(array $method = [], array $data = []): void
	{
		if (!_exists($method) || !_exists($data) || count($data) < 2) {
			return;
		}

		$route      = $data[0] ?? null;
		$controller = $data[1] ?? null;
		$action     = $data[2] ?? null;
		$parameters = $data[3] ?? [];

		if (!$route || !$controller || !is_array($parameters)) {
			return;
		}

		// Create Klein route
		self::$router->respond($method, $route, function($request, $response, $service) use($controller, $action, $parameters) {

			// Create new Controller object
			$controller = '\App\Controllers\\' . $controller;
			$controller = new $controller(self::$router);

			// Complete action variable
			if (!$action) {
				$action = 'indexAction';
			}
			else {
				$action .= 'Action';
			}

			// If method does not exist in object
			if (!method_exists($controller, $action)) {
				return $controller->throw404();
			}

			// If no valid permissions
			if ($controller->getAdminSection() && !$controller->getLoggedIn()) {
				return $controller->throw404();
			}

			// Get URL parameters
			$namedParameters = array_filter($request->paramsNamed()->all(), 'is_string', ARRAY_FILTER_USE_KEY);
			$namedParameters = array_values($namedParameters);

			// Append URL parameters to the hard-coded parameters
			array_push($parameters, ...$namedParameters);

			// Call Controller action
			return $controller->{$action}(...$parameters);
		});
	}

	public static function createNavigation(): void
	{
		// Pull from cache
		$sections = Db::getSections();
		$pages = Db::getPages();

		// [
		// 	[
		// 		'section url',
		// 		'title',
		// 		['page url', 'title'],
		// 		['page url', 'titleOfPage2'],
		// 	],
		// 	[],
		// 	[],
		// ]

		$navigation = [];

		// Generate sections
		foreach ($sections as $section) {
			// Skip hidden sections
			if ($section['hide_navigation'] == '1') {
				continue;
			}

			// Add URL, title to ID
			$navigation[$section['id']] = [
				$section['section'], $section['title']
			];
		}

		// Generate pages
		foreach ($pages as $page) {
			// Skip hidden sections
			if (!_exists($navigation, $page['section_id'])) {
				continue;
			}

			// Skip hidden pages
			if ($page['hide_navigation'] == '1') {
				continue;
			}

			// Add [URL, title] to ID
			$navigation[$page['section_id']][] = [
				$page['page'], $page['title']
			];
		}

		self::$router->service()->navigation = $navigation;
	}

	//-------------------------------------//

	protected static function setHttpError(): void
	{
		self::$router->onHttpError(function($code) {
			$service = self::$router->service();

			switch ($code) {
				case 404:

					// @Todo: find out why this was needed and remove if possible
					$service->escape = function (?string $string) {
						return htmlentities($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
					};

					self::$router->response()->sendHeaders(true, true);
					$service->pageTitle = 'Error 404 (Not Found)';
					$service->render('../app/views/errors/404.php');
					break;
			}
		});
	}

	//-------------------------------------//

	public static function getRoutes(): array
	{
		return self::$routes;
	}

}

Router::_init();
