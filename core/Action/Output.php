<?php

namespace Core\Action;

use Core\Controller\IController;
use Core\Http\Http;
use Core\System\Response;

class Output extends Action
{

	public function __construct(IController $controller, array $args)
	{
		parent::__construct($controller, $args);
	}

	public function execute()
	{
	}

	protected function onPreRender()
	{
	}

	protected function getHtmlOutput($file) : \Slim\Http\Response
	{
		$args = $this->getTplArgs();
		$container = $this->controller->getContainer();
		$view = $container->view;
		return $view->render($this->controller->getResponse(), $file, $args);
	}

	protected function getFile() : ?string
	{
		$routeConfig = $this->controller->getRoute();
		if (!empty($routeConfig['view'])) {
			$path = $this->getTemplatePath();
			if ($path === null) {
				$basePath = str_replace(BASE_PATH, '', $routeConfig['path']);
				$path = $basePath . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . $routeConfig['view'];
			}
			return $path;
		}
		return null;
	}

	protected function getTemplatePath() : ?string
	{
		$path = null;
		$conf = config('system.templatePath');
		if ($conf instanceof \Closure) {
			$path = call_user_func_array($conf, [$this]);
		}
		return $path;
	}

	protected function getTplArgs() : array
	{
		$tplArgs['action'] = $this;
		$tplArgs['args'] = $this->args;
		$tplArgs['data'] = $this->outputData;
		if (isset($this->model)) {
			$tplArgs['model'] = $this->model;
		}
		return $tplArgs;
	}

	public function getOutput() : \Slim\Http\Response
	{
		$this->onPreRender();
		if (Http::isHtml()) {
			$viewFile = $this->getFile();
			if ($viewFile !== null) {
				return $this->getHtmlOutput($viewFile);
			}
		}
		return new Response($this->outputData, $this->getResponseCode());
	}
}