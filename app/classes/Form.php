<?php

namespace App\Classes;

use \Klein\Klein;

class Form {

	private $router;

	private $data = [];
	private $resetLabel = '';
	private $submitLabel = 'Submit';
	private $errorKey = '';

	public function __construct(Klein $router)
	{
		$this->router = $router;
		$this->router->service()->csrfToken = Session::token();
		$this->router->service()->form = $this;
		$this->router->service()->injectView = '../app/views/form.php';
	}

	//-------------------------------------//

	public function addField(string $name, array $field): void
	{
		// "name" => [
		// 	"label",
		// 	"type",	   text, email, tel, password, radio, textarea, checkbox(?), comment
		// 	"data",	   (for radio fields) [ 'value' => 'Label', 'value' => 'Label']
		// 	"rules",   (server side rules)
		// 	"message", (server side error message)
		// 	"pattern", (client sided rule)
		// 	"title",
		// ],

		$this->data[$name] = [
			$field[0] ?? '',
			$field[1] ?? '',
			$field[2] ?? '',
			$field[3] ?? '',
			$field[4] ?? '',
			$field[5] ?? '',
			$field[6] ?? '',
		];
	}

	public function validated(array $submit = []): bool
	{
		$result = false;

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {

			$result = true;

			if (empty($submit)) {
				$submit = $_POST;
			}

			if (!Session::validateToken($submit)) {
				$result = false;
			}

			// Only check fields if CSRF token is valid
			if ($result) {
				// Check field rules
				foreach ($this->data as $ruleName => $ruleValue) {

					$found = false;
					$value = '';
					foreach ($submit as $submitName => $submitValue) {

						if ($ruleName == $submitName) {
							$found = true;
							$value = $submitValue;
							break;
						}

						if ($ruleValue[1] == 'comment') {
							$found = true;
							break;
						}
					}

					if (!$found || !$this->matchRule($ruleName, $value)) {
						$this->errorKey = $ruleName;
						$result = false;
						break;
					}
				}
			}

			// If unsuccessful, remember the form fields
			if (!$result) {
				foreach (array_keys($this->data) as $name) {
					if ($name == 'captcha') {
						continue;
					}

					$this->router->service()->{$name} = $submit[$name] ?? '';
				}
			}
		}

		Session::delete('captcha');

		return $result;
	}

	public function matchRule(string $key, string $value): bool
	{
		// Get the rule(s)
		$rule = $this->data[$key];
		$rules = explode('|', $rule[3]);

		if (array_search('required', $rules) !== false) {
			if (empty($value)) {
				return false;
			}
		}

		if (array_search('email', $rules) !== false) {
			if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
				return false;
			}
		}

		if (array_search('tel', $rules) !== false) {
			if (!filter_var($value, FILTER_VALIDATE_REGEXP, [
				'options' => [
					'regexp' => '/^([0-9]{2}-?[0-9]{8})|([0-9]{3}-?[0-9]{7})$/',
				]
			])) {
				return false;
			}
		}

		if (array_search('captcha', $rules) !== false) {
			if (!Session::exists('captcha')) {
				return false;
			}
			else if ($value != Session::get('captcha')) {
				return false;
			}
		}

		return true;
	}

	public function errorMessage(): string
	{
		return $this->errorKey != '' ? $this->data[$this->errorKey][4] : '';
	}

	//-------------------------------------//

	public function getFields(): array
	{
		return $this->data;
	}

	public function getReset(): string
	{
		return $this->resetLabel;
	}

	public function getSubmit(): string
	{
		return $this->submitLabel;
	}

	public function setData(array $data): void
	{
		$this->data = $data;
	}

	public function setReset(string $resetLabel): void
	{
		$this->resetLabel = $resetLabel;
	}

	public function setSubmit(string $submitLabel): void
	{
		$this->submitLabel = $submitLabel;
	}

}
