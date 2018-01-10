<?php

namespace Core\Auth;

use Illuminate\Support\Arr;

class User implements IUser
{
	protected $properties;

	protected $isAuthenticated;

	protected function __construct()
	{
		$this->properties = [];
		$this->isAuthenticated = !config('system.defaultSecure');
	}

	final public static function getInstance() : IUser
	{
		static $instance;
		if (!isset($instance)) {
			$class = config('system.user', User::class);
			$instance = new $class;
		}
		return $instance;
	}

	public function authenticate( $params )
	{
		$this->params = $params;
		if ($this->beforeAuthenticate() && $this->onAuthenticate() && $this->afterAuthenticate()) {
			$this->isAuthenticated = true;
			return true;
		}
		$this->isAuthenticated = false;
		return false;
	}

	protected function beforeAuthenticate()
	{
		return true;
	}

	protected function onAuthenticate()
	{
		return true;
	}

	protected function afterAuthenticate()
	{
		return true;
	}

	public function isAuthenticated(): bool
	{
		return $this->isAuthenticated;
	}

	public function getProperty($name, $fallback = null)
	{
		return Arr::get($this->properties, $name, $fallback);
	}

	public function setProperty($name, $value)
	{
		Arr::set($this->properties, $name, $value);
	}

	public function logout()
	{
		$this->properties = [];
		$this->isAuthenticated = false;
	}
}