<?php

namespace Core\Console;

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
		foreach ($this->getPaths() as $path) {
			$rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
			foreach ($rii as $file) {
				if ($file->isDir()) {
					continue;
				}
				if ($file->getExtension() == 'stub') {
					continue;
				}
				$declared = get_declared_classes();
				include_once $file->getPathname();
				$currentDeclared = array_diff(get_declared_classes(), $declared);
				foreach ($currentDeclared as $ns) {
					if (!in_array($ns,$ignoreList)) {
						$this->list[] = $ns;
					}
				}
			}
		}
	}

	private function getPaths() : array
	{
		$paths[] = config('system.console.commands');
		$paths[] = __DIR__ . '/Commands/';
		return $paths;
	}
}