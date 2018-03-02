<?php

namespace Core\Console;

use Symfony\Component\Console\Command\Command;

require __DIR__ . '/../Config/base.php';

class Console
{
	private $list;

	private function __construct()
	{
		$this->list();
	}

	public static function getInstance()
	{
		static $instance;
		if (!isset($instance)) {
			$instance = new Console;
		}
		return $instance;
	}

	public function bind($app)
	{
		foreach ($this->list as $command) {
			$app->add( new $command);
		}
	}

	private function list()
	{
		$ignoreList = array_merge([
			'Symfony\Component\Console\Command\Command',
			'Core\Console\Commands\StubCommand',
			'Core\Console\Commands\Module\Base'
		], config('system.console.ignore', []));
		$this->list = [];
		$added = [];
		$rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(BASE_PATH));
		foreach ($rii as $file) {
			if (!in_array($file->getFileName(),$added)) {
				if ($file->isDir()) {
					continue;
				}
				if (stripos($file->getBasename(), '.atom.php') === false) {
					continue;
				}
				$declared = get_declared_classes();
				include_once $file->getPathname();
				$currentDeclared = array_diff(get_declared_classes(), $declared);
				foreach ($currentDeclared as $ns) {
					if (!in_array($ns, $ignoreList)) {
						$this->list[$ns] = $ns;
					}
				}
				$added[] = $file->getFileName();
			}
		}
	}
}