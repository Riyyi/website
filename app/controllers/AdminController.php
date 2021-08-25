<?php

namespace App\Controllers;

use App\Classes\Config;
use App\Classes\User;

class AdminController extends PageController {

	public function indexAction(): void {
		$this->router->service()->user = User::getUser();

		parent::view('', 'Admin');
	}

	public function developmentAction(): void
	{
		if (Config::c('DEVELOPMENT_MODE') == 'cloudflare') {
			$token = Config::c('DEVELOPMENT_MODE_TOKEN');
			$zone = Config::c('DEVELOPMENT_MODE_ZONE');

			$url = "https://api.cloudflare.com/client/v4/zones/$zone/settings/development_mode";
			$headers = [
				"Authorization: Bearer $token",
				"Content-Type: application/json"
			];
			$data = '{"value": "on"}';

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			// curl_setopt($curl, CURLINFO_HEADER_OUT, 1);

			$response = curl_exec($curl);
			// $info = curl_getinfo($curl, CURLINFO_HEADER_OUT);

			curl_close($curl);
		}

		echo $response;
		// echo $info;
	}

	public function toggleAction(): void {
		User::toggle();
		echo User::getToggle() ? '1' : '0';
	}

	public function syntaxAction(): void {
		parent::view();
	}

}
