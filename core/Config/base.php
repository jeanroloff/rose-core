<?php
// Validation to the current PHP Version
if (version_compare(PHP_VERSION, "7.1.0","<")) {
	die("This application needs PHP 7.1 or above to run properly.");
}
// Path padrão do core da Aplicação
define('CORE_PATH', realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR );
define('BASE_PATH', realpath( CORE_PATH . '/../' ) . DIRECTORY_SEPARATOR );
define('APP_PATH', realpath( BASE_PATH . '/app/' ) . DIRECTORY_SEPARATOR );
// Definir o timezone atual
date_default_timezone_set( 'America/Sao_Paulo' );
setlocale( LC_ALL, "pt_BR", "pt_BR.iso-8859-1", "pt_BR.utf-8", "portuguese" );
// Inicializar as funções base do sistema
require_once CORE_PATH . '/includes/functions.php';
// URL base da aplicação
define('BASE_URL', getFullUrl(false));
// Definições de path gerais
define('TEMPLATE_PATH', APP_PATH.'/template/');