<?php

namespace Core\Config;

use Core\Exception\EmptyConfigException;
use Illuminate\Support\Str;

class DbConfig
{
	public $driver;

	public $host;

	public $port;

	public $username;

	public $password;

	public $database;

	private function __construct()
	{
		$config = \config('system.db');
		if (empty($config)) {
			throw new EmptyConfigException("Database configuration not found.");
		}
		if (isset($config['dsn'])) {
			$config = $this->parseDSN($config['dsn']);
		}
		foreach ($config as $k => $v) {
			$this->{$k} = $v;
		}
	}

	public static function getInstance() : DbConfig
	{
		static $instance;
		if (!isset($instance)) {
			$instance = new DbConfig;
		}
		return $instance;
	}

	private function parseDSN($dsn) : array
	{
		$parts = parse_url($dsn);
		return [
			'driver' => $parts['scheme'],
			'host' => $parts['host'],
			'port' => @$parts['port'],
			'username' => $parts['user'],
			'password' => @$parts['pass'],
			'database' => Str::substr($parts['path'], 1)
		];
	}

	public function toArray() : array
	{
		return [
			'driver' => $this->driver,
			'host' => $this->host,
			'port' => $this->port,
			'username' => $this->username,
			'password' => $this->password,
			'database' => $this->database
		];
	}
}