<?php
define('_ENV_', 'test');

require_once __DIR__.'/../autoload.php';
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__);

$kernel = new \Kernel(dirname(__DIR__));
$kernel->load();
$container = $kernel->getContainer();

#WARNING: be sure to configurate a test database because you uncomment these lines
// $container['schema']->dropAll();
// $mm = new \Asgard\Migration\MigrationManager($container['kernel']['root'].'/migrations/', $container['db'], $container['schema'], $container);
// $mm->migrateAll();

if(!defined('_TESTING_')) {
	define('_TESTING_', $container['kernel']['root'].'/tests/tested.txt');
	\Asgard\File\FileSystem::delete(_TESTING_);
}
