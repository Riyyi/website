<?php

namespace App\Model;

use App\Traits\Log;

class SectionModel extends Model {

	use Log;

	protected $table = 'section';
	protected $sort = 'order';

	// Attribute rules
	// Name | Type | Required | Filtered
	public $rules = [
		["section",          "text",      1, 0],
		["title",            "text",      0, 0],
		["order",            "text",      1, 0],
		["hide_navigation",  "checkbox",  1, 0],
		["active",           "checkbox",  1, 0],
		["log_id",           "text",      1, 1],
	];
}
