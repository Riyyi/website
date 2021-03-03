<?php

namespace App\Classes;

use stdClass;

class Session {

	/**
	 * The session attributes.
	 *
	 * @var array
	 */
	private static $attributes;

	/**
	 * Session store started status.
	 *
	 * @var bool
	 */
	protected static $started = false;

	//-------------------------------------//

	/**
	 * Start the PHP session
	 *
	 * @return void
	 */
	public static function start(): void
	{
		if (self::$started) {
			return;
		}

		session_set_cookie_params(['secure' => true, 'httponly' => true, 'samesite' => 'Strict']);
		session_start();

		self::$attributes = &$_SESSION;

        if (!self::exists('_token')) {
            self::regenerateToken();
        }

		self::$started = true;
	}

	//-------------------------------------//

	/**
	 * Get all of the session data.
	 *
	 * @return array
	 */
	public static function all(): array
	{
		return self::$attributes;
	}

	/**
	 * Checks if a key exists.
	 *
	 * @param  string|array  $key
	 *
	 * @return bool
	 */
	public static function isset($key): bool
	{
		$placeholder = new stdClass;
		return self::get($key, $placeholder) !== $placeholder;
	}

	/**
	 * Check if a key is present and not null.
	 *
	 * @param  string|array  $key
	 *
	 * @return bool
	 */
	public static function exists($key): bool
	{
		return !is_null(self::get($key));
	}

	/**
	 * Get an item from the session.
	 *
	 * @param  string  $key
	 * @param  mixed  $default
	 *
	 * @return mixed
	 */
	public static function get(string $key, $default = null)
	{
		if (array_key_exists($key, self::$attributes)) {
			return self::$attributes[$key];
		}

		if (strpos($key, '.') === false) {
			return $default;
		}

		// Get item using the "dot" notation
		$array = self::$attributes;
		foreach (explode('.', $key) as $segment) {
			if (is_array($array) && array_key_exists($segment, $array)) {
				$array = $array[$segment];
			}
			else {
				return $default;
			}
		}

		return $array;
	}

	/**
	 * Put a key / value pair or array of key / value pairs in the session.
	 *
	 * @param  string|array  $key
	 * @param  mixed  $value
	 * @return void
	 */
	public static function put($key, $value = null)
	{
		if (!is_array($key)) {
			 $key = [$key => $value];
		}

		foreach ($key as $arrayKey => $arrayValue) {

			// Set array item to a given value using the "dot" notation

			$keys = explode('.', $arrayKey);

			$array = &self::$attributes;
			foreach ($keys as $i => $key) {
				if (count($keys) === 1) {
					break;
				}

				unset($keys[$i]);

				// If key doesnt exist at this depth, create empty array holder
				if (!isset($array[$key]) || !is_array($array[$key])) {
					$array[$key] = [];
				}

				$array = &$array[$key];
			}

			$array[array_shift($keys)] = $arrayValue;
		}
	}

	/**
	 * Get an item from the session, or store the default value.
	 *
	 * @param  string  $key
	 *
	 * @return mixed
	 */
	public static function emplace(string $key)
	{
		$value = self::get($key);
		if (!is_null($value)) {
			return $value;
		}

		self::put($key, $value);
		return $value;
	}

	/**
	 * Push a value onto a session array.
	 *
	 * @param  string  $key
	 * @param  mixed  $value
	 *
	 * @return void
	 */
	public static function push(string $key, $value): void
	{
		$array = self::get($key, []);
		$array[] = $value;
		self::put($key, $array);
	}

	/**
	 * Get the value of a given key and then remove it.
	 *
	 * @param  string  $keys
	 *
	 * @return mixed
	 */
	public static function pull(string $key, $default = null)
	{
		$result = self::get($key, $default);
		self::delete($key);

		return $result;
	}

	/**
	 * Delete one or many items from the session.
	 *
	 * @param  string|array  $keys
	 *
	 * @return void
	 */
	public static function delete($keys): void
	{
		$start = &self::$attributes;

		$keys = (array)$keys;

		if (count($keys) === 0) {
			return;
		}

		foreach ($keys as $key) {
			// Delete top-level if non-"dot" notation key
			if (self::exists($key)) {
				unset(self::$attributes[$key]);

				continue;
			}

			// Delete using "dot" notation key

			$parts = explode('.', $key);

			// Move to the start of the array each pass
			$array = &$start;

			// Traverse into the associative array
			while (count($parts) > 1) {
				$part = array_shift($parts);

				if (isset($array[$part]) && is_array($array[$part])) {
					$array = &$array[$part];
				}
				else {
					continue 2;
				}
			}

			unset($array[array_shift($parts)]);
		}
	}

	/**
	 * Remove all of the items from the session.
	 *
	 * @return void
	 */
	public static function flush(): void
	{
		self::$attributes = [];
	}

	//-------------------------------------//

	/**
	 * Get the CSRF token value.
	 *
	 * @return string
	 */
	public static function token()
	{
		return self::get('_token');
	}

	/**
	 * Regenerate the CSRF token value.
	 *
	 * @return void
	 */
	public static function regenerateToken()
	{
		self::put('_token', _randomStr(40));
	}

	/**
	 * Validate the CSRF token value to the given value.
	 *
	 * @param  array  $array
	 *
	 * @return bool
	 */
	public static function validateToken(array $array = null): bool
	{
		if (is_array($array) && array_key_exists('_token', $array) && $array['_token'] == self::token()) {
			return true;
		}

		return false;
	}

}
