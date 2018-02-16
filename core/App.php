<?php
namespace Core;

use Core\Config\DbConfig;
use Core\Exception\EmptyConfigException;
use Core\Http\Http;
use Core\Router\AppRouter;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Str;
use Slim\App as Slim;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

require __DIR__ . '/Config/base.php';

class App
{
	/**
	 * Instance of Slim App
	 * @var \Slim\App
	 */
	public $slim;

	private $capsule;

	private $INIT_TIME;

	private function __construct()
	{
		$this->INIT_TIME = microtime(true);
		set_exception_handler(config('system.errors.handler'));
		$this->registerDatabase();
		$this->buildSlim();
		$this->initialize();
	}

	public static function &getInstance() : App
	{
		static $instance;
		if (!isset($instance)) {
			$instance = new App;
		}
		return $instance;
	}

	public function run()
	{
		$this->slim->run();
	}

	public function getElapsed()
	{
		$elapsed = microtime(true) - $this->INIT_TIME;
		return sprintf("%.3f", $elapsed);
	}

	private function initialize()
	{
		$this->router = new AppRouter( $this );
		$this->router->execute();
		$this->onInitialize();
	}

	private function onInitialize()
	{
		$callback = config('system.onInitialize');
		if (is_callable($callback)) {
			call_user_func_array($callback, [$this]);
		} else if($callback instanceof \Closure) {
			$callback($this);
		}
	}

	private function buildSlim()
	{
		if (!isset($this->slim)) {
			$this->slim = new Slim( config('system.slim') );
			unset($this->slim->getContainer()['errorHandler']);
			$this->registerView();
		}
	}

	private function registerView()
	{
		$container = $this->slim->getContainer();
		$container['view'] = function ($container) {
			$view = new Twig( BASE_PATH );
			$basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
			$view->addExtension(new TwigExtension($container['router'], $basePath));
			$this->registerFilters($view);
			$this->setTwigIncludes($view);
			return $view;
		};
	}

	private function registerFilters(&$view)
	{
		$functions = get_defined_functions()['user'];
		foreach ($functions as $name) {
			$view->getEnvironment()->addFilter( new \Twig_SimpleFilter( $name, $name ) );
			$view->getEnvironment()->addFunction( new \Twig_SimpleFunction( $name, $name ) );
		}
	}

	private function setTwigIncludes(&$view)
	{
		global $twig;
		$twig = $view;
		foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(BASE_PATH)) as $file) {
			if (stripos($file, "twigincludes.php")!==false) {
				include_once $file;
			}
		}
	}

	private function registerDatabase()
	{
		try {
			$config = DbConfig::getInstance();
			$this->capsule = new Capsule;
			$this->capsule->addConnection(array_merge_recursive_distinct( $config->toArray(), [
				'charset' => 'utf8',
				'collation' => 'utf8_unicode_ci',
				'engine' => config('system.db.engine', null)
			]));
			$this->capsule->bootEloquent();
			$this->capsule->setAsGlobal();
		} catch (EmptyConfigException $e){}
		$afterConnect = config('system.db.afterConnect');
		if ($afterConnect instanceof \Closure) {
			call_user_func_array($afterConnect, [$this, $this->capsule]);
		}
	}

	public function getCapsule() : Capsule
	{
		return $this->capsule;
	}
}