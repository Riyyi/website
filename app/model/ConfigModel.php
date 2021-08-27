<?php

namespace App\Model;

use App\Traits\Log;

class ConfigModel extends Model {

	use Log;

	protected $table = 'config';
	protected $sort = 'key';

	public $title = "Config";

	// Attribute rules
	// Name | Type | Required | Filtered
	public $rules = [
		["key",              "text",      1, 0],
		["value",            "text",      0, 0],
		["log_id",           "text",      1, 1],
	];
}
