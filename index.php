<?php

if (is_dir(__DIR__."/vendor/")) {
	require_once __DIR__ . '/vendor/autoload.php';
} else {
	require_once __DIR__ . '/../../autoload.php';
}

use Core\App;

$app = App::getInstance();

$app->run();