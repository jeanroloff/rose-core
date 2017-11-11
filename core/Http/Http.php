<?php

namespace Core\Http;

use Illuminate\Support\Str;

class Http
{
	private static $contentType;

	private function __construct()
	{
	}

	public static function init()
	{
		self::setContentType();
	}

	public static function setContentType($value = null)
	{
		if ($value == null && is_callable('getallheaders')) {
			$headers = getallheaders();
			foreach ($headers as $k => $v) {
				if (Str::contains($k, "content-type")) {
					$value = $v;
				}
			}
		}
		self::$contentType = $value;
	}

	public static function getContentType()
	{
		if (self::$contentType == null) {
			return config('system.defaultContentType', 'application/json');
		}
		return self::$contentType;
	}

	public static function isJson()
	{
		return Str::contains(self::getContentType(), "json");
	}

	public static function isHtml()
	{
		return Str::contains(self::getContentType(), "html");
	}
}