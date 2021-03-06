<?php

namespace Core\System\Cron;

use Jobby\Jobby;

class Cron extends Jobby
{

	protected $jobList = [];

	protected function getJobName() : string
	{
		return "__INTERNAL_JOB__".count($this->jobs);
	}

	public function include($command, $value, $jobName = null) : Job
	{
		$jobName = $jobName ?? $this->getJobName();
		$job = new Job($command, $value);
		$this->jobList[$jobName] = $job;
		return $job;
	}

	public function closure(\Closure $value, $jobName = null) : Job
	{
		return $this->include("closure", $value, $jobName);
	}

	public function command($value, $jobName = null) : Job
	{
		return $this->include("command", $value, $jobName);
	}

	public function call($path, $jobName = null, $method = 'GET', $query = '', $contentType = 'application/json', $headers = []) : Job
	{
		return $this->closure(function () use ($path, $method, $query, $contentType, $headers){
			try {
				$data = \subrequest($path, $method, $query, $contentType, $headers);
				if (is_array($data)) {
					print_r($data);
				} else {
					echo $data;
				}
				return true;
			} catch (\Exception $e) {
				echo "Error: " . $e->getMessage();
				return false;
			}
		}, $jobName);
	}

	public function run()
	{
		foreach ($this->jobList as $name => $job) {
			$this->add($name, (array)$job);
		}
		return parent::run(); // TODO: Change the autogenerated stub
	}
}