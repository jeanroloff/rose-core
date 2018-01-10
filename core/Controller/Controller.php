<?php

namespace Core\Controller;

use Core\Auth\IAuthorizer;
use Core\Action\IAction;
use Core\Data\Model;
use Core\Http\Http;
use Core\System\Response as CoreResponse;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class Controller implements IController
{
	private $config;

	public $container;

	public $request;

	public $response;

	public function __construct(array $config, Container $container)
	{
		$this->container = $container;
		$this->config = $config;
	}

	public function __invoke(Request $request, Response $response, array $args) : Response
	{
		Http::setContentType($request->getContentType());
		$this->request = $request;
		$this->response = $response;
		if ($this->config['secure'] && !$this->authorize($this->config['module'], $this->config['pattern'])) {
			return $this->authDenied($request, $response, $args);
		}
		$params = array_merge_recursive_distinct($request->getParams(), $args);
		return $this->execute($params);
	}

	public function getModuleName(): string {
		return $this->config['module'];
	}

	public function getModel() : ?Model
	{
		if ($class = $this->config['model']) {
			$instance = new $class;
			if (!is_a($instance, Model::class)) {
				throw new \Exception("Model defined for module \"{$this->getModuleName()}\" is not an instance of ".Model::class);
			}
			return $instance;
		}
		return null;
	}

	public function getContainer() : Container
	{
		return $this->container;
	}

	public function getResponse() : Response
	{
		return $this->response;
	}

	public function getRequest() : Request
	{
		return $this->request;
	}

	public function getRoute() : array
	{
		return $this->config;
	}

	protected function authorize($module, $pattern)
	{
		$class = config('system.authorizer');
		$authorizer = new $class;
		if (!($authorizer instanceof IAuthorizer)) {
			throw new \Exception("The authorizer class \"{$class}\" must be an instance of " . IAuthorizer::class);
		}
		return $authorizer->module($module, $pattern);
	}

	protected function authDenied(Request $request, Response $response, array $args)
	{
		if (Http::isHtml()) {
			$conf = config('system.onHtmlAuthDenied');
			if ($conf instanceof \Closure || is_callable($conf)) {
				return call_user_func_array($conf, [$this->config, $request, $response, $args]);
			}
		}
		return new CoreResponse("You don't have permission to access this content.");
	}

	protected function execute($params)
	{
		$action = $this->getAction($params);
		if ($action->canExecute($params)) {
			$action->validate();
			$action->execute();
			$action->setOutputData();
			return $action->getOutput();
		}
		return new CoreResponse($action->getMessageCannotExecute());
	}

	private function getAction($params) : IAction
	{
		$class = $this->config['class'];
		$action = new $class($this, $params);
		if (!($action instanceof IAction)) {
			throw new \Exception("The action class \"{$class}\" must be an instance of " . IAction::class);
		}
		return $action;
	}
}