<?php

namespace Core\Console\Commands\Module;

use Core\Console\Commands\StubCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

class MakeMap extends Base
{

	protected $name = 'map';

	protected $command = "module:make:map";

}