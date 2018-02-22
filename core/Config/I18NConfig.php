<?php

namespace Core\Config;

use Illuminate\Support\Arr;

class I18NConfig extends \ArrayObject
{
	protected $entries;

	public function __construct($input = array(), int $flags = 0, string $iterator_class = "ArrayIterator")
	{
		parent::__construct($input, $flags, $iterator_class);
		$this->load();
	}

	public static function getInstance()
	{
		static $instance;
		if (!isset($instance)) {
			$instance = new I18NConfig();
		}
		return $instance;
	}

	public function offsetGet($index)
	{
		$this->load();
		return Arr::get($this, $index);
	}

	public function offsetSet($key, $value)
	{
		$this->load();
		Arr::set($this, $key, $value);
	}

	protected function load()
	{
		if (empty($this->entries)) {
			$info = config('i18n');
			if ($info instanceof \Closure) {
				$info = call_user_func_array($info,[]);
			}
			$this->entries = $info;
		}
	}
}