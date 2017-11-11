<?php
require __DIR__ . '/vendor/autoload.php';
$app = \Core\App::getInstance();
$capsule = $app->getCapsule();
return [
	'paths' => [
		'migrations' => '%%PHINX_CONFIG_DIR%%/app/database/migration/',
		'seeds' => '%%PHINX_CONFIG_DIR%%/app/database/seed/'
	],
	'migration_base_class' => '\Core\Data\Migration',
	'environments' => [
		'default_migration_table' => 'migrations',
		'default' => [
			'name' => \Core\Config\DbConfig::getInstance()->database,
			'connection' => $capsule->getConnection()->getPdo()
		]
	]
];