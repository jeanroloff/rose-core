<?php

namespace Core\Action;

use Core\Controller\IController;
use Slim\Http\Response;

abstract class Input extends Action
{

	protected $status;

	public function __construct(IController $controller, array $args)
	{
		parent::__construct($controller, $args);
		$this->setModel();
	}

	abstract protected function onSuccess();

	abstract protected function onFailure();

	protected function setFields()
	{
		foreach ($this->args as $field => $value) {
			$this->model->{$field} = $value;
		}
	}

	protected function save()
	{
		if ($this->model->save()) {
			$this->onSuccess();
		} else {
			$this->onFailure();
		}
	}

	public function setOutputData()
	{
		$this->outputData = $this->model->toArray();
	}

	public function getOutput() : Response
	{
		return new \Core\System\Response( ['status' => $this->status, 'data' => $this->outputData], $this->getResponseCode());
	}

}