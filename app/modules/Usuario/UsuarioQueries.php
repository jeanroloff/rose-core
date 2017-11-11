<?php
/**
 * Arquivo de definição de queries do módulo de Usuario
 * Deve retornar um array contendo:
 * @example 
	return [
		'example' => "
			SELECT
				*
			FROM
				dual
		"
	];
 */
return [
	// Query utilizada para autenticação do usuário atual
	'login' => "
		SELECT
			u.*
		FROM
			usuario u 
		WHERE
			u.nome_usuario = :username AND
			u.senha = MD5(:password) 
	",
	// Query utilizada para Exibição da lista na rota "/usuario"
	'list' => "
		SELECT
			*
		FROM
			usuario
	",
	// Query utilizada para exibição de dados de um registro específico
	'view' => "
		SELECT
			*
		FROM
			usuario
		WHERE
			id_usuario = :id_usuario
	"
];