<?php
require_once '../autoload.php'; #composer autoloader

/* RUN & SEND */
$kernel = new Kernel();
if($kernel->getEnv() === 'prod') {
	$cache = new Doctrine\Common\Cache\ApcCache;
	$cache->setNamespace($kernel['root']);
	$kernel->setCache($cache);
}
$kernel->load();
$kernel->getContainer()['httpKernel']->run()->send();