<?php

namespace Core\Action;

use Core\Controller\IController;
use Core\Exception\Message;

class View extends Output
{
	public function __construct(IController $controller, array $args)
	{
		parent::__construct($controller, $args);
		$this->setModel();
	}

	public function validate()
	{
		if (!array_key_exists('id', $this->args)) {
			throw new Message("ID not informed");
		}
	}

	public function execute()
	{
		$this->model = $this->model::withRelations()->find($this->args['id']);
	}

	public function setOutputData()
	{
		if ($this->model != null) {
			$this->outputData = $this->model->toArray();
		}
	}
}