<?php

namespace Core\Action;

use Core\Controller\IController;
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
		$this->setPaginationAttributes($list);
	}

	protected function setPaginationAttributes(LengthAwarePaginator $list)
	{
		$this->outputData['pagination'] = [
			'current' => [ 'id' => $list->currentPage(), 'url' => $this->getPageUrl($list->currentPage()) ],
			'previous' => ($list->currentPage() > 1 ? [ 'id' => $list->currentPage(), 'url' => $this->getPageUrl($list->currentPage() - 1) ] : []),
			'next' => ($list->lastPage() > $list->currentPage() ? [ 'id' => $list->currentPage(), 'url' => $this->getPageUrl($list->currentPage() + 1) ] : []),
			'first' => ($list->lastPage() > 1 ? [ 'id' => 1, 'url' => $this->getPageUrl(1) ] : []),
			'last' =>  ($list->lastPage() > 1 ? [ 'id' => $list->lastPage(), 'url' => $this->getPageUrl($list->lastPage()) ] : [])
		];
		for ($i=1; $i<= $list->lastPage(); $i++) {
			$this->outputData['pagination']['list'][$i] = ['id' => $i, 'url' => $this->getPageUrl($i)];
		}
	}

	protected function getPageUrl($page)
	{
		parse_str($this->controller->getRequest()->getUri()->getQuery(), $query);
		$query['page'] = $page;
		return $this->controller->getRequest()->getUri()->withQuery(http_build_query($query))->__toString();
	}

	protected function getList() : LengthAwarePaginator
	{
		$model = $this->filterModel();
		$model = $this->setOrderBy($model);
		return $model->paginate($this->size, ['*'], 'page', $this->page);
	}

	protected function filterModel()
	{
		$model = $this->model::withRelations();
		foreach ($this->filterReservedArgs() as $name => $value) {
			$model = $this->filterArgByName($model, $name, $value);
		}
		return $model;
	}

	protected function filterArgByName($model, $name, $value)
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

	protected function setOrderBy($model)
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