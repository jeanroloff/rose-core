<?php

namespace Core\Console\Commands\Module;

use Core\Console\Commands\StubCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

class MakeAction extends Base
{

	protected $name = 'action';

	protected $command = "module:make:action";

	protected function configure()
	{
		parent::configure();
		$this->addArgument('action', InputArgument::REQUIRED, "Current action's name without \"Action\"");
	}

	protected function setInfo($stub): string
	{
		$stub = parent::setInfo($stub);
		return str_replace('{{action}}', Str::studly($this->input->getArgument('action')), $stub);
	}

	protected function getPath() : string
	{
		return config('system.modulesPath') . DIRECTORY_SEPARATOR . $this->module . DIRECTORY_SEPARATOR . 'Action' . $this->module . Str::studly($this->input->getArgument('action')) . '.php';
	}

}