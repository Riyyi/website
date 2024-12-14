<?php

error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', 1);

require_once __DIR__ . '/../vendor/autoload.php';

\App\Classes\Session::start();
\App\Classes\Config::load();
\App\Classes\Router::fire();
