<?php
if(version_compare(PHP_VERSION, '5.3.0') < 0)
	die('You need PHP ≥ 5.3');

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
		
	if(php_sapi_name() != 'cli')
		echo '<pre>';
	foreach(func_get_args() as $arg)
		var_dump($arg);
	if(php_sapi_name() != 'cli')
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
require_once 'core/Autoloader.php';
spl_autoload_register(array('Coxis\Core\Autoloader', 'loadClass'));
Autoloader::preloadDir('core');

/* ERRORS/EXCEPTIONS */
class PHPErrorException extends Exception {
	public $errno, $errstr, $errfile, $errline;
}
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
	$e = new PHPErrorException();
	$e->errno = $errno;
	$e->errstr = $errstr;
	$e->errfile = $errfile;
	$e->errline = $errline;
	throw $e;
});
set_exception_handler(function ($e) {
	if(is_a($e, 'PHPErrorException')) {
		$msg = '('.$e->errno.') '.$e->errstr.'<br>'.$e->errfile.' ('.$e->errline.')';
		$result = Error::report($msg, $e->getTrace());
	}
	else {
		$first_trace = array(array(
			'file'	=>	$e->getFile(),
			'line'	=>	$e->getLine(),
		));
		$result = Error::report($e->getMessage(), array_merge($first_trace, $e->getTrace()));
	}
	send($result);
});
register_shutdown_function(function () {
	chdir(dirname(__FILE__));//wtf?
	if($e=error_get_last()) {
		$result = Error::report("($e[type]) $e[message]<br>
			$e[file] ($e[line])", array(array('file'=>$e['file'], 'line'=>$e['line'])));
		send($result);
	}
});

/* CONFIG */
Config::loadConfigDir('config');
if(Config::get('error_display'))
	Error::display(true);