<?php
/**
 * Definição da lista de módulos do sistema e configuração da aplicação
 */
return [
	// Configurações da aplicação
	'config' => [
		// Classes de autenticação
		'authorizer' => '\\Core\\Auth\\Authorizer',
		'user' => '\\Core\\Auth\\User',
		// Configurações de módulo
		'defaultModule' => null, // Módulo padrão do sistema
		'defaultModuleRoute' => '/', // Rota padrão do módulo
		'defaultSecure' => true, // Rotas de módulos que não tem informação definida de segurança são seguros?
		'modelsPath' => APP_PATH . 'database/model/',
		'modulesPath' => APP_PATH . 'modules/',
		'dataNamespace' => null,
		'dataPath' => null,
		// Configuração de exibição de dados
		'debug' => false, // Quando ativado, faz com que o output de dados venha formatado para melhor visualização
		// Configurações de exibição de erro
		'errors' => [
			'display' => false,
			'backtrace' => true,
			'log' => true,
			'save_dir' => null,
			'file_name' => 'errorlog_%Y%m%d_%H.log',
			'handler' => 'ExceptionHandler'
		],
		// Configurações de banco de dados
		'db' => [
			//'dsn' => 'mysql://root:123456@localhost/rcore', OR
			//'provider' => 'mysql',
			//'host' => 'localhost',
			//'user' => 'root',
			//'pass' => '123456',
			//'base' => 'rcore',
			// Alternately you can add to define the engine type for MySQL
			// 'engine' => 'innodb',
			// You can also define things to be executed after the database is booted
			// 'afterConnect' => function(\Core\App $app, \Illuminate\Database\Capsule\Manager $capsule) {
			// }
		],
		// Configurações do roteador Slim
		'slim' => [
			'settings' => [
				'displayErrorDetails' => true,
				'determineRouteBeforeAppMiddleware' => false,
			],
			'notFoundHandler' => function ($c) {
				return function (\Slim\Http\Request $request, \Slim\Http\Response $response) use ($c) {
					return new \Core\System\Response('Caminho inválido', 404);
				};
			},
			'notAllowedHandler' => function ($c) {
				return function ($request, $response, $methods) use ($c) {
					$obj = new \Core\System\Response(['Método utilizado inválido. Somente é permitido: '. implode(', ', $methods)], 405);
					return $obj->withHeader('Allow', implode(', ', $methods));
				};
			}
		],
		// Tipo de conteúdo padrão do sistema
		'defaultContentType' => 'application/json',
		// Console
		'console' => [
			'commands' => APP_PATH . "/console/",
			'ignore' => []
		],
		// Cron Jobs
		'cron' => function(\Core\System\Cron\Cron &$scheduler) {
//			 Example:
//			 @see https://github.com/jobbyphp/jobby
//			$scheduler
//				->closure(function(){
//						echo date('Y-m-d H:i:s'), PHP_EOL;
//						return true;
//				})
//				->everyMinute()
//				->output( BASE_PATH . 'cron_log.log');
		},
		// Notificações
		// - Conjunto de notificações a serem enviadas quando a próxima execução de CRON for acionada.
		'notification' => function() {
//			Example:
//			\core\System\NotificationService::send( new MyEmailNotification("This is a message to be send by cron mail!"));
		}
	],
	// Lista de módulos do sistema
	'modules' => [
		// 'example' => ['secure' => false],
		/*'example' => [
			'secure' => true,
			'model' => 'App\\Model\\ExampleModel',
			'fields' => getSystemConfiguration('fieldsPath') . '/ExampleFields.php',
		]*/
	],
	// Internacionalização
	'i18n' => function() : array {
		if (is_file(APP_PATH . '/i18n.php')) {
			return include APP_PATH.'/i18n.php';
		}
		return [];
	}
];