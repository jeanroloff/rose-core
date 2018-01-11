<?php

namespace Core\System\Cron;

class Job extends \ArrayObject
{
	public function __construct($command = null, $value = null)
	{
		parent::__construct();
		if ($command != null)
			$this->{$command}($value);
	}

	public function set($name, $arguments) : Job
	{
		$this->offsetSet($name, $arguments);
		return $this;
	}

	public function output($value)
	{
		return $this->set("output", $value);
	}

	public function closure(\Closure $value)
	{
		return $this->set("command", $value);
	}

	public function command($value)
	{
		return $this->set("command", $value);
	}

	public function schedule($value) : Job
	{
		return $this->set("schedule", $value);
	}

	public function debug($value) : Job
	{
		return $this->set("debug", $value);
	}

	public function everyMinute() : Job
	{
		return $this->schedule("* * * * *");
	}

	public function everyHour($minute = 0) : Job
	{
		return $this->schedule("{$minute} * * * *");
	}

	public function everyDay($minute = 0, $hour = 0) : Job
	{
		return $this->schedule("{$minute} {$hour} * * *");
	}

	public function everyMonth($minute = 0, $hour = 0, $day = 0) : Job
	{
		return $this->schedule("{$minute} {$hour} {$day} * *");
	}

	public function __call($name, $arguments) : Job
	{
		$value = is_array($arguments) ? $arguments[0] : $arguments;
		$this->offsetSet($name, $value);
		return $this;
	}
}