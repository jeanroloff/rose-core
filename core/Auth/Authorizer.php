<?php

namespace Core\Auth;

class Authorizer implements IAuthorizer
{
	public $user;

	public function __construct()
	{
		$this->user = User::getInstance();
	}

	public function module($module, $pattern) : bool
	{
		return $this->user->isAuthenticated();
	}
}