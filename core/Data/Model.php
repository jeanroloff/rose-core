<?php

namespace Core\Data;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Arr;

class Model extends Eloquent
{
	protected $aliases = [];

	public function getAlias($field, $fallback = null) : ?string
	{
		if (Arr::exists($this->aliases, $field)) {
			return $this->aliases[$field];
		}
		return $fallback;
	}

	public function getAliased(array $attributes) : array
	{
		$result = [];
		foreach ($attributes as $k => $v) {
			$result[$this->getAlias($k, $k)] = $v;
		}
		return $result;
	}

	public function getFromAlias($column)
	{
		if (in_array($column, $this->aliases)) {
			return array_flip($this->aliases)[$column];
		}
		return $column;
	}

	protected function getArrayableAttributes()
	{
		return $this->getAliased(parent::getArrayableAttributes());
	}

	protected function newBaseQueryBuilder()
	{
		$conn = $this->getConnection();
		$grammar = $conn->getQueryGrammar();
		return new QueryBuilder($conn, $grammar, $conn->getPostProcessor(), $this);
	}

	public function __set($key, $value)
	{
		$key = $this->getFromAlias($key);
		parent::__set($key, $value);
	}

	public function __get($key)
	{
		$key = $this->getFromAlias($key);
		return parent::__get($key);
	}
}