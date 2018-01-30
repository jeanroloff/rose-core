<?php

namespace Core\Data;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;

class Model extends Eloquent
{
	private $connLv = 0;

	protected $aliases = [];

	protected function setDefaultAttributes(array $attributes)
	{
		$this->setRawAttributes(array_merge($this->attributes, $attributes), true);
	}

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
		if (!is_array($value) && trim($value) === '') {
			$value = null;
		}
		parent::__set($key, $value);
	}

	public function __get($key)
	{
		$key = $this->getFromAlias($key);
		return parent::__get($key);
	}

	protected function beforeInsert()
	{
	}

	protected function afterInsert()
	{
	}

	protected function beforeUpdate()
	{
	}

	protected function afterUpdate()
	{
	}

	protected function beforeDelete()
	{
	}

	protected function afterDelete()
	{
	}

	protected function beforeCommit()
	{
	}

	protected function afterCommit()
	{
	}

	protected function beforeRollback()
	{
	}

	protected function afterRollback()
	{
	}

	protected function performInsert(Builder $query)
	{
		try {
			$this->beginTransaction();
			$this->beforeInsert();
			$result = parent::performInsert($query);
			$this->afterInsert();
			if ($result) {
				$this->commit();
			} else {
				$this->rollback();
			}
		} catch (\Exception $e) {
			$this->rollback();
			throw $e;
		}
		return $result;
	}

	protected function performUpdate(Builder $query)
	{
		try {
			$this->beginTransaction();
			$this->beforeUpdate();
			$result = parent::performUpdate($query);
			$this->afterUpdate();
			if ($result) {
				$this->commit();
			} else {
				$this->rollback();
			}
		} catch (\Exception $e) {
			$this->rollback();
			throw $e;
		}
		return $result;
	}

	protected function performDeleteOnModel()
	{
		$this->beginTransaction();
		try {
			$this->beforeDelete();
			parent::performDeleteOnModel();
			$this->afterDelete();
			$this->commit();
		} catch (\Exception $e) {
			$this->rollback();
			throw $e;
		}
	}

	final private function beginTransaction()
	{
		$this->connLv++;
		if ($this->connLv == 1) {
			$this->getConnection()->beginTransaction();
		}
	}

	final private function commit()
	{
		if ($this->connLv > 0) {
			$this->connLv--;
		}
		if ($this->connLv == 0) {
			$this->beforeCommit();
			$this->getConnection()->commit();
			$this->afterCommit();
		}
	}

	final private function rollback()
	{
		if ($this->connLv > 0) {
			$this->connLv--;
		}
		if ($this->connLv == 0) {
			$this->beforeRollback();
			$this->getConnection()->rollBack();
			$this->afterRollback();
		}
	}

	public static function relationships()
	{
		$model = new static;
		$relationships = [];
		foreach((new \ReflectionClass($model))->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
			if ($method->class != get_class($model) ||
				!empty($method->getParameters()) ||
				$method->getName() == __FUNCTION__) {
				continue;
			}
			try {
				$return = $method->invoke($model);
				if ($return instanceof Relation) {
					$relationships[$method->getName()] = [
						'type' => (new \ReflectionClass($return))->getShortName(),
						'model' => (new \ReflectionClass($return->getRelated()))->getName()
					];
				}
			} catch(\ErrorException $e) {}
		}
		return $relationships;
	}

	public static function withRelations()
	{
		$relations = @array_keys(self::relationships());
		if ($relations != null) {
			$model = self::with($relations);
		} else {
			$model = new static;
		}
		return $model;
	}
}