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
			'save_dir' => CORE_PATH . '/../',
			'file_name' => 'errorlog_%Y%m%d_%H.log',
			'db_file_name' => 'dberrorlog_%Y%m%d_%H.log',
			'handler' => 'exceptionHandler'
		],
		// Configurações de banco de dados
		'db' => [
			//'dsn' => 'mysql://root:123456@localhost/rcore', OR
			//'provider' => 'mysql',
			//'host' => 'localhost',
			//'user' => 'root',
			//'pass' => '123456',
			//'base' => 'rcore'
			// Alternately you can add to define the engine type for MySQL
			//'engine' => 'innodb'
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
		]
	],
	// Lista de módulos do sistema
	'modules' => [
		// 'example' => ['secure' => false],
		/*'example' => [
			'secure' => true,
			'model' => 'App\\Model\\ExampleModel',
			'fields' => getSystemConfiguration('fieldsPath') . '/ExampleFields.php',
		]*/
	]
];