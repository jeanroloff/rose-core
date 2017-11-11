<?php

namespace Core\Auth;

interface IUser
{

	public static function getInstance() : IUser;

	public function isAuthenticated() : bool;
}