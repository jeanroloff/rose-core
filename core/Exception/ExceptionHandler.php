<?php

namespace Core\Exception;

use Core\System\Response;
use Illuminate\Database\QueryException;
use Slim\Http\Response as SlimResponse;

/**
 * Gerencia e controla exibição ou log de erros de sistema
 * @author Leonardo Leal da Rosa <leonardodarosa23@gmail.com>
 */
class ExceptionHandler
{
	/**
	 * Executa o tratador de exceções do sistema
	 * @param \Exception $e Exceção a ser tratada
	 */
	public static function run( \Exception $e )
	{
		$config = config('system.errors');
		if (($config['display'] || is_a( $e, Message::class )) && !($e instanceof QueryException)) {
			$obj = new Response( self::getErrorObject($e), ($e->getCode() ? $e->getCode() : 500) );
		} else {
			$obj = new Response( $e->getMessage(), 500 );
		}
		if ($config['log']) {
			self::logObject($e);
		}
		self::sendResponse( $obj );
	}

	/**
	 * Gera um objeto padrão para a exceção
	 *
	 * @param \Exception $e Exceção a ser tratada
	 * @return \stdClass
	 */
	private static function getErrorObject( $e ) : \stdClass
	{
		$config = config('system.errors');
		$error = new \stdClass;
		$error->message = $e->getMessage();
		if ($data = json_decode($error->message)) {
			$error->message = $data;
		}
		ob_start();
		$sentData = ob_get_clean();
		if ($sentData) {
			$error->data = $sentData;
		}
		if (!is_a($e,Message::class)) {
			$error->type = self::getType($e);
			$error->file = $e->getFile();
			$error->line = $e->getLine();
			if ($config['backtrace']) {
				$error->trace = str_replace("\n", "\n\t", "\n".$e->getTraceAsString())."\n  ";
			}
		}
		return $error;
	}

	/**
	 * Retorna o tipo de exceção gerada
	 *
	 * @param \Exception $e Exceção a ser tratada
	 * @return string
	 */
	private static function getType( $e ) : String
	{
		// Padrão é erro de sistema
		return 'System Error';
	}

	/**
	 * Exibe os dados da exceção na tela
	 *
	 * @param \Slim\Http\Response $response
	 */
	private static function sendResponse( SlimResponse $response )
	{
		header(sprintf(
			'HTTP/%s %s %s',
			$response->getProtocolVersion(),
			$response->getStatusCode(),
			$response->getReasonPhrase()
		));
		foreach ($response->getHeaders() as $name => $values) {
			header( sprintf('%s: %s', $name, $response->getHeaderLine($name)) );
		}
		if (config('system.errors.display')) {
			echo $response->getBody(true);
		}
	}

	/**
	 * Efetua o log do erro nos registros
	 *
	 * @param \Exception $obj
	 */
	private static function logObject( $obj )
	{
		$path = config('system.errors.save_dir');
		if ($path != null) {
			if (!is_dir($path)) {
				@mkdir($path, 0755);
			}
			$name = strftime(config('system.errors.file_name'));
			if($name != '' && @touch($path.DIRECTORY_SEPARATOR.$name)){
				@error_log(PHP_EOL . $obj . PHP_EOL, 3, $path.DIRECTORY_SEPARATOR.$name);
			} else {
				@error_log(PHP_EOL . $obj . PHP_EOL, 0);
			}
		}
	}
}