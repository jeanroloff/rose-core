<?php

namespace Core\Console\Commands\Db;

use Core\Console\Commands\StubCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

class MakeModel extends StubCommand
{
	protected $className;

	protected function getStub() : ?string
	{
		return file_get_contents(__DIR__."/template/model.stub");
	}

	protected function getPath() : string
	{
		return config('system.modelsPath') . DIRECTORY_SEPARATOR . $this->className . '.php';
	}

	protected function configure()
	{
		$this
			->setName("model:make")
			->setDescription("Creates a Model class")
			->setHelp("This command creates a model class file into the Modules folder")
			->addArgument("class", InputArgument::REQUIRED, "Name of the class file")
			->addArgument("table", InputArgument::OPTIONAL, "Name of the table")
		;
	}

	protected function setInfo($stub) : string
	{
		list ($this->className, $table) = $this->getNames($this->input);
		$stub = str_replace("{{class}}", $this->className, $stub);
		return str_replace("{{table}}", $table, $stub);
	}

	protected function getNames(InputInterface $input)
	{
		$class = Str::studly($input->getArgument('class'));
		$table = $input->getArgument('table');
		if ($table == null) {
			$table = Str::lower(Str::snake($class));
		}
		return [
			$class."Model",
			$table
		];
	}
}