<?php
if(!defined('_ASGARD_START_'))
	define('_ASGARD_START_', time()+microtime());
set_include_path(get_include_path() . PATH_SEPARATOR . $app['kernel']['root']);

if(file_exists(__DIR__.'/helpers.php'))
	require_once __DIR__.'/helpers.php';

#Working dir
chdir(__DIR__.'/..');

#Error handler
$app['errorHandler'] = \Asgard\Debug\ErrorHandler::register()
	->ignoreDir(__DIR__.'/../vendor/nikic/php-parser/')
	->ignoreDir(__DIR__.'/../vendor/jeremeamia/SuperClosure/')
	->setLogPHPErrors($app['config']['log_php_errors']);
if($this->app['config']['log'] && $app->has('logger'))
	$app['errorHandler']->setLogger($app['logger']);
\Asgard\Debug\Debug::setURL($app['config']['debug_url']);

#Logger
$app->register('logger', function() {
	return new Logger;
});

#Translator
$app['translator'] = new \Symfony\Component\Translation\Translator($app['config']['locale'], new \Symfony\Component\Translation\MessageSelector());
$app['translator']->addLoader('yaml', new \Symfony\Component\Translation\Loader\YamlFileLoader());
foreach(glob($app['kernel']['root'].'/locales/'.$app['translator']->getLocale().'/*') as $file) {
	$app['translator']->addResource('yaml', $file, $app['translator']->getLocale());
}

#Cache
if($app['config']['cache'])
	$driver = new \Doctrine\Common\Cache\FilesystemCache(__DIR__.'/../storage/cache/');
else
	$driver = new \Asgard\Cache\NullCache();
$app['cache'] = new \Asgard\Cache\Cache($driver);

#Loading ORM and Timestamps behavior for all entities
$app['hooks']->hook('Asgard.Entity.LoadBehaviors', function($chain, &$behaviors) {
	$behaviors[] = new \Asgard\Behaviors\TimestampsBehavior;
	$behaviors[] = new \Asgard\Orm\ORMBehavior;
});

#Call start
$app['httpKernel']->start($app['kernel']['root'].'/app/start.php');

#Layout
$app['httpKernel']->filterAll('Asgard\Http\Filters\PageLayout', [
	['\General\Controllers\DefaultController', 'layout'],
	$app['kernel']['root'].'/app/general/html/html.php'
]);