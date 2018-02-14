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
			$basePath = $this->getTemplatePath($routeConfig);
			if ($basePath === null) {
				$basePath = str_replace(BASE_PATH, '', $routeConfig['path']) . DIRECTORY_SEPARATOR . 'view';
			}
			return $basePath . DIRECTORY_SEPARATOR . $routeConfig['view'];
		}
		return null;
	}

	protected function getTemplatePath($routeConfig) : ?string
	{
		$path = null;
		$conf = config('system.templatePath');
		if ($conf instanceof \Closure) {
			$path = call_user_func_array($conf, [$this, $routeConfig]);
		}
		return $path;
	}

	protected function getTplArgs() : array
	{
		$tplArgs['action'] = $this;
		$tplArgs['args'] = $this->args;
		$tplArgs['data'] = $this->outputData;
		$tplArgs['request'] = $this->controller->getRequest();
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