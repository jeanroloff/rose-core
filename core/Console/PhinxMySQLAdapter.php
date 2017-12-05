<?php

namespace Core\Console;

use Phinx\Db\Adapter\MysqlAdapter;

class PhinxMySQLAdapter extends MysqlAdapter
{
	public function isDryRunEnabled()
	{
		return false;
	}
}