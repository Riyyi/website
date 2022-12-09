<?php

namespace App\Controllers;

use App\Classes\Config;
use App\Model\BlogModel;

class BlogController extends PageController {

	public function searchAction(): void
	{
		$archived = $this->router->request()->param('archived', 0);
		$search = $this->router->request()->param('search', '');
		$posts = $this->search($search, $archived);

		$this->defineHelpers();

		// AJAX search request will only render the posts partial
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$this->router->service()->partial($this->views . '/partials/blog-posts.php', ['posts' => $posts]);
			return;
		}

		$this->router->service()->posts = $posts;
		$this->router->service()->search = $search;
		$this->router->service()->injectView = $this->views . '/partials/blog-posts.php';
		parent::view('blog', 'Blog');
	}

	public function rssAction(): void
	{
		date_default_timezone_set('Europe/Amsterdam');

		$xml = new \SimpleXMLElement('<rss version="2.0"/>');
		$channel = $xml->addChild('channel');
		$channel->addChild('title', 'Rick&apos;s Webpage');
		$channel->addChild('link', Config::c('APP_URL'));
		$channel->addChild('description', 'Recent content on Rick&apos;s Webpage.');
		$channel->addChild('language', 'en-us');
		$channel->addChild('lastBuildDate', date('D, d M Y H:i:s O'));

		// Fetch all non-archived blog posts
		$search = $this->router->request()->param('search', '');
		$posts = $this->search($search, 0);

		foreach ($posts as $post) {
			$link = Config::c('APP_URL') . '/' . $post['section'] . '/' . $post['page'];
			$date = (new \DateTime($post['created_at']))->format('D, d M Y H:i:s O');

			$item = $channel->addChild('item');
			$item->addChild('title', $post['title']);
			$item->addChild('link', $link);
			$item->addChild('description', $post['content']);
			$item->addChild('pubDate', $date);
			$item->addChild('guid', $link);
		}

		header('content-type: text/xml; charset=UTF-8');
		$dom = new \DOMDocument("1.0");
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML($xml->asXML());
		print $dom->saveXML();
	}

	//-------------------------------------//

	private function search(string $query = '', int $archived = 0): ?array
	{
		return BlogModel::selectAll(
			'blog_post.*, media.filename, media.extension, page.page, section.section, log.created_at', '
				LEFT JOIN media ON blog_post.media_id = media.id
				LEFT JOIN page ON blog_post.page_id = page.id
				LEFT JOIN section ON page.section_id = section.id
				LEFT JOIN log ON blog_post.log_id = log.id
				WHERE blog_post.archived = :archived AND
				(blog_post.content LIKE :query OR
				 blog_post.title LIKE :query OR
				 blog_post.tag LIKE :query)
			', [
				[':archived', "$archived", \PDO::PARAM_INT],
				[':query', "%$query%", \PDO::PARAM_STR]
			]);
	}

	private function defineHelpers(): void
	{
		$this->router->service()->prettyTimestamp = function (string $timestamp): string {
			$date = date_create($timestamp);
			$date = date_format($date, 'd M Y');
			return "<u class=\"text-decoration-none text-reset\" title=\"{$timestamp}\">{$date}</u>";
		};

		$this->router->service()->tags = function (string $tags): string {
			$result = "";

			// Remove empty elements via array_filter()
			$splitTags = array_filter(explode(':', $tags), function ($tag) {
				return !empty(trim($tag));
			});

			foreach ($splitTags as $key => $tag) {
				$result .= $tag;
				$result .= (($key === array_key_last($splitTags)) ? '' : ', ');
			}

			return $result;
		};
	}


}
