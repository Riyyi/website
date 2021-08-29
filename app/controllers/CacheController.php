<?php

namespace App\Controllers;

use App\Classes\Config;
use App\Classes\Http\Http;
use App\Classes\Session;
use App\Model\ConfigModel;

class CacheController extends PageController {

	/**
	 * Maximum amount of files that can be purged on a single request
	 */
	public static int $purgeLimit = 30;

	public function cacheAction(): void
	{
		$config = $this->getConfigValues();

		$this->router->service()->config = $config;
		$this->router->service()->csrfToken = Session::token();
		$this->router->service()->purgeUrl = $this->url . '/purge';
		parent::view();
	}

	public function purgeAction(): void
	{
		if (!$this->validatePostRequest()) {
			return;
		}

		$token = Config::c('CLOUDFLARE_TOKEN');
		$zone = Config::c('CLOUDFLARE_ZONE');

		$url = "https://api.cloudflare.com/client/v4/zones/$zone/purge_cache";

		if (!_exists($_POST, 'type')) {
			return;
		}

		$bodies = [];
		switch($_POST['type']) {
			case 'css-js':
				$body = self::generateUrls(['css', 'js']);
				$chunks = array_chunk($body['files'], self::$purgeLimit);
				foreach($chunks as $chunk) {
					$bodies[]['files'] = $chunk;
				}
				break;
			case 'fonts-images':
				$body = self::generateUrls(['fonts', 'img', 'media']);
				$chunks = array_chunk($body['files'], self::$purgeLimit);
				foreach($chunks as $chunk) {
					$bodies[]['files'] = $chunk;
				}
				break;
			case 'all':
				$bodies[] = ['purge_everything' => true];
				break;
			default:
				return;
		}

		$response = null;
		foreach($bodies as $body) {
			$response = (new Http)->withToken($token)
								->asJson()
								->acceptJson()
								->post($url, $body);
			if (empty($response->body())
				|| !json_decode($response->body(), true)['success']) {
				break;
			}
		}

		if ($response) {
			echo $response->body();
		}
	}

	public function toggleAction(): void
	{
		if (Config::c('CLOUDFLARE_ENABLED') != '1') {
			return;
		}

		$token = Config::c('CLOUDFLARE_TOKEN');
		$zone = Config::c('CLOUDFLARE_ZONE');

		$url = "https://api.cloudflare.com/client/v4/zones/$zone/settings/development_mode";

		$config = $this->getConfigValues();
		$currentState = $config['CLOUDFLARE_DEVELOPMENT_MODE_ENABLED'];

		$response = (new Http)->withToken($token)
							  ->asJson()
							  ->acceptJson()
							  ->patch($url, [
								  'value' => $currentState == '1' ? 'off' : 'on',
							  ]);

		$this->saveConfigValues($response->body());

		echo $response->body();
	}

	//-------------------------------------//

	private function validatePostRequest(): bool
	{
		if ($_SERVER['REQUEST_METHOD'] == 'GET') {
			parent::throw404();
		}

		if (Config::c('CLOUDFLARE_ENABLED') != '1') {
			return false;
		}

		if (!Session::validateToken($_POST)) {
			return false;
		}

		return true;
	}

	private static function generateUrls(array $directories): array
	{
		$result = [];

		foreach ($directories as $directory) {
			$files = array_diff(scandir($directory), ['..', '.']);
			foreach ($files as $file) {
				$result['files'][] = Config::c('APP_URL') . "/$directory/$file";
			}
		}

		return $result;
	}

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
