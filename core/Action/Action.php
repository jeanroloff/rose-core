<?php

namespace Core\Action;

use Core\Controller\IController;
use Slim\Http\Response;

abstract class Action implements IAction
{
	protected $messageCannotExecute;

	protected $responseCode;

	protected $outputData;

	public $controller;

	public $args;

	public $model;

	public function __construct(IController $controller, array $args)
	{
		$this->controller = $controller;
		$this->args = $args;
		$this->initializeProperties($this->getConfiguration());
	}

	protected function setModel()
	{
		$instance = $this->controller->getModel();
		if ($instance === null) {
			throw new \Exception("Model not defined for the module \"{$this->controller->getModuleName()}\".");
		}
		$this->model = $instance;
	}

	public function canExecute(array $args): bool
	{
		return true;
	}

	public function validate()
	{
	}

	abstract public function execute();

	public function setOutputData()
	{
		$this->outputData = [];
	}

	abstract public function getOutput(): Response;

	public function getMessageCannotExecute(): string
	{
		return $this->messageCannotExecute;
	}

	public function getResponseCode() : int
	{
		return $this->responseCode;
	}

	private function getConfiguration() : array
	{
		$config = config('modules.'.$this->controller->getModuleName());
		$properties = $this->getObjectProperties();
		foreach ($config as $name => $value) {
			if (!array_key_exists($name, $properties)) {
				unset($config[$name]);
			}
		}
		return $config;
	}

	private function getObjectProperties() : array
	{
		$objConfig = get_object_vars($this);
		unset($objConfig['controller']);
		unset($objConfig['model']);
		return $objConfig;
	}

	protected function initializeProperties($config)
	{
		foreach ($config as $name => $value) {
			$this->{$name} = $value;
		}
		if (empty($this->messageCannotExecute)) {
			$this->messageCannotExecute = "Não é possível executar esta operação";
		}
		if (empty($this->responseCode)) {
			$this->responseCode = 200;
		}
	}
}