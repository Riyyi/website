<?php

use \App\Classes\Router;

Router::resource('/admin/config',              'CrudController');
Router::resource('/admin/section',             'CrudController');
Router::resource('/admin/page',                'CrudController');
Router::resource('/admin/content',             'CrudController');
Router::resource('/admin/section-has-content', 'CrudController');
Router::resource('/admin/page-has-content',    'CrudController');
Router::resource('/admin/media',               'MediaController');

// Basic routes
return [
	// URL,                         controller,           action,     view/title/description
	['/',                           'IndexController',    '',         ''],
	['/img/captcha.jpg',            'IndexController',    'captcha',  ''],
	['/robots.txt',                 'IndexController',    'robots',   ''],
	['/sitemap.xml',                'IndexController',    'sitemap',  ''],
	['/login',                      'LoginController',    'login',    ['', 'Sign in', '']],
	['/reset-password',             'LoginController',    'reset',    ['', 'Reset password', '']],
	['/logout',                     'LoginController',    'logout',   ''],
	['/admin',                      'AdminController',    '',         ''],
	['/admin/cache',                'CacheController',      'cache',        ''],
	['/admin/toggle',               'AdminController',    'toggle',   ''],
	['/admin/toggle-development-mode',  'CacheController',  'development',  ''],
	['/admin/syntax-highlighting',  'AdminController',    'syntax',   ''],
	['/test', 'TestController', '', ''],
	// ["", "", "", ""],
];
