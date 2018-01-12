<?php

/* 
 * Arquivo de exemplo de configurações do usuário
 * As configurações informadas aqui serão acessíveis através da função "getSystemConfig()"
 */

return [
	// Dados da conexão padrão ao banco de dados e adicionais para o ambiente atual
	'db' => [
		'dsn' => 'mysql://root@localhost/rcore',
	],
	// Configuração de exibição de dados
	'debug' => true, // Quando ativado, faz com que o output de dados venha formatado para melhor visualização,
	'auditData' => false, // Salvar os registros de audit de execução
	// Exibição de erros por padrão é desligado
	// Utilize esta configuração para exibir as mensagens geradas pelo sistema em ambiente de desenvolvimento
	// e para ajustar o diretório onde os erros serão salvos
	'errors' => [
		'display' => true,
		'log' => true,
		'backtrace' => true,
		'save_dir' => BASE_PATH . '/Tmp/Error'
	]
];