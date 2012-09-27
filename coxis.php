<?php
if(version_compare(PHP_VERSION, '5.3.0') < 0)
	die('You need PHP ≥ 5.3');

/* ENV */
ini_set('error_reporting', E_ALL);
chdir(dirname(__FILE__));
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
	foreach($args as $key)
		if(!isset($result[$key]))
			return null;
		else
			$result = $result[$key];
	
	return $result;
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
		$result = \Coxis\Core\Error::report($msg, $e->getTrace());
	}
	else {
		$first_trace = array(array(
			'file'	=>	$e->getFile(),
			'line'	=>	$e->getLine(),
		));
		$result = \Coxis\Core\Error::report($e->getMessage(), array_merge($first_trace, $e->getTrace()));
	}
	\Coxis\Core\Response::send($result);
});
register_shutdown_function(function () {
	chdir(dirname(__FILE__));//wtf?
	#todo get the full backtrace for shutdown errors
	if($e=error_get_last()) {
		while(ob_get_level()){ ob_end_clean(); }
		$result = \Coxis\Core\Error::report("($e[type]) $e[message]<br>
			$e[file] ($e[line])".debug_backtrace(), array(array('file'=>$e['file'], 'line'=>$e['line'])));
		\Coxis\Core\Response::send($result);
	}
});