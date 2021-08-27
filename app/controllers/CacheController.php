<?php

namespace App\Controllers;

use App\Classes\Config;
use App\Model\ConfigModel;

class CacheController extends PageController {

	public function cacheAction(): void
	{
		$config = $this->getConfigValues();

		$this->router->service()->config = $config;
		parent::view();
	}

	public function developmentAction(): void
	{
		if (Config::c('CLOUDFLARE_ENABLED') != '1') {
			return;
		}

		$token = Config::c('CLOUDFLARE_TOKEN');
		$zone = Config::c('CLOUDFLARE_ZONE');

		$url = "https://api.cloudflare.com/client/v4/zones/$zone/settings/development_mode";
		$headers = [
			"Authorization: Bearer $token",
			"Content-Type: application/json"
		];

		$config = $this->getConfigValues();
		$currentState = $config['CLOUDFLARE_DEVELOPMENT_MODE_ENABLED'];

		$newState = $currentState == '1' ? 'off' : 'on';
		$data = '{"value": "' . $newState . '"}';

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

		$this->saveConfigValues($response);

		echo $response;
		// echo $info;
	}

	//-------------------------------------//

	private static function getConfigValues(): array
	{
		$result = [];

		$config = [
			'CLOUDFLARE_DEVELOPMENT_MODE_ENABLED' => '0',
			'CLOUDFLARE_DEVELOPMENT_MODE_UPDATED_AT' => '',
			'CLOUDFLARE_DEVELOPMENT_MODE_EXPIRES_IN' => '',
		];

		foreach ($config as $key => $value) {
			$result[$key] = ConfigModel::firstOrCreate(
				['key' => $key],
				['value' => $value]
			)->value;
		}

		if ($result['CLOUDFLARE_DEVELOPMENT_MODE_ENABLED']) {
			$expiresIn = $result['CLOUDFLARE_DEVELOPMENT_MODE_EXPIRES_IN'];
			$updatedAt = $result['CLOUDFLARE_DEVELOPMENT_MODE_UPDATED_AT'];

			$expiresAtObject = new \DateTime($updatedAt);
			$expiresAt = $expiresAtObject
					   ->modify("+ $expiresIn seconds")
					   ->format('Y-m-d H:i:s');
			$nowObject = new \DateTime('now');
			$now = $nowObject->format('Y-m-d H:i:s');

			if ($now >= $expiresAt) {
				ConfigModel::updateOrCreate(
					['key' => 'CLOUDFLARE_DEVELOPMENT_MODE_ENABLED'],
					['value' => 0]);
				$result['CLOUDFLARE_DEVELOPMENT_MODE_ENABLED'] = 0;
			}
			else {
				$result['enabled-remaining'] = $expiresAtObject->modify(
					'- ' . $nowObject->getTimestamp() . ' seconds'
				)->format('H:i:s');
			}
		}

		return $result;
	}

	private static function saveConfigValues(string $response): void
	{
		$decodedResponse = json_decode($response, true);
		if ($decodedResponse['success'] == true) {
			$state = $decodedResponse['result']['value'];
			$expiresIn = $decodedResponse['result']['time_remaining'];
			$updatedAt = $decodedResponse['result']['modified_on'];

			$updatedAtFormatted = (new \DateTime($updatedAt))->format('Y-m-d H:i:s');

			ConfigModel::updateOrCreate(
				['key' => 'CLOUDFLARE_DEVELOPMENT_MODE_ENABLED'],
				['value' => $state == 'on' ? 1 : 0]);

			ConfigModel::updateOrCreate(
				['key' => 'CLOUDFLARE_DEVELOPMENT_MODE_EXPIRES_IN'],
				['value' => $expiresIn]);

			ConfigModel::updateOrCreate(
				['key' => 'CLOUDFLARE_DEVELOPMENT_MODE_UPDATED_AT'],
				['value' => $updatedAtFormatted]);
		}
	}

}
