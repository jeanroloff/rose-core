<?php

namespace Core\Console\Commands\Db;

use Core\Console\PhinxMySQLAdapter;
use Phinx\Console\PhinxApplication;
use Phinx\Db\Adapter\AdapterFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeMigration extends Command
{
	protected function configure()
	{
		$this
			->setName("db:make:migration")
			->setDescription("Creates a migration")
			->setHelp("This command creates a new migration file for the database usage")
			->addArgument("name", InputArgument::REQUIRED, "Name of the migration class")
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		putenv("PHINX_ENVIRONMENT=default");
		$_SERVER['argv'] = [
			'null',
			"create",
			$input->getArgument("name")
		];
		$phinx = new PhinxApplication();
		AdapterFactory::instance()->registerAdapter('mysql', PhinxMySQLAdapter::class);
		$phinx->run();
	}
}