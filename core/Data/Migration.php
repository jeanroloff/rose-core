<?php

namespace Core\Data;

use Core\App;
use Phinx\Migration\AbstractMigration;

class Migration extends AbstractMigration
{
	/** @var \Illuminate\Database\Capsule\Manager $capsule */
	public $capsule;
	/** @var \Illuminate\Database\Schema\Builder $capsule */
	public $schema;

	public function init() {
		$this->capsule = App::getInstance()->getCapsule();
		$this->schema = $this->capsule->schema();
	}
}