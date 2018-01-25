<?php
/**
 * Arquivo de definição de mapeamento do módulo "Usuário"
 * Deve retornar um array contendo:
 * @example [
		['pattern' => '/', 'method' => 'GET', 'class' => '\\RCore\\Action\\Report'],
		['pattern' => '/{id}', 'method' => 'GET','class' => '\\RCore\\Action\\View'],
 * ]
 */
return [
	['pattern' => '/', 'method' => 'GET', 'class' => \Core\Action\Report::class],
	['pattern' => '/new', 'method' => 'POST','class' => 	\Core\Action\Insert::class],
	['pattern' => '/{id}/view', 'method' => 'GET', 'class' => \Core\Action\View::class, 'view' => 'view.twig'],
	['pattern' => '/{id}/edit', 'method' => 'PATCH', 'class' => \Core\Action\Edit::class],
	['pattern' => '/{id}/delete', 'method' => 'DELETE', 'class' => \Core\Action\Delete::class]
];
