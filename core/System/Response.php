<?php

namespace Core\System;

use Illuminate\Support\Str;
use Slim\Http\Response as SlimResponse;
use Slim\Interfaces\Http\HeadersInterface;

/**
 * Classe de resposta padrão do sistema.
 * @author Leonardo Leal da Rosa <leonardodarosa23@gmail.com>
 */
class Response extends SlimResponse
{
	/**
	 * Construtor da classe
	 * Define os parâmetros necessários para a resposta do sistema
	 *
	 * @param Mixed $data Dados para resposta em tela
	 * @param Int $status Status HTTP
	 * @param \Slim\Interfaces\Http\HeadersInterface $headers
	 */
	public function __construct($data, $status = 200, HeadersInterface $headers = null)
	{
		parent::__construct($status, $headers);
		$this->status = $status;
		$this->headers->add('Content-Type', 'application/json;charset=utf-8');
		$this->setData($data);
		$this->display = false;
	}

	/**
	 * Define o corpo da resposta
	 *
	 * @param Mixed $data Dados para resposta em tela
	 * @throws \RuntimeException
	 */
	private function setData($data)
	{
		// Não exibir somente strings
		if (is_string($data)) {
			$data = [$data];
		}
		// Setar o JSON
		$body = $this->getBody();
		$body->rewind();
		$json = json_encode($data);
		if (config('system.debug', false)) {
			$json = jsonpp( str_replace(['\n','\\\\','\t'],["\n",'\\',"\t"], $json ) );
		}
		$body->write($json);
		// Ensure that the json encoding passed successfully
		if ($json === false) {
			throw new \RuntimeException(json_last_error_msg(), json_last_error());
		}
	}
}