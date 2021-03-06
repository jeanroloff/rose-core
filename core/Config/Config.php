<?php

namespace Core\Config;

use Illuminate\Support\Arr;

class Config extends \ArrayObject
{
	public function __construct(... $files)
	{
		$info = [];
		foreach ($files as $path) {
			if (is_file($path)) {
				$data = include $path;
				$info = array_merge_recursive_distinct($info, $data);
			}
		}
		Arr::set($this, 'system', $info['config']);
		if ($info['modules'] instanceof \Closure) {
			$info['modules'] = call_user_func_array($info['modules'],[]);
		}
		Arr::set($this, 'modules', $info['modules']);
		Arr::set($this, 'i18n', $info['i18n']);
	}

	public function get($key, $fallback = null)
	{
		return Arr::get($this, $key, $fallback);
	}

	public function set($key, $value)
	{
		Arr::set($this, $key, $value);
	}
}