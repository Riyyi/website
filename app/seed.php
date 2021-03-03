<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Classes\Config;
use App\Classes\Db;
use App\Classes\User;

use App\Model\ContentModel;
use App\Model\PageModel;
use App\Model\PageHasContentModel;
use App\Model\SectionModel;
use App\Model\SectionHasContentModel;
use App\Model\UserModel;

Config::load();
Db::load();

// Drop db and reset auto increment
//-------------------------------------//

$query = "
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE `page_has_content`;
TRUNCATE `section_has_content`;
TRUNCATE `content`;
TRUNCATE `page`;
TRUNCATE `section`;
TRUNCATE `media`;
TRUNCATE `log`;
TRUNCATE `user`;
SET FOREIGN_KEY_CHECKS = 1;

ALTER TABLE `page_has_content`    AUTO_INCREMENT = 1;
ALTER TABLE `section_has_content` AUTO_INCREMENT = 1;
ALTER TABLE `content`             AUTO_INCREMENT = 1;
ALTER TABLE `page`                AUTO_INCREMENT = 1;
ALTER TABLE `section`             AUTO_INCREMENT = 1;
ALTER TABLE `media`               AUTO_INCREMENT = 1;
ALTER TABLE `log`                 AUTO_INCREMENT = 1;
ALTER TABLE `user`                AUTO_INCREMENT = 1;
";

if ($argc >= 2) {
	$query = Db::get()->prepare($query);
	$query->execute();
	die();
}

// Users
//-------------------------------------//

$users = [
	['', '', '', '', '', '', '0', ''], // 1
	// ['', '', '', '', '', '', '', ''],
];

foreach ($users as $user) {
	UserModel::firstOrCreate(
		['username' => $user[0]],
		[
			'email'                => $user[1],
			'first_name'           => $user[2],
			'last_name'            => $user[3],
			'salt'                 => $user[4],
			'password'             => $user[5],
			'failed_login_attempt' => $user[6],
			'reset_key'            => $user[7],
		]
	);
}

User::login($users[0][0], 'password', $users[0][6]);

// Sections
//-------------------------------------//

$sections = [
	['home', 'Homepage', '1', '1', '1'], // 1
	// ['', '', '', '', ''],
];

foreach ($sections as $section) {
	SectionModel::firstOrCreate(
		['section' => $section[0]],
		[
			'title'           => $section[1],
			'order'           => $section[2],
			'hide_navigation' => $section[3],
			'active'          => $section[4],
		],
	);
}

// Pages
//-------------------------------------//

$pages = [
	['home', 'Homepage', '', '', '1', '1', '0', '1', '1'], // 1
	// ['', '', '', '', '', '', '', '', ''],
];

foreach ($pages as $page) {
	PageModel::firstOrCreate(
		['page' => $page[0]],
		[
			'title'            => $page[1],
			'title_url'        => $page[2],
			'meta_description' => $page[3],
			'type'             => $page[4],
			'order'            => $page[5],
			'hide_navigation'  => $page[6],
			'active'           => $page[7],
			'section_id'       => $page[8],
		],
	);
}

// Content
//-------------------------------------//

$contents = [
	['Homepage', 'home', '1', '1', '0', '1'], // 1
	// ['', '', '', '', '', ''],
];

foreach ($contents as $content) {
	ContentModel::firstOrCreate(
		['title' => $content[0]],
		[
			'content'         => $content[1],
			'type'            => $content[2],
			'hide_title'      => $content[3],
			'hide_background' => $content[4],
			'active'          => $content[5],
		],
	);
}

// PageHasContent
//-------------------------------------//

$pageLinks = [
	// id, order, page_id, content_id
	[ '1', '1',  '1',  '1'],
	// ['', '', '', ''],
];

foreach ($pageLinks as $pageLink) {
	PageHasContentModel::firstOrCreate(
		['id' => $pageLink[0]],
		[
			'order'      => $pageLink[1],
			'page_id'    => $pageLink[2],
			'content_id' => $pageLink[3],
		],
	);
}

// SectionHasContent
//-------------------------------------//

$sectionLinks = [
	// id, order, section_id, content_id
	// ['1', '1', '1' '1'],
	// ['', '', '', ''],
];

foreach ($sectionLinks as $sectionLink) {
	SectionHasContentModel::firstOrCreate(
		['id' => $sectionLink[0]],
		[
			'order'      => $sectionLink[1],
			'section_id' => $sectionLink[2],
			'content_id' => $sectionLink[3],
		],
	);
}
