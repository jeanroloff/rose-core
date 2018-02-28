<?php

namespace Core\Console\Commands\Module;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Make extends Base
{
	protected $command = "module:make";

	protected function configure()
	{
		$this
			->setName($this->command)
			->setDescription("Creates a module with a map file and optionally a controller file")
			->setHelp("This command creates a module folder with a predefined map file and optionally a controller file")
			->addArgument("module", InputArgument::REQUIRED, "Name of the module")
			->addArgument('controller', InputArgument::OPTIONAL, "If set to \"true\" will create a Controller file within the module.")
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$result = $this->call('module:make:map', $input, $output);
		if ($input->getArgument('controller') == 'true') {
			$result = $this->call('module:make:controller', $input, $output);
		}
		return $result;
	}

	protected function call($command, $input, $output)
	{
		$command = $this->getApplication()->find($command);
		$arguments = array(
			'command' => $command,
			'module'    => $input->getArgument('module')
		);
		$newInput = new ArrayInput($arguments);
		return $command->run($newInput, $output);
	}
}