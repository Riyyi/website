<?php

namespace App\Model;

use App\Traits\Log;

class ContentModel extends Model {

	use Log { delete as deleteLog; }

	protected $table = 'content';
	protected $sort = 'title';

	// Attribute rules
	// Name | Type | Required | Filtered
	public $rules = [
		["content",         "textarea",  0, 0],
		["title",           "text",      0, 0],
		["type",            "dropdown",  1, 0],
		["hide_title",      "checkbox",  1, 0],
		["hide_background", "checkbox",  1, 0],
		["active",          "checkbox",  1, 0],
		["log_id",          "text",      1, 1],
	];

	//-------------------------------------//

	// Generate the dropdown data
	public function getDropdownData(string $type): array
	{
		if ($type == 'type') {
			return [0 => 'Select type', 1 => 'Page content', 2 => 'Side block'];
		}

		return [];
	}

	public function delete(): bool
	{
		if (self::query(
			"DELETE FROM `page_has_{$this->table}` WHERE `{$this->table}_$this->primaryKey` = :id", [
				[':id', $this->{$this->primaryKey}],
			]
		) === null) {
			return false;
		}

		return $this->deleteLog();
	}
}
