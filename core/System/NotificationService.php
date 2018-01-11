<?php

namespace core\System;

class NotificationService
{
	private static $list = [];

	private function __construct()
	{
	}

	public static function setListFromSystem()
	{
		$config = config('system.notifications');
		if ($config instanceof \Closure || is_callable($config)) {
			call_user_func_array($config);
		}
	}

	public static function send(INotification $notification, $now = false)
	{
		if ($now) {
			self::sendNow($notification);
		} else {
			self::$list[] = $notification;
		}
	}

	public static function sendNow(INotification $notification)
	{
		if ($notification->send()) {
			$notification->onSuccess();
		} else {
			$notification->onError();
		}
	}

	/**
	 * @return INotification[]
	 */
	public static function list() : array
	{
		return self::$list;
	}
}