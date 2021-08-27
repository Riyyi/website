<?php

/**
 * Check if array element exists
 *
 * @param array $array The array to check for element
 * @param string $key The element to find
 *
 * @return bool True if element eixsts
 */
function _exists(array $array = null, string $key = '0'): bool {
	return isset($array[$key]) && !empty($array[$key]);
}

/**
 * Cut off string after the found character
 *
 * @param string $string The input string
 * @param string $fromCharacter The string is cut after this character
 *
 * @return string The new cut string
 */
function _trim(string $string, string $fromCharacter): string {
	$position = strpos($string, $fromCharacter);
	$end = strlen($string);
	return substr($string, 0, $position !== false ? $position : $end);
}

/**
 * Generate a cryptographically secure random string
 *
 * @param int $length Length of the string
 * @param string $keyspace Possible characters the string can have
 *
 * @return string The generated string
 */
function _randomStr(int $length, string $keyspace =
	'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'): string {

	$pieces = [];
	$max = mb_strlen($keyspace, '8bit') - 1;

	for ($i = 0; $i < $length; $i++) {
		$pieces[] = $keyspace[random_int(0, $max)];
	}

	return implode('', $pieces);
}

/**
 * Print variable inside of a <pre> and exit
 *
 * @param mixed[] $output The variable (single/array) to print
 * @param bool $die Call die(); after printing
 *
 * @return void Nothing
 */
function _log($output, bool $die = true): void {
	echo '<pre>';
	var_dump($output);
	echo '</pre>';
	if ($die) {
		die();
	}
}

//-------------------------------------//

if (!function_exists('session')) {
	/**
	 * Get / set the specified session value.
	 * If key is an array, treat as a setter.
	 *
	 * @param  string|array  $key
	 * @param  mixed  $default
	 *
	 * @return mixed
	 */
	function session($key = null, $default = null)
	{
		$session = "\App\Classes\Session";

		if (is_null($key)) {
			return $session;
		}

		if (is_array($key)) {
			return $session::put($key);
		}

		return $session::get($key, $default);
	}
}
