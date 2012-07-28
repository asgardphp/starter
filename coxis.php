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
	while(ob_get_level()){
		ob_end_clean();
	}
		
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
	foreach($args as $key)
		if(!isset($result[$key]))
			return null;
		else
			$result = $result[$key];
	
	return $result;
}
function send($result) {
	try {
		\Coxis\Core\Event::trigger('end');
	} catch(\Exception $e) {
		Error::report($e->getMessage(), $e->getTrace());
	}
	\Coxis\Core\Response::sendHeaders($result->headers);
	echo $result->content;
	exit();
}
function __($key, $params=array()) {
	return \Coxis\Core\Locale::translate($key, $params);
}

ob_start();

/* CORE/LIBS */
require_once 'core/Autoloader.php';
spl_autoload_register(array('Coxis\Core\Autoloader', 'loadClass'));
\Coxis\Core\Autoloader::preloadDir('core');

/* ERRORS/EXCEPTIONS */
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
	throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
});
set_exception_handler(function ($e) {
	if($e instanceof \ErrorException) {
		$msg = '('.$e->getCode().') '.$e->getMessage().'<br>'.$e->getFile().' ('.$e->getLine().')';
		$result = Error::report($msg, $e->getTrace());
	}
	else {
		$first_trace = array(array(
			'file'	=>	$e->getFile(),
			'line'	=>	$e->getLine(),
		));
		$result = \Coxis\Core\Error::report($e->getMessage(), array_merge($first_trace, $e->getTrace()));
	}
	send($result);
});
register_shutdown_function(function () {
	chdir(dirname(__FILE__));//wtf?
	if($e=error_get_last()) {
		$result = \Coxis\Core\Error::report("($e[type]) $e[message]<br>
			$e[file] ($e[line])", array(array('file'=>$e['file'], 'line'=>$e['line'])));
		send($result);
	}
});

/* CONFIG */
import('Coxis\Core\Config');
\Coxis\Core\Config::loadConfigDir('config');
if(\Coxis\Core\Config::get('error_display'))
	\Coxis\Core\Error::display(true);