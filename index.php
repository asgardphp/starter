<?php
if(!defined('_ENV_')) {
	if(isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] == '127.0.0.1' || $_SERVER['HTTP_HOST'] == 'localhost'))
		define('_ENV_', 'dev');
	else
		define('_ENV_', 'prod');
}

require 'paths.php';

/* INIT */
require _CORE_DIR_.'core.php';

/* RUN AND SEND */
Asgard\Core\HttpKernel::run();
