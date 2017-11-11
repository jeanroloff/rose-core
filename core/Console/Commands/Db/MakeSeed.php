<?php

namespace Core\Console\Commands\Db;

use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeSeed extends Command
{
	protected function configure()
	{
		$this
			->setName("db:make:seed")
			->setDescription("Creates a seed")
			->setHelp("This command creates a new seed file for the database")
			->addArgument("name", InputArgument::REQUIRED, "Name of the migration class")
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		putenv("PHINX_ENVIRONMENT=default");
		$_SERVER['argv'] = [
			'null',
			"seed:make",
			$input->getArgument("name")
		];
		$phinx = new PhinxApplication();
		$phinx->run();
	}
}