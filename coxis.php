<?php
if(version_compare(PHP_VERSION, '5.3.0') < 0)
	die('You need PHP ≥ 5.3');

/* ENV */
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1); #todo a verifier
// chdir(dirname(__FILE__));
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));
define('_DIR_', dirname(__FILE__).'/');
// die(_DIR_);
define('_WEB_DIR_', 'web');#todo: remove..

/* UTILS */
function d() {
	while(ob_get_level()){ ob_end_clean(); }
		
	if(php_sapi_name() != 'cli')
		echo '<pre>';
	foreach(func_get_args() as $arg)
		var_dump($arg);
	if(php_sapi_name() != 'cli')
		echo '</pre>';
	
	\Coxis\Core\Error::print_backtrace('', debug_backtrace());
	exit();
}
function get() {
	$args = func_get_args();
	$result = array_shift($args);
	$args = \Coxis\Core\Tools\Tools::flateArray($args);
	foreach($args as $key)
		if(!isset($result[$key]))
			return null;
		else
			$result = $result[$key];
	
	return $result;
}
function __($key, $params=array()) {
	return \Locale::translate($key, $params);
}

ob_start();

/* CORE/LIBS */
require_once 'core/IoC.php';
require_once 'core/Context.php';
require_once 'core/Importer.php';
require_once 'core/Autoloader.php';

spl_autoload_register(array(\Coxis\Core\Context::get('autoloader'), 'loadClass'));
\Coxis\Core\Context::get('autoloader')->preloadDir(_DIR_.'core');

/* ERRORS/EXCEPTIONS */
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
	if($errno <= \Memory::get('errno'))#todo
		throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
});
set_exception_handler(function ($e) {
	\Coxis\Core\Coxis::getExceptionResponse($e)->send();
});
register_shutdown_function(function () {
	if(\Config::get('no_shutdown_error'))
		exit();
	chdir(dirname(__FILE__));//wtf?
	#todo get the full backtrace for shutdown errors
	if($e=error_get_last()) {
		while(ob_get_level()){ ob_end_clean(); }
		$response = \Coxis\Core\Error::report("($e[type]) $e[message]<br>
			$e[file] ($e[line])".debug_backtrace(), array(array('file'=>$e['file'], 'line'=>$e['line'])));
		\Response::send($response);
	}
});

if(!defined('_ENV_'))
	if(\Server::get('HTTP_HOST') == '127.0.0.1' || \Server::get('HTTP_HOST') == 'localhost')
		define('_ENV_', 'dev');
	else
		define('_ENV_', 'prod');