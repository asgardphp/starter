<?php
if(!defined('_ENV_'))
	define('_ENV_', 'test');

require_once __DIR__.'/../vendor/autoload.php';
foreach(spl_autoload_functions() as $function) {
	if(is_array($function) && $function[0] instanceof \Composer\Autoload\ClassLoader)
		$function[0]->setUseIncludePath(true);
}
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__.'/../app');

$kernel = new \Kernel(dirname(__DIR__));
$kernel->load();
$app = $kernel->getApp();

$app['schema']->dropAll();
$mm = new \Asgard\Migration\MigrationsManager($app['kernel']['root'].'/Migrations/', $app);
$mm->migrateAll(false);
