<?php

namespace Core\Config;

class ApplicationConfig
{
	private $config;

	private function __construct()
	{
		$this->config = new Config(CORE_PATH . 'Config/application.config.php', APP_PATH . 'config/application.config.php');
	}

	public static function getInstance()
	{
		static $instance;
		if (!isset($instance)) {
			$instance = new ApplicationConfig;
		}
		return $instance;
	}

	public function get($key, $fallback = null) {
		return $this->config->get($key, $fallback);
	}

	public function set($key, $value) {
		$this->config->set($key, $value);
	}
}