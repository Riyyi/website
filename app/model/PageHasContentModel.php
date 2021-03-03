<?php

namespace App\Model;

use App\Classes\Db;
use App\Model\ContentModel;
use App\Model\PageModel;

class PageHasContentModel extends Model {

	protected $table = 'page_has_content';
	protected $sort = ['page_id', 'order'];

	public $title = "PageHasContent";

	// Attribute rules
	// Name | Type | Required | Filtered
	public $rules = [
		["order",       "text",      1, 0],
		["page_id",     "dropdown",  1, 0],
		["content_id",  "dropdown",  1, 0],
	];

	//-------------------------------------//

	// Generate the dropdown data
	public function getDropdownData(string $type): array
	{
		if ($type == 'page_id') {
			return $this->dropdownPage();
		}
		else if ($type == 'content_id') {
			return $this->dropdownContent();
		}

		return [];
	}

	//-------------------------------------//

	protected function dropdownPage(): array
	{
		$pages = PageModel::selectAll(
			'*', "WHERE `active` = ? ORDER BY `title` ASC", [1], '?');

		return [0 => 'Select page'] + array_combine(
			array_column($pages, 'id'),
			array_column($pages, 'title')
		);
	}

	protected function dropdownContent(): array
	{
		$contents = ContentModel::selectAll(
			'*', "WHERE `active` = ? ORDER BY `title` ASC", [1], '?');

		// Exit if nothing was found
		if (!_exists($contents)) {
			return [];
		}

		return [0 => 'Select content'] + array_combine(
			array_column($contents, 'id'),
			array_column($contents, 'title')
		);
	}

}
