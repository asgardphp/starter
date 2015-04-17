<?php
require_once '../autoload.php'; #composer autoloader

/* RUN & SEND */
$kernel = new Kernel();
if($kernel->getEnv() === 'prod')
	$kernel->setCache(new Doctrine\Common\Cache\FilesystemCache('../storage/cache'));
$kernel->load();
$kernel->getContainer()['httpKernel']->run()->send();