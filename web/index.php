<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

require_once '../autoload.php'; #composer autoloader

/* RUN & SEND */
$kernel = new Kernel();
if($kernel->getEnv() === 'prod')
	$kernel->setCache(new Doctrine\Common\Cache\FilesystemCache('../storage/cache'));
$kernel->load();
$kernel->getContainer()['httpKernel']->run()->send();