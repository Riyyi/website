<?php

namespace App\Controllers;

use App\Classes\Db;
use App\Model\Model;
use App\Model\ContentModel;
use App\Model\PageHasContentModel;
use App\Model\SectionHasContentModel;

class PageController extends BaseController {

	/**
	 * Path of the page view files.
	 *
	 * @var string
	 */
	protected $views = '../app/views/';

	//-------------------------------------//

	/**
	 * Create a new page controller instance.
	 *
	 * @param  Klein $router
	 *
	 * @return mixed
	 */
	public function __construct(\Klein\Klein $router = null)
	{
		parent::__construct($router);
	}

	//-------------------------------------//

	/**
	 * Handle page request with Db stored content.
	 *
	 * @param  int $id
	 *
	 * @return void
	 */
	public function routeAction(int $id): void
	{
		// Pull pages from cache
		$pages = Db::getPages();
		$page = array_search($id, array_column($pages, 'id'));
		$page = $pages[$page];

		$title           = $page['title'] ?? '';
		$metaDescription = $page['meta_description'] ?? '';

		// Load linked content
		$pageHasContent    = new PageHasContentModel;
		$sectionHasContent = new SectionHasContentModel;
		$contents = array_merge(
			(array)$this->loadLinkedContent($pageHasContent, 'page', $id),
			(array)$this->loadLinkedContent($sectionHasContent, 'section', $page['section_id']));

		// Exit if nothing was found
		if (!_exists($contents)) {
			parent::throw404();
		}

		$sideContent = in_array('2', array_column($contents, 'type'));

		$this->router->service()->contents = $contents;
		$this->router->service()->sideContent = $sideContent;
		$this->view('content', $title, $metaDescription);
	}

	//-------------------------------------//

	/**
	 * Load all content blocks linked to the provided Model.
	 *
	 * @param  Model  $model
	 * @param  string  $column
	 * @param  int  $id
	 *
	 * @return null|array
	 */
	protected function loadLinkedContent(Model $model, string $column, int $id): ?array
	{
		// Load all the Model <-> Content link data
		$hasContent = $model::selectAll('*', "
			WHERE {$column}_id = :id
			ORDER BY {$model->getSort()} ASC", [
				[':id', $id, \PDO::PARAM_INT],
			]
		);

		// Exit if nothing was found
		if (!_exists($hasContent)) {
			return null;
		}

		// Get all the content
		$contentIds = array_column($hasContent, 'content_id');
		$contents = ContentModel::findAll($contentIds);

		// Exit if nothing was found
		if (!_exists($contents)) {
			return null;
		}

		// Remove inactive content
		foreach ($contents as $key => $content) {
			if ($content['active'] == "0") {
				unset($contents[$key]);
			}
		}
		$contents = array_values($contents);

		return $contents;
	}

	/**
	 * Render page view with title and meta description.
	 *
	 * @param  string  $view
	 * @param  string  $pageTitle
	 * @param  string  $metaDescription
	 *
	 * @return void
	 */
	protected function view(
		string $view = '', string $pageTitle = '', string $metaDescription = ''): void
	{
		if ($view != '') {
			$view = $this->fileExists($this->views . $view . '.php');
		}

		if ($this->page == null) {
			if ($view == '' && $this->section == '') {
				// /
				$view = $this->fileExists($this->views . 'home.php');
			}

			if ($view == '') {
				// /example.php
				$view = $this->fileExists($this->views . $this->section . '.php');
			}

			if ($view == '') {
				// /example/index.php
				$view = $this->fileExists($this->views . $this->section . '/index.php');
			}
		}
		else if ($view == '') {
			// /example/my-page.php
			$view = $this->fileExists($this->views . $this->section  . '/' . $this->page  . '.php');
		}

		if ($view != '') {
			$pageTitle != ''
				? $this->router->service()->pageTitle = $pageTitle
				: $this->router->service()->pageTitle = ucfirst(str_replace('-', ' ', $this->page));
			$this->router->service()->metaDescription = $metaDescription;
			$this->router->service()->render($view);
		}
		else {
			parent::throw404();
		}
	}

	//-------------------------------------//

	/**
	 * Loop back filename if it exists, empty string otherwise.
	 *
	 * @param  string  $file
	 *
	 * @return string
	 */
	private function fileExists(string $file): string
	{
		return file_exists($file) ? $file : '';
	}

}

// @Todo
// - Fix line 32, breaks if no DB content!
// - Implement page.description (meta)
// - Use page.title instead of content.title (?)
