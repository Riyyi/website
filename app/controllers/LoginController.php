<?php

namespace App\Controllers;

use App\Classes\Config;
use App\Classes\Db;
use App\Classes\Form;
use App\Classes\User;
use App\Classes\Mail;

class LoginController extends PageController {

	public function loginAction(string $view, string $title): void {
		$form = new Form($this->router);
		$form->addField('username', [
			'Username*',
			'text',
			'',
			'required',
			'Username is required.',
		]);
		$form->addField('password', [
			'Password*',
			'password',
			'',
			'required',
			'Password is required.',
		]);
		$form->addField('rememberMe', [
			'',
			'checkbox',
			['1' => 'Remember me'],
		]);

		$form->setSubmit('Sign in');

		if ($form->validated()) {
			if (User::login($_POST['username'], $_POST['password'], $_POST['rememberMe'])) {
				$this->setAlert('success', 'Successfully signed in, redirecting...');

				// Set delayed redirect URL
				$this->router->service()->redirectURL = Config::c('APP_URL') . '/admin';
			}
			else {
				$user = User::getUser('', $_POST['username']);
				if ($user->exists() && !$user->loginAllowed()) {
					$this->setAlert('danger', 'User has been blocked.');
				}
				else {
					$this->setAlert('danger', 'Invalid username and password combination.');
				}
			}
		}
		else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(!_exists($_POST, 'username') || !_exists($_POST, 'password')) {
				$this->setAlert('danger', 'Please fill out both fields.');
			}
			else {
				$this->setAlert('danger', 'Could not sign in.');
			}
		}

		$this->router->service()->password = "";
		parent::view($view, $title);
	}

	public function resetAction(string $view, string $title): void {

		// Send request
		if (!_exists($_GET, 'uid') || !_exists($_GET, 'reset-key')) {
			$this->requestResetForm();
		}
		// Reset password
		else {
			$this->resetPasswordForm();

			if ($_SERVER['REQUEST_METHOD'] != 'POST') {
				$this->router->service()->newPassword = true;
				$user = User::getUser($_GET['uid']);
				if (!$user->exists() || $_GET['reset-key'] != $user->reset_key) {
					$this->setAlert('danger', 'Link expired.');
				}
			}
		}

		parent::view($view, $title);
	}

	public function logoutAction(): void {
		User::logout();

		$this->router->response()->redirect('/');
	}

	//-------------------------------------//

	private function requestResetForm(): void {
		// Only have required on 1 field
		$usernameRule = !_exists($_POST, 'reset-password-email') ? 'required' : '';
		$emailRule = !_exists($_POST, 'reset-password-username') ? 'required|email' : '';

		$form = new Form($this->router);
		$form->addField('reset-password-username', [
			'Username',
			'text',
			'',
			"$usernameRule",
			'Username is required.',
		]);
		$form->addField('reset-password-email', [
			'Email',
			'email',
			'',
			"$emailRule",
			'Please enter a valid email address.',
			'[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$',
			'Valid email address'
		]);

		$form->setSubmit('Send request');

		if ($form->validated()) {
			$this->requestResetSend();
		}
		else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (!_exists($_POST, 'reset-password-username') &&
				!_exists($_POST, 'reset-password-email')) {
				$this->setAlert('danger', 'Please fill out one of the fields.');
			}
			else {
				$error = $form->errorMessage();
				$this->setAlert('danger', $error ? $error : 'Could not send request.');
			}
		}
	}

	private function requestResetSend(): void {
		$user = User::getUser('', $_POST['reset-password-username'], $_POST['reset-password-email']);
		if ($user->exists()) {

			// Generate new reset key
			$user->reset_key = _randomStr(25);
			$user->save();

			// Send reset mail
			$subject = 'Password reset request - ' . Config::c('APP_NAME');

			$resetUrl = Config::c('APP_URL') . "/reset-password?uid={$user->id}&reset-key={$user->reset_key}";
			$message = "
				Click the link below to reset your password:<br>
				<a href='$resetUrl'>$resetUrl</a>
			";

			if (Mail::sendMail($subject, $message, $user->email)) {
				$this->setAlert('success', 'Successfully requested password reset.');
			}
			else {
				$this->setAlert('danger', 'Password reset failed.');
			}
		}
		else {
			$this->setAlert('danger', 'User was not found.');
		}
	}

	private function resetPasswordForm(): void {
		// Add _GETs to form post url
		$uid = $_GET['uid'];
		$resetKey = $_GET['reset-key'];
		$this->router->service()->url .= "?uid=$uid&reset-key=$resetKey";

		$form = new Form($this->router);
		$form->addField('password', [
			'New password*',
			'password',
			'',
			'required',
			'New password is required.',
		]);
		$form->addField('password-again', [
			'New password again*',
			'password',
			'',
			'required',
			'New password again is required.',
		]);

		$form->setSubmit('Reset password');

		if ($form->validated()) {
			$this->resetPasswordSend();
		}
		else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (!_exists($_POST, 'password') ||
				!_exists($_POST, 'password-again')) {
				$this->setAlert('danger', 'Please fill out all fields.');
			}
			else {
				$error = $form->errorMessage();
				$this->setAlert('danger', $error ? $error : 'Could not reset password.');
			}
		}
	}

	private function resetPasswordSend(): void
	{
		$user = User::getUser($_GET['uid']);
		if ($user->exists()) {
			(bool)$notEmptyKey   = $user->reset_key != '';
			(bool)$correctKey    = $user->reset_key == $_GET['reset-key'];
			(bool)$identicalPass = $_POST['password'] == $_POST['password-again'];
		}
		if ($notEmptyKey && $correctKey && $identicalPass) {

			$user->salt = _randomStr(15);
			$user->password = password_hash($user->salt . $_POST['password'], PASSWORD_BCRYPT, ['cost', 12]);
			$user->reset_key = '';
			$user->save();

			$this->setAlert('success', 'Successfully changed password.');
		}

		// Display error message
		if (!$user || !$notEmptyKey || !$correctKey) {
			$this->setAlert('danger', 'Invalid password reset link.');
		}
		else if (!$identicalPass) {
			$this->setAlert('danger', 'Fields did not match.');
		}
	}

}
