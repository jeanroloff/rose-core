<?php

namespace Core\Console\Commands\Cron;

use Core\App;
use Core\System\Cron\Cron;
use Core\System\NotificationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CronExecute extends Command
{
	/**
	 * @var Cron
	 */
	protected $scheduler;

	protected function configure()
	{
		$this
			->setName("cron")
			->setDescription("Runs the predefined cron jobs")
			->setHelp("This command validates and execute the cronjobs registered in this application")
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		App::getInstance();
		$this->scheduler = new Cron();
		$this->setJobs();
		$this->setNotifications();
		$this->scheduler->run();
	}

	protected function setJobs()
	{
		$config = config('system.cron');
		if ($config instanceof \Closure || is_callable($config)) {
			call_user_func_array($config, [&$this->scheduler]);
		}
	}

	protected function setNotifications()
	{
		NotificationService::setListFromSystem();
		$list = NotificationService::list();
		foreach ($list as $notification) {
			$this->scheduler->closure(function () use ($notification) {
				try {
					App::getInstance();
					if ($notification->send()) {
						$notification->onSuccess();
					} else {
						$notification->onError();
					}
				} catch (\Exception $e) {
					echo "Error: " . $e->getMessage();
					return false;
				}
				return true;
			})->everyMinute();
		}
	}
}