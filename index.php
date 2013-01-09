<?php
if(!defined('_ENV_'))
	if(isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] == '127.0.0.1' || $_SERVER['HTTP_HOST'] == 'localhost'))
		define('_ENV_', 'dev');
	else
		define('_ENV_', 'prod');

/* INIT */
require('coxis.php');

/* RUN AND SEND */
\Coxis\Core\Controller::run('Coxis\Core\Front', 'main')->send();
