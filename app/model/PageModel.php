<?php

namespace App\Model;

use App\Classes\Db;
use App\Traits\Log;

class PageModel extends Model {

	use Log { delete as deleteLog; }

	protected $table = 'page';
	protected $sort = ['section_id', 'order'];

	// Attribute rules
	// Name | Type | Required | Filtered
	public $rules = [
		["page",             "text",      1, 0],
		["title",            "text",      0, 0],
		["title_url",        "text",      0, 1],
		["meta_description", "text",      0, 0],
		["type",             "text",      1, 1],
		["order",            "text",      1, 0],
		["hide_navigation",  "checkbox",  1, 0],
		["active",           "checkbox",  1, 0],
		["section_id",       "dropdown",  1, 0],
		["log_id",           "text",      1, 1],
	];

	//-------------------------------------//

	// Set default values
	public function __construct()
	{
		parent::__construct();

		$this->type = 0;
	}

	// Generate the dropdown data
	public function getDropdownData(string $type): array
	{
		if ($type == 'section_id') {
			return $this->dropdownSection();
		}

		return [];
	}

	public function delete(): bool
	{
		if (self::query(
			"DELETE FROM `{$this->table}_has_content` WHERE `{$this->table}_$this->primaryKey` = :id", [
				[':id', $this->{$this->primaryKey}],
			]
		) === null) {
			return false;
		}

		return $this->deleteLog();
	}

	//-------------------------------------//

	protected function dropdownSection(): array
	{
		// Pull sections from cache
		$sections = Db::getSections();

		return [0 => 'Select section'] + array_combine(
			array_column($sections, 'id'),
			array_column($sections, 'title')
		);
	}

}
