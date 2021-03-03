<?php

namespace App\Model;

use App\Classes\Db;
use App\Model\ContentModel;
use App\Model\SectionModel;

class SectionHasContentModel extends Model {

	protected $table = 'section_has_content';
	protected $sort = ['section_id', 'order'];

	public $title = "SectionHasContent";

	// Attribute rules
	// Name | Type | Required | Filtered
	public $rules = [
		["order",       "text",      1, 0],
		["section_id",  "dropdown",  1, 0],
		["content_id",  "dropdown",  1, 0],
	];

	//-------------------------------------//

	// Generate the dropdown data
	public function getDropdownData(string $type): array
	{
		if ($type == 'section_id') {
			return $this->dropdownSection();
		}
		else if ($type == 'content_id') {
			return $this->dropdownContent();
		}

		return [];
	}

	//-------------------------------------//

	protected function dropdownSection(): array
	{
		$sections = SectionModel::selectAll(
			'*', "WHERE `active` = ? ORDER BY `title` ASC", [1], '?');

		return [0 => 'Select section'] + array_combine(
			array_column($sections, 'id'),
			array_column($sections, 'title')
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
