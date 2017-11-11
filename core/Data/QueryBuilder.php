<?php

namespace Core\Data;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Support\Str;
use MongoDB\Driver\Query;

class QueryBuilder extends Builder
{
	protected $model;

	public function __construct(ConnectionInterface $connection, Grammar $grammar = null, Processor $processor = null, Model $model = null)
	{
		parent::__construct($connection, $grammar, $processor);
		$this->model = $model;
	}

	public function toSql()
	{
		if ($this->model instanceof Model) {
			$this->setColumnAliases();
			$this->setWhereAliases($this);
		}
		return $this->grammar->compileSelect($this);
	}

	protected function setColumnAliases()
	{
		foreach ($this->columns as $k => $column) {
			$this->columns[$k] = $this->model->getFromAlias($column, $column);
		}
	}

	protected function setWhereAliases(QueryBuilder $builder)
	{
		foreach ($builder->wheres as $k => $clause) {
			if ($clause['type'] == "Nested") {
				$this->setWhereAliases($clause['query']);
			} else {
				$builder->wheres[$k]['column'] = $this->model->getFromAlias($builder->wheres[$k]['column'], $builder->wheres[$k]['column']);
			}
		}
	}
}