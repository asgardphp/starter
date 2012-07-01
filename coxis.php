<?php
if(version_compare(PHP_VERSION, '5.3.0') < 0)
	die('You need PHP > 5.3');

/* ENV */
ini_set('error_reporting', E_ALL);
chdir(dirname(__FILE__));
define('_WEB_DIR_', 'web');#todo: remove..
	
if(!defined('_ENV_'))
	if(isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] == '127.0.0.1' || $_SERVER['HTTP_HOST'] == 'localhost') || php_sapi_name() == 'cli')
		define('_ENV_', 'dev');
	else
		define('_ENV_', 'prod');

/* UTILS */
function d() {
	if(ob_get_length() > 0)
		ob_end_clean();
		
	if(php_sapi_name() == 'cli')
		echo '<pre>';
		
	foreach(func_get_args() as $arg)
		var_dump($arg);
		
	if(php_sapi_name() == 'cli')
		echo '</pre>';
	
	Error::print_backtrace('', debug_backtrace());
		
	exit();
}
function access() {	
	$args = func_get_args();
	$result = array_shift($args);
	foreach($args as $key)
		if(!isset($result[$key]))
			return null;
		else
			$result = $result[$key];
	
	return $result;
}
function send($result) {
	Response::sendHeaders($result->headers);
	
	echo $result->content;
	exit();
}

ob_start();

/* CORE/LIBS */
require_once('core/Coxis.php');
spl_autoload_register(array('Coxis', 'loadClass'));
Coxis::preLoadClasses('core');

/* ERRORS/EXCEPTIONS */
class PHPErrorException extends Exception {}
function errorHandler($errno, $errstr, $errfile, $errline) {
	throw new PHPErrorException("($errno) $errstr<br>
	$errfile ($errline)");
	//todo add file and line to stack & death loop exceptions
}
function exceptionHandler($e) {
	if(is_a($e, 'EndException'))
		$result = $e->result;
	elseif(is_a($e, 'PHPErrorException'))
		$result = Error::report($e->getMessage(), $e->getTrace());
	else {
		$first_trace = array(array(
			'file'	=>	$e->getFile(),
			'line'	=>	$e->getLine(),
		));
		$result = Error::report($e->getMessage(), array_merge($first_trace, $e->getTrace()));
	}
	
	send($result);
}
//todo cannot handle multiple requests with this thing
function shutdown() {
	chdir(dirname(__FILE__));//wtf?

	if($e=error_get_last()) {
		$result = Error::report("($e[type]) $e[message]<br>
			$e[file] ($e[line])", array(array('file'=>$e['file'], 'line'=>$e['line'])));
		send($result);
	}
}
set_error_handler('errorHandler');
set_exception_handler('exceptionHandler');
register_shutdown_function('shutdown');

/* CONFIG */
Config::loadConfigFile('config.php');
if(Config::get('error_display'))
	Error::policy(true);