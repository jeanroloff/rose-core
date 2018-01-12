<?php

namespace Core\System;

use Core\App;

class NotificationService
{
	private static $list = [];

	private function __construct()
	{
	}

	public static function setListFromSystem()
	{
		$config = config('system.notification');
		if ($config instanceof \Closure || is_callable($config)) {
			call_user_func_array($config,[]);
		}
	}

	public static function send(INotification $notification, $now = false)
	{
		self::$list[] = $notification;
	}
	/**
	 * @return INotification[]
	 */
	public static function list() : array
	{
		return self::$list;
	}
}