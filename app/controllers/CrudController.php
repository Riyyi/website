<?php

namespace App\Controllers;

use App\Classes\Session;

class CrudController extends PageController {

	public static $pagination = 10;

	//-------------------------------------//

	/**
	 * Display a listing of the resource.
	 *
	 * @return void
	 */
	public function indexAction(): void
	{
		$modelName = $this->getModelName();
		$model = "\App\Model\\{$modelName}Model";
		$model = new $model;

		// ?page=x
		$page = 1;
		if (_exists($_GET, 'page') && is_numeric($_GET['page'])) {
			$page = $_GET['page'];
		}

		$rows = $model->all($page, self::$pagination);

		// Set empty value
		if (!_exists($rows)) {
			$rows = [];
		}

		$this->router->service()->attributes = $model->getAttributesRules();
		$this->router->service()->csrfToken = Session::token();
		$this->router->service()->rows = $rows;
		$this->router->service()->title = $modelName;

		// Set page variables
		$this->router->service()->page = $page;
		$pages = ceil($model->count() / self::$pagination);
		$pages > 1
			? $this->router->service()->pages = $pages
			: $this->router->service()->pages = 1;

		parent::view("{$this->section}/crud/index");
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return void
	 */
	public function createAction(): void
	{
		$model = "\App\Model\\{$this->getModelName()}Model";
		$model = new $model;

		$attributes = $model->getAttributesRules();

		// Get dropdown data
		$dropdownData = [];
		foreach ($attributes as $key => $attribute) {
			if ($attribute[1] == 'dropdown') {
				$dropdownData[$key] = $model->getDropdownData($attribute[0]);
			}
		}

		$this->router->service()->attributes = $attributes;
		$this->router->service()->csrfToken = Session::token();
		$this->router->service()->dropdownData = $dropdownData;
		parent::view("{$this->section}/crud/create");
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return void
	 */
	public function storeAction(): void
	{
		$modelName = $this->getModelName();
		$model = "\App\Model\\{$modelName}Model";
		$model = new $model;

		$token = Session::validateToken($_POST);

		$token && $model->fill($_POST) && $model->save()
			? $this->setAlertNext('success', "$modelName successfully created.")
			: $this->setAlertNext('danger', "$modelName could not be created!");

		$this->router->response()->redirect($this->url);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 *
	 * @return void
	 */
	public function showAction(int $id): void
	{
		$model = "\App\Model\\{$this->getModelName()}Model";
		$model = $model::find($id);

		if (!$model->exists()) {
			parent::throw404();
		}

		$this->router->service()->model = $model;
		$this->router->service()->attributes = $model->getAttributesRules();
		parent::view("{$this->section}/crud/show");
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id
	 *
	 * @return void
	 */
	public function editAction(int $id): void
	{
		$model = "\App\Model\\{$this->getModelName()}Model";
		$model = $model::find($id);

		if (!$model->exists()) {
			parent::throw404();
		}

		$attributes = $model->getAttributesRules();

		// Get dropdown data
		$dropdownData = [];
		foreach ($attributes as $key => $attribute) {
			if ($attribute[1] == 'dropdown') {
				$dropdownData[$key] = $model->getDropdownData($attribute[0]);
			}
		}

		$this->router->service()->attributes = $attributes;
		$this->router->service()->csrfToken = Session::token();
		$this->router->service()->dropdownData = $dropdownData;
		$this->router->service()->model = $model;
		parent::view("{$this->section}/crud/edit");
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param int $id
	 *
	 * @return void
	 */
	public function updateAction(int $id): void
	{
		$modelName = $this->getModelName();
		$model = "\App\Model\\{$modelName}Model";
		$model = $model::find($id);

		if (!$model->exists()) {
			$this->setAlertNext('danger', "$modelName does not exist!");
		}
		else {
			// Read PUT request
			$this->parsePhpInput($_PUT);

			$token = Session::validateToken($_PUT);

			$token && $model->fill($_PUT) && $model->save()
				? $this->setAlertNext('success', "$modelName successfully updated.")
				: $this->setAlertNext('danger', "$modelName could not be updated!");
		}

		echo $this->url;
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
		// Read DELETE request
		$this->parsePhpInput($_DELETE);

		$token = Session::validateToken($_DELETE);

		$modelName = $this->getModelName();
		$model = "\App\Model\\{$modelName}Model";

		$token && $model::destroy($id)
			? $this->setAlertNext('success', "$modelName successfully deleted.")
			: $this->setAlertNext('danger', "$modelName could not be deleted!");
	}

	//-------------------------------------//

	/**
	 * Generate the model name from the /section/page url
	 *
	 * @return string The model name
	 */
	private function getModelName(): string
	{
		$model = $this->page;
		$model = str_replace('-', ' ', $model);
		$model = str_replace('_', ' ', $model);
		$model = ucwords($model);
		$model = str_replace(' ', '', $model);

		return $model;
	}

	/**
	 * PUT/DELETE requests aren't handled by PHP automatically yet.
	 * Parse them from php://input instead.
	 *
	 * @param ?array &$result Array filled with input data
	 *
	 * @return void
	 */
	private function parsePhpInput(?array &$result): void
	{
		// Parse json or form-encoded formatted data
		if (strpos($_SERVER['CONTENT_TYPE'], "application/json") !== false) {
			$result = json_decode(file_get_contents("php://input"), true);
		}
		else if (strpos($_SERVER['CONTENT_TYPE'], "application/x-www-form-urlencoded") !== false) {
			parse_str(file_get_contents("php://input"), $result);

			// Cleanup &amp; HTML entities
			foreach ($result as $key => $value) {
				unset($result[$key]);
				$result[str_replace('amp;', '', $key)] = $value;
			}
		}

		$_REQUEST = array_merge($_REQUEST, $result);
	}

}
