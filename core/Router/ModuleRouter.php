<?php

namespace Core\Router;


use Core\Controller\Controller;
use Core\Controller\IController;
use Slim\App as Slim;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Slim\Http\Request;
use Slim\Http\Response;

class ModuleRouter
{
	private $slim;

	private $module;

	private $routes;

	private $controllerName;

	private $secure;

	private $appendTo;

	private $isDefaultModule;

	private $defaultRoute;

	public function __construct(Slim $slim, array $module)
	{
		$this->slim = $slim;
		$this->module = $module;
		$this->initialize();
	}

	public function execute()
	{
		if (!empty($this->defaultRoute)) {
			$this->addRoute($this->defaultRoute);
		}
		foreach ($this->routes as $route) {
			$this->addRoute($route);
		}
	}

	private function initialize()
	{
		if (!Arr::exists($this->module,'secure')) {
			$this->secure = config('system.defaultSecure');
		}
		if (!Arr::exists($this->module,'controller')) {
			$this->controllerName = Controller::class;
		}
		if (!Arr::exists($this->module,'appendModuleToPattern')) {
			$this->appendTo = true;
		}
		if (config('system.defaultModule') == $this->module['name']) {
			$this->isDefaultModule = true;
		}
		$this->readRoutes();
	}

	private function readRoutes()
	{
		$file = Str::ucfirst(Str::camel($this->module['name']))."Map.php";
		$routes = include $this->module['path'] .DIRECTORY_SEPARATOR. $file;
		if (empty($routes)) {
			throw new \Exception("Unable to read routes for module \"{$this->module['name']}\".");
		}
		foreach ($routes as $k => $route) {
			$routes[$k] = $this->parseRoute($route);
		}
		$this->routes = $routes;
	}

	private function parseRoute($route)
	{
		$route['module'] = $this->module['name'];
		$route['base_pattern'] = $route['pattern'];
		if ($route['method'] == '*'){
			$route['method'] = 'GET,POST,PUT,DELETE';
		}
		if ($route['pattern'] == '/') {
			$route['pattern'] = '[/]';
		}
		if (!isset($route['secure'])) {
			$route['secure'] = $this->secure;
		}
		if ($this->isDefaultModule && $route['pattern'] == config('system.defaultModuleRoute')) {
			$conf = $route;
			$conf['pattern'] = "";
			$this->defaultRoute = $conf;
		}
		if ($this->appendTo) {
			$route['pattern'] = $this->module['name'].$route['pattern'];
		}
		return $route;
	}

	private function addRoute($config)
	{
		$router = $this;
		$methods = explode(",", $config['method']);
		$routePath = $config['pattern'];
		if ($routePath != '[/]') {
			$routePath = substr($config['pattern'], 0, 1) == '/' ? $config['pattern'] : "/{$config['pattern']}";
			if (!in_array(substr($routePath, -1), [']', '/'])) {
				$routePath .= "[/]";
			}
		}
		$this->slim->map($methods, $routePath, function(Request $request, Response $response, array $args) use ($config, $router) {
			$c = new $router->controllerName(array_merge_recursive_distinct($router->module, $config), $this);
			if (!($c instanceof IController)) {
				throw new \Exception("The class \"{$router->controllerName}\" must implement " . IController::class);
			}
			return $c($request, $response, $args);
		});
	}
}