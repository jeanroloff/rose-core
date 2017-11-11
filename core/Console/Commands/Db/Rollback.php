<?php

namespace Core\Console\Commands\Db;

use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Rollback extends Command
{
	protected function configure()
	{
		$this
			->setName("db:rollback")
			->setDescription("Execute the database rollback migration")
			->setHelp("This command executes the database rollback migrations")
			->addArgument("timestamp", InputArgument::OPTIONAL, "Target timestamp for the rollback")
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		putenv("PHINX_ENVIRONMENT=default");
		$_SERVER['argv'] = [
			'null',
			"rollback"
		];
		if ($value = $input->getArgument("timestamp")) {
			$_SERVER['argv'][] = "-t";
			$_SERVER['argv'][] = $value;
		}
		$phinx = new PhinxApplication();
		$phinx->run();
	}
}