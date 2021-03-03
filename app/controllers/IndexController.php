<?php

namespace App\Controllers;

use App\Classes\Config;
use App\Classes\Db;
use App\Classes\Router;
use App\Classes\Session;

class IndexController extends PageController {

    public function indexAction(): void
	{
		// Pull pages from cache
		$pages = Db::getPages();

		parent::routeAction(
			array_search('home', array_column($pages, 'page', 'id')));
    }

	public function captchaAction(): void
	{
		header('Content-type: image/jpeg');

		if (!Session::exists('captcha')) {
			Session::put('captcha', _randomStr(4, '0123456789'));
		}

		$imageWidth = 151;
		$imageHeight = 51;

		// Text
		$textSize = 30;
		$textFont = 'fonts/captcha.otf';
		$text = Session::get('captcha');

		// Generate position
		$randPosX = rand(0, 40);
		$randPosY = rand(35, 45);

		// Calculate rotation from the position
		$rotationFactorUp   = 1.0 - (($randPosY - 40.0) / 5.0);
		$rotationFactorDown = 1.0 - ((40.0 - $randPosY) / 5.0);
		// Clamp between 0.0-1.0
		$rotationFactorUp   = max(0.0, min(1.0, $rotationFactorUp));
		$rotationFactorDown = max(0.0, min(1.0, $rotationFactorDown));
		$rotation = rand(-8 * $rotationFactorUp, 8 * $rotationFactorDown);

		// Create image
		$image = imagecreate($imageWidth, $imageHeight);
		imagecolorallocate($image, 255, 255, 255);

		// Render number
		$textColor = imagecolorallocate($image, 0, 0, 0);
		imagettftext($image, $textSize, $rotation, $randPosX, $randPosY,
					 $textColor, $textFont, $text);

		// Render grid pattern
		$lineColor = imagecolorallocate($image, 73, 106, 164);
		$cord = 0;
		for ($i = 1; $i <= 31; $i++) {
			imageline($image, $cord, 0, $cord, 50, $lineColor);
			imageline($image, 0, $cord, 150, $cord, $lineColor);
			$cord = $cord + 5;
		}

		imagejpeg($image);
		exit();
	}

    public function sitemapAction(): void
	{
		$xml = new \SimpleXMLElement('<urlset/>');

		// Config routes
		$routes = array_column(Router::getRoutes(), '0');

		// Remove /admin and /test pages
		foreach ($routes as $key => $route) {
			if (strpos($route, '/admin') !== false ||
				strpos($route, '/test') !== false) {

				unset($routes[$key]);
			}
		}

		foreach ($routes as $route) {
			$url = $xml->addChild('url');
			$loc = Config::c('APP_URL') . $route;

			$url->addChild('loc', $loc);
		}

		Header('Content-type: text/xml');
		print($xml->asXML());
	}

}
