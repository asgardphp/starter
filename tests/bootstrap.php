<?php
require_once __DIR__.'/../paths.php';
if(!defined('_ENV_'))
	define('_ENV_', 'test');
require_once _VENDOR_DIR_.'autoload.php';
\Asgard\Core\App::loadDefaultApp();

\Asgard\Core\App::get('schema')->dropAll();
\Asgard\Orm\Libs\MigrationsManager::migrateAll(false);
