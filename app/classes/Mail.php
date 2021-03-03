<?php

namespace App\Classes;

use Tx\Mailer;

class Mail {

	protected static $host;
	protected static $port;
	protected static $name;
	protected static $username;
	protected static $password;
	protected static $to;

	public static function _init(): void {
		self::$host     = Config::c('MAIL_HOST');
		self::$port     = Config::c('MAIL_PORT');
		self::$name     = Config::c('MAIL_NAME');
		self::$username = Config::c('MAIL_USERNAME');
		self::$password = Config::c('MAIL_PASSWORD');
		self::$to       = Config::c('MAIL_TO');
	}

	public static function send(
		string $subject, string $message, string $to = '', string $from = ''): bool
	{
		if ($to == '') {
			$to = self::$to;
		}
		if ($from == '') {
			$from = 'Website <'. self::$username . '>';
		}

		$headers =
			'MIME-Version: 1.0' . "\r\n" .
			'Content-type: text/html; charset=utf-8' . "\r\n" .
			'From: ' . $from . "\r\n" .
			'Reply-To: ' . $from . "\r\n" .
			'X-Mailer: PHP/' . phpversion();

		return mail($to, $subject, $message, $headers);
	}

	public static function sendMail(string $subject, string $message, string $from = '', string $to = ''): bool
	{
		if ($to == '') {
			$to = self::$to;
		}
		if ($from == '') {
			$from = self::$name;
		}
		if (empty(self::$host) || empty(self::$port) ||
			empty(self::$username) || empty(self::$password) || empty($to)) {
			return false;
		}

		$result = (new Mailer())
			->setServer(self::$host, self::$port, "tlsv1.2")
			->setAuth(self::$username, self::$password)
			->setFrom($from, self::$username)
			->addTo('', $to)
			->setSubject($subject)
			->setBody($message)
			->send();

		return $result;
	}

}

Mail::_init();
