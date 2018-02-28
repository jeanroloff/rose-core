<?php

namespace Core\Console\Commands\Db;

use Core\Console\PhinxMySQLAdapter;
use Phinx\Console\PhinxApplication;
use Phinx\Db\Adapter\AdapterFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Seed extends Command
{
	protected function configure()
	{
		$this
			->setName("db:seed")
			->setDescription("Execute the database seeding")
			->setHelp("This command executes the seeding of the database")
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		putenv("PHINX_ENVIRONMENT=default");
		$_SERVER['argv'] = [
			'null',
			"seed:run"
		];
		$phinx = new PhinxApplication();
		AdapterFactory::instance()->registerAdapter('mysql', PhinxMySQLAdapter::class);
		$phinx->run();
	}
}