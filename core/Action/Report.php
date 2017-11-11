<?php

namespace Core\Action;

use Core\Controller\IController;
use Core\Data\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class Report extends Output
{
	protected $page;

	protected $size;

	public function __construct(IController $controller, array $args)
	{
		parent::__construct($controller, $args);
		$this->setModel();
	}

	protected function initializeProperties($config)
	{
		parent::initializeProperties($config);
		if (isset($this->args['page'])) {
			$this->page = $this->args['page'];
		}
		if (empty($this->page)) {
			$this->page = 1;
		}
		if (empty($this->size)) {
			$this->size = $this->args['size'] ?? 30;
			if ($this->size < 0) {
				$this->size = PHP_INT_MAX;
			}
		}
	}

	public function setOutputData()
	{
		$list = $this->getList();
		$this->outputData = [
			'data' => $list->items(),
			'page' => $list->currentPage(),
			'size' => $list->perPage(),
			'total' => $list->total()
		];
	}

	protected function getList() : LengthAwarePaginator
	{
		$model = $this->filterModel();
		$model = $this->setOrderBy($model);
		return $model->paginate($this->size, ['*'], 'page', $this->page);
	}

	protected function filterModel()
	{
		$model = $this->model;
		foreach ($this->filterReservedArgs() as $name => $value) {
			$model = $this->filterArgByName($model, $name, $value);
		}
		return $model;
	}

	protected function filterArgByName(Model $model, $name, $value)
	{
		if (is_array($value)) {
			if (count($value) == 2) {
				$model = $model->whereBetween($name, $value);
			} else {
				$model = $model->whereIn($name, $value);
			}
		} elseif ($value == null) {
			$model = $model->where($name,$value)->orWhereNull($name);
		} else {
			$model = $model->where($name, $value);
		}
		return $model;
	}

	protected function setOrderBy(Model $model)
	{
		if (isset($this->args['order'])) {
			$model = $model->orderBy($this->args['order'], Str::lower($this->args['orderType']));
		}
		return $model;
	}

	protected function filterReservedArgs()
	{
		$args = $this->args;
		unset($args['order']);
		unset($args['orderType']);
		unset($args['page']);
		unset($args['size']);
		unset($args['_']); // JQuery specific
		return $args;
	}
}