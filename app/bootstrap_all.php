<?php
if(!defined('_ASGARD_START_'))
	define('_ASGARD_START_', time()+microtime());
set_include_path(get_include_path() . PATH_SEPARATOR . $container['kernel']['root']);

if(file_exists(__DIR__.'/helpers.php'))
	require_once __DIR__.'/helpers.php';

#Working dir
chdir(__DIR__.'/..');

#Error handler
$container['errorHandler'] = \Asgard\Debug\ErrorHandler::register()
	->ignoreDir(__DIR__.'/../vendor/nikic/php-parser/')
	->ignoreDir(__DIR__.'/../vendor/jeremeamia/SuperClosure/')
	->setLogPHPErrors($container['config']['log_php_errors']);
if($this->container['config']['log'] && $container->has('logger'))
	$container['errorHandler']->setLogger($container['logger']);
\Asgard\Debug\Debug::setURL($container['config']['debug_url']);

#Logger
$container->register('logger', function() {
	return new Logger;
});

#Translator
$container['translator'] = new \Symfony\Component\Translation\Translator($container['config']['locale'], new \Symfony\Component\Translation\MessageSelector());
$container['translator']->addLoader('yaml', new \Symfony\Component\Translation\Loader\YamlFileLoader());
foreach(glob($container['kernel']['root'].'/translations/'.$container['translator']->getLocale().'/*') as $file)
	$container['translator']->addResource('yaml', $file, $container['translator']->getLocale());

#Cache
if($container['config']['cache'])
	$driver = new \Doctrine\Common\Cache\FilesystemCache(__DIR__.'/../storage/cache/');
else
	$driver = new \Asgard\Cache\NullCache();
$container['cache'] = new \Asgard\Cache\Cache($driver);

#Loading ORM and Timestamps behavior for all entities
$container['hooks']->hook('Asgard.Entity.LoadBehaviors', function($chain, &$behaviors) {
	$behaviors[] = new \Asgard\Behaviors\TimestampsBehavior;
	$behaviors[] = new \Asgard\Orm\ORMBehavior;
});

#Call start
$container['httpKernel']->start($container['kernel']['root'].'/app/start.php');

#Layout
$container['httpKernel']->filterAll('Asgard\Http\Filters\PageLayout', [
	['\General\Controllers\DefaultController', 'layout'],
	$container['kernel']['root'].'/app/General/html/html.php'
]);

#Remove trailing / and www.
$container['hooks']->hook('Asgard.Http.Start', function($chain, $request) {
	$oldUrl = $request->url;
	$newUrl = clone $oldUrl;

	if(preg_match('/^www./', $oldUrl->host()))
		$newUrl->setHost(preg_replace('/^www./', '', $oldUrl->host()));
	if(($url=rtrim($oldUrl->get(), '/')) !== $oldUrl->get())
		$newUrl->setURL($url);

	if($newUrl->full() !== $oldUrl->full())
		return (new \Asgard\Http\Response())->redirect($newUrl->full());
});