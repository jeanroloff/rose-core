<?php
/**
 * Lista de dependências das funções auxiliares
 */
use Core\Config\ApplicationConfig;
use Core\Exception\ExceptionHandler;
/**
 * Lista uma configuração
 *
 * @param string $key Caminho da configuração
 */
function config($key, $fallback=null)
{
	return ApplicationConfig::getInstance()->get($key, $fallback);
}
/**
 * Exibe o conteúdo de uma variável
 *
 * @param	mixed $var	Variável a ser exibida
 */
function dumpVariable($var)
{
	echo '<pre>', dd($var);
}
/**
 * Converte Size em bytes, kbytes...
 *
 * @param	Float	$Size	Tamanho a ser convertido
 * @return	String
 */
function convert($Size)
{
	$Unit=array('B','KB','MB','GB','TB','PB');
	return @round($Size/pow(1024,($i=floor(log($Size,1024)))),2).' '.$Unit[$i];
}
/**
 * Normaliza uma string
 * Recoloca todos os caracteres especiais
 *
 * @param String $Str String a ser convertida
 * @param Bool $RemoveSpaces Remover espaços em branco
 * @return String
 */
function normalize($Str, $RemoveSpaces=FALSE)
{
	$Ts = array("/[À-Å]/", "/Æ/", "/Ç/", "/[È-Ë]/", "/[Ì-Ï]/", "/Ð/", "/Ñ/", "/[Ò-ÖØ]/", "/×/", "/[Ù-Ü]/", "/Ý/", "/ß/", "/[à-å]/", "/æ/", "/ç/", "/[è-ë]/", "/[ì-ï]/", "/ð/", "/ñ/", "/[ò-öø]/", "/÷/", "/[ù-ü]/", "/[ý-ÿ]/");
	$Tn = array("A", "AE", "C", "E", "I", "D", "N", "O", "X", "U", "Y", "ss", "a", "ae", "c", "e", "i", "d", "n", "o", "x", "u", "y");
	$Res = preg_replace($Ts, $Tn, $Str);
	// Remover caracteres especiais
	$Res = preg_replace('/[^A-Za-z0-9\-\s]/', '', $Res);
	if ($RemoveSpaces)
		return preg_replace('/\s/','', $Res);
	return $Res;
}
/**
 * Lê um arquivo e retorna o conteúdo em pedaços
 *
 * @see	http://teddy.fr/2007/11/28/how-serve-big-files-through-php/
 * @param	String	$filename	Nome do arquivo
 * @param	Bool	$retbytes	Retornar em bytes
 * @return	Mixed
 */
// Read a file and display its content chunk by chunk
function readfile_chunked($filename, $retbytes = TRUE)
{
	$buffer = '';
	$cnt =0;
	// $handle = fopen($filename, 'rb');
	$handle = fopen($filename, 'rb');
	if ($handle === false) {
		return false;
	}
	while (!feof($handle)) {
		$buffer = fread($handle, 1024*1024);
		echo $buffer;
		ob_flush();
		flush();
		if ($retbytes) {
			$cnt += strlen($buffer);
		}
	}
	$status = fclose($handle);
	if ($retbytes && $status) {
		return $cnt; // return num. bytes delivered like readfile() does.
	}
	return $status;
}
/**
 * Converte o JSON em um formato human-readable
 *
 * @param	String	$json	JSON string
 * @param	String	$istr	Indexador (tab)
 * @return	String
 */
function jsonpp($json, $istr='  ')
{
	$result = '';
	for($p=$q=$i=0; isset($json[$p]); $p++) {
		$json[$p] == '"' && ($p>0?$json[$p-1]:'') != '\\' && $q=!$q;
		if(strchr('}]', $json[$p]) && !$q && $i--) {
			strchr('{[', $json[$p-1]) || $result .= "\n".str_repeat($istr, $i);
		}
		$result .= $json[$p];
		if(strchr(',{[', $json[$p]) && !$q) {
			$i += strchr('{[', $json[$p])===FALSE?0:1;
			strchr('}]', $json[$p+1]) || $result .= "\n".str_repeat($istr, $i);
		}
	}
	return $result;
}
/**
 * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
 * keys to arrays rather than overwriting the value in the first array with the duplicate
 * value in the second array, as array_merge does. I.e., with array_merge_recursive,
 * this happens (documented behavior):
 *
 * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
 *     => array('key' => array('org value', 'new value'));
 *
 * array_merge_recursive_distinct does not change the datatypes of the values in the arrays.
 * Matching keys' values in the second array overwrite those in the first array, as is the
 * case with array_merge, i.e.:
 *
 * array_merge_recursive_distinct(array('key' => 'org value'), array('key' => 'new value'));
 *     => array('key' => array('new value'));
 *
 * Parameters are passed by reference, though only for performance reasons. They're not
 * altered by this function.
 *
 * @param array $array1
 * @param array $array2
 * @return array
 * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
 * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
 */
function array_merge_recursive_distinct ( array $array1, array $array2 )
{
	$merged = $array1;
	foreach ( $array2 as $key => &$value ) {
		if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) ) {
			$merged [$key] = array_merge_recursive_distinct ( $merged [$key], $value );
		} else {
			$merged [$key] = $value;
		}
	}
	return $merged;
}
/**
 * Padrão para resolução de boolanos
 *
 * @param mixed $value
 * @param mixed $fallback = null
 * @return boolean
 */
function resolveBoolean($value, $fallback = null)
{
	if (is_bool($value)) {
		return $value;
	}
	switch (strtoupper($value)) {
		case 'T':	case 'TRUE':	case '1':	case 'V':
		return true;
		case 'F':	case 'FALSE':	case '0':	case 'F':
		return false;
		default:
			return $fallback;
	}
}
/**
 * Retorna a url atual completa
 *
 * @param bool $addParams Adicionar os parâmetros (querystring)
 * @return string
 */
function getFullUrl($addParams = true)
{
	if (!empty($_SERVER['HTTP_HOST'])) {
		$protocol = strpos(strtolower(@$_SERVER['SERVER_PROTOCOL']), 'https') ===
		FALSE ? 'http' : 'https';            // Get protocol HTTP/HTTPS
		$host = $_SERVER['HTTP_HOST'];   // Get  www.domain.com
		$script = dirname($_SERVER['SCRIPT_NAME']) . '/'; // Get folder/file.php
		$params = $_SERVER['QUERY_STRING'];// Get Parameters occupation=odesk&name=ashik
		if ($addParams) {
			return $protocol . '://' . str_replace("//", "/", $host . $script . '?' . $params);
		}
		return $protocol . '://' . str_replace("//", "/", $host . $script);
	}
	return null;
}
/**
 * Executa uma função definida pelo usuário com os argumentos variáveis
 *
 * @return Mixed
 */
function call($fn, ...$args)
{
	return call_user_func_array($fn, $args);
}

/**
 * Global exception handler on system
 *
 * @param Exception $e
 */
function exceptionHandler($e)
{
	if (is_a($e, Exception::class)) {
		ExceptionHandler::run($e);
	} else {
		echo $e;
	}
}