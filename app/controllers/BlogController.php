<?php

namespace App\Controllers;

use App\Model\BlogModel;

class BlogController extends PageController {

	public function indexAction(): void
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
		parent::view('blog', 'Hello');
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

		$this->router->service()->tags = function (string $tags): array {
			// Remove empty elements via array_filter()
			return array_filter(explode(':', $tags));
		};
	}


}
