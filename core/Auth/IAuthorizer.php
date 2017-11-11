<?php

namespace Core\Auth;


interface IAuthorizer
{
	public function module($module, $pattern) : bool;
}