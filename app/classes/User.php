<?php

namespace App\Classes;

use App\Model\UserModel;

class User {

	public static function check(): bool
	{
		$success = false;

		// Session
		if (Session::exists('user')) {
			$success = true;
		}

		// If cookie is set, try to login
		if (!$success &&
			_exists($_COOKIE, 'id') &&
			_exists($_COOKIE, 'username') &&
			_exists($_COOKIE, 'salt') &&
			_exists($_COOKIE, 'toggle')) {

			$user = UserModel::find($_COOKIE['id']);

			if ($user->exists() &&
				$_COOKIE['username'] == $user->username &&
				$_COOKIE['salt'] == $user->salt) {
				$success = true;

				self::setSession($_COOKIE['id'], $_COOKIE['username'],
								 $_COOKIE['salt'], $_COOKIE['toggle']);
			}
		}

		return $success;
	}

	public static function login(string $username, string $password, string $rememberMe): bool
	{
		$user = UserModel::search(['username' => $username]);

		$success = false;
		if ($user->exists() && $user->failed_login_attempt <= 2) {
			$saltPassword = $user->salt . $password;
			if (password_verify($saltPassword, $user->password)) {
				$success = true;

				// On successful login, set failed_login_attempt to 0
				if ($user->failed_login_attempt > 0) {
					$user->failed_login_attempt = 0;
					$user->save();
				}
			}
			else {
				$user->failed_login_attempt++;
				$user->save();
			}
		}

		if (!$success) {
			self::logout();

			return false;
		}

		// Set session
		self::setSession($user->id, $user->username, $user->salt, 1);

		// Set cookie
		if ($rememberMe == '1') {
			$time = time() + (3600 * 24 * 7);
			self::setCookie($time, $user->id, $user->username, $user->salt, 1);
		}

		return true;
	}

	public static function logout(): void
	{
		Session::delete('user');

		// Destroy user login cookie
		$time = time() - 3600;
		self::setCookie($time, 0, '', '', 0);
	}

	public static function getUser(string $id = '', string $username = '', string $email = ''): UserModel
	{
		if ($id == '' && $username == '' && $email == '' && self::check()) {
			$id = Session::get('user.id');
			$username = Session::get('user.username');
		}

		return UserModel::search([
			'id' => $id,
			'username' => $username,
			'email' => $email,
		], 'OR');
	}

	public static function toggle(): void
	{
		if (self::check()) {
			// Toggle session
			Session::put('user.toggle', !Session::get('user.toggle'));
			// Toggle cookie
			self::setCookieToggle(Session::get('user.toggle'));
		}
	}

	//-------------------------------------//

	protected static function setSession(
		int $id, string $username, string $salt, int $toggle): void
	{
		Session::put('user', [
			'id'			=> $id,
			'username'		=> $username,
			'salt'			=> $salt,
			'toggle'		=> $toggle,
		]);
	}

	protected static function setCookie(
		int $time, int $id, string $username, string $salt, int $toggle): void
	{
		if (_exists($_SERVER, 'HTTPS') && $_SERVER['HTTPS'] == 'on') {
			$domain = Config::c('APP_NAME');
			$options = [
				'expires' => $time,
				'path' => '/',
				'domain' => $domain,
				'secure' => true,
				'httponly' => true,
				'samesite' => 'Strict'
			];
			setcookie('id',        $id,        $options);
			setcookie('username',  $username,  $options);
			setcookie('salt',      $salt,      $options);
			setcookie('toggle',    $toggle,    $options);
		}
	}

	protected static function setCookieToggle(int $toggle): void
	{
		if (_exists($_SERVER, 'HTTPS') && $_SERVER['HTTPS'] == 'on') {
			$domain = Config::c('APP_NAME');
			$options = [
				'expires' => time() + (3600 * 24 * 7),
				'path' => '/',
				'domain' => $domain,
				'secure' => true,
				'httponly' => true,
				'samesite' => 'Strict'
			];
			setcookie('toggle', $toggle, $options);
		}
	}

	//-------------------------------------//

	public static function getToggle(): int
	{
		return self::check() ? Session::get('user.toggle') : 0;
	}

	public static function getSession(): array
	{
		return self::check() ? Session::get('user') : [];
	}

}
