<?php
if(!defined('_ENV_'))
	define('_ENV_', 'test');

require_once __DIR__.'/../autoload.php';

$kernel = new \Kernel(dirname(__DIR__));
$kernel->load();
$app = $kernel->getContainer();

$app['schema']->dropAll();
$mm = new \Asgard\Migration\MigrationsManager($app['kernel']['root'].'/migrations/', $app);
$mm->migrateAll(false);

if(!defined('_TESTING_')) {
	define('_TESTING_', $app['kernel']['root'].'/tests/tested.txt');
	\Asgard\File\FileSystem::delete(_TESTING_);
}
