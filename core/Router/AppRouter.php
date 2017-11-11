<?php

namespace Core\Router;

use Core\App;

class AppRouter
{
	private $app;

	public function __construct(App $app)
	{
		$this->app = $app;
	}

	public function execute()
	{
		foreach ($this->getModules() as $name => $module) {
			$module['name'] = $name;
			$instance = new ModuleRouter($this->app->slim, $module);
			$instance->execute();
		}
	}

	private function getModules() : array
	{
		$config = config('modules');
		if (empty($config)) {
			throw new \Exception("Modules not defined for current application.");
		}
		return $config;
	}
}