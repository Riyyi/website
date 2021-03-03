<?php

namespace App\Classes;

class Config {

    protected static $config;

	//-------------------------------------//

	public static function load(): void
	{
		if (file_exists('../config.php')) {
			self::$config = require_once '../config.php';
		}
    }

	//-------------------------------------//

	public static function c(string $index = ''): string
	{
		if (_exists(self::$config, $index)) {
			return self::$config[$index];
		}

		return '';
	}

}
