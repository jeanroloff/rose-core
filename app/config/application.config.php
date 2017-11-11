<?php
// Configurações da estação de trabalho
$stationInfo = is_file(APP_PATH . '/config/station.php') ? include_once APP_PATH . '/config/station.php' : [];
/**
 * Definição da lista de módulos do sistema e configuração da aplicação
 */
return [
	// As configurações de aplicação podem ser alteradas pelo arquivo de configuração por usuário
	'config' => array_merge_recursive_distinct([
		// Classes de autenticação
//		'authorizer' => '\App\Auth\Authorizer',
//		'user' => '\App\Auth\User',
		'defaultModule' => 'usuario',
		'defaultModuleRoute' => '[/]',
		'defaultSecure' => false, // Rotas de módulos que não tem informação definida de segurança são seguros?
		// Dados de conexão ao banco de dados
		'db' => [
			'dsn' => 'mysql://root:123456@127.0.0.1/core',
		],
		// Tipo de conteúdo padrão do sistema
		'defaultContentType' => 'text/html',
//		'defaultContentType' => 'application/json',
		// Configurações do site
		'onInitialize' => function( \Core\App $app) {
			if (is_file(APP_PATH . '/functions.php')) {
				include_once APP_PATH.'/functions.php';
			}
			if (is_file(APP_PATH . '/defines.php')) {
				include_once APP_PATH.'/defines.php';
			}
		},
		// Gestão de erro de autenticação
		'onHtmlAuthDenied' => function() : \Core\System\Response {
			return new \Core\System\Response('Você não tem permissão para acessar este conteúdo.', 401);
		},
		// Configurações do roteador Slim
		'slim' => [
			'notFoundHandler' => function ($c) {
				return function (\Slim\Http\Request $request, \Slim\Http\Response $response) use ($c) {
					return new \Core\System\Response('Caminho inválido', 404);
				};
			},
			'notAllowedHandler' => function ($c) {
				return function (\Slim\Http\Request $request, \Slim\Http\Response $response, $methods) use ($c) {
					$obj = new \Core\System\Response(['Método utilizado inválido. Somente é permitido: '. implode(', ', $methods)], 405);
					return $obj->withHeader('Allow', implode(', ', $methods));
				};
			}
		],
		// Template
		'templatePath' => function(\Core\Action\Action $action) : ?string {
			return null;
		}
	], $stationInfo),
	// Lista de módulos do sistema
	'modules' => [
		'usuario' => [ 'path' => BASE_PATH . 'app/modules/Usuario', 'model' => \App\Model\UsuarioModel::class ]
	]
];