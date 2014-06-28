<?php
define('_ENV_', 'dev');
if(version_compare(PHP_VERSION, '5.4.0') < 0)
	die('You need PHP ≥ 5.4');

if(!defined('_ENV_')) {
	if(isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] == '127.0.0.1' || $_SERVER['HTTP_HOST'] == 'localhost'))
		define('_ENV_', 'dev');
	else
		define('_ENV_', 'prod');
}

require_once '../autoload.php'; #composer autoloader

/* RUN & SEND */
$kernel = new Kernel();
$kernel->load();
$kernel->getContainer()['httpKernel']->run()->send();