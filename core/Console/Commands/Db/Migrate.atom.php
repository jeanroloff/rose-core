<?php

namespace Core\Console\Commands\Db;

use Core\Console\PhinxMySQLAdapter;
use Phinx\Console\PhinxApplication;
use Phinx\Db\Adapter\AdapterFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Migrate extends Command
{
	protected function configure()
	{
		$this
			->setName("db:migrate")
			->setDescription("Execute the database migration")
			->setHelp("This command executes all the unused migrations")
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		putenv("PHINX_ENVIRONMENT=default");
		$_SERVER['argv'] = [
			'null',
			"migrate"
		];
		$phinx = new PhinxApplication();
		AdapterFactory::instance()->registerAdapter('mysql', PhinxMySQLAdapter::class);
		$phinx->run();
	}
}