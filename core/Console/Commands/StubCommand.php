<?php

namespace Core\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class StubCommand extends Command
{
	protected $input;

	protected $output;

	abstract protected function getStub() : ?string;

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->input = $input;
		$this->output = $output;
		$stub = $this->getStub();
		$stub = $this->setInfo($stub);
		$filepath = $this->getPath();
		if (!is_file($filepath)) {
			$this->setFile($filepath, $stub);
			$output->writeln("File \"{$filepath}\" created.");
		} else {
			$output->writeln("Error, file \"{$filepath}\" already exists!");
		}
	}

	abstract protected function setInfo($stub) : string;

	abstract protected function getPath() : string;

	protected function setFile($filepath, $stub)
	{
		file_put_contents($filepath, $stub);
	}
}