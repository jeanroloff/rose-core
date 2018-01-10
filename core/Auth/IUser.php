<?php

namespace Core\Auth;

interface IUser
{

	public static function getInstance() : IUser;

	public function isAuthenticated() : bool;

	public function authenticate( $params );

	public function logout();

	public function setProperty($name, $value);

	public function getProperty($name, $fallback = null);
}