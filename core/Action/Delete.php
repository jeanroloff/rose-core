<?php

namespace Core\Action;

use Core\Controller\IController;
use Core\Exception\Message;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Delete extends Input
{
	public function __construct(IController $controller, array $args)
	{
		parent::__construct($controller, $args);
		$this->setModel();
	}

	public function validate()
	{
		if (!array_key_exists('id', $this->args)) {
			throw new Message("ID not informed", 400);
		}
	}

	public function execute()
	{
		try {
			$this->model = $this->model::findOrFail($this->args['id']);
		} catch (ModelNotFoundException $e) {
			throw new Message($e->getMessage(), 404);
		}
		$this->save();
	}

	protected function save()
	{
		if ($this->model->delete()) {
			$this->onSuccess();
		} else {
			$this->onFailure();
		}
	}

	protected function onSuccess()
	{
		$this->status = "Success";
		$this->responseCode = 200;
	}

	protected function onFailure()
	{
		$this->status = "Error";
		$this->responseCode = 400;
	}
}