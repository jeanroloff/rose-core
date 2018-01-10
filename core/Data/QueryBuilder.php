<?php

namespace Core\Data;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;

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
		if (is_array($this->columns)) {
			foreach ($this->columns as $k => $column) {
				$this->columns[$k] = $this->model->getFromAlias($column, $column);
			}
		}
	}

	protected function setWhereAliases(QueryBuilder $builder)
	{
		foreach ($builder->wheres as $k => $clause) {
			if ($clause['type'] == "Nested") {
				$this->setWhereAliases($clause['query']);
			} else {
				if (is_array($builder->wheres[$k])) {
					$builder->wheres[$k]['column'] = $this->model->getFromAlias(@$builder->wheres[$k]['column'], @$builder->wheres[$k]['column']);
				}
			}
		}
	}
}