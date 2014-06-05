<?php
if(!defined('_ASGARD_START_'))
	define('_ASGARD_START_', time()+microtime());
set_include_path(get_include_path() . PATH_SEPARATOR . $app['kernel']['root']);

#Utils
if(!function_exists('d')) {
	function d() {
		if(\Asgard\Core\App::instance()['config']['debug'] === false)
			return;
		$app = \Asgard\Core\App::instance();
		$request = $app->has('request') ? $app['request']:new \Asgard\Http\Request();
		call_user_func_array(array('Asgard\Debug\Debug', 'dWithTrace'), array_merge(array($request, debug_backtrace()), func_get_args()));
	}
}
if(!function_exists('__')) {
	function __($key, $params=array()) {
		return \Asgard\Core\App::instance()->get('translator')->trans($key, $params);
	}
}

#Error handler
\Asgard\Debug\ErrorHandler::initialize($app)
	->ignoreDir(__DIR__.'/../vendor/nikic/php-parser/')
	->ignoreDir(__DIR__.'/../vendor/jeremeamia/SuperClosure/');

#Autoloader
foreach(spl_autoload_functions() as $function) {
	if(is_array($function) && $function[0] instanceof \Composer\Autoload\ClassLoader)
		$function[0]->setUseIncludePath(true);
}
set_include_path(get_include_path() . PATH_SEPARATOR . 'app');
$app->register('autoloader', function($app) {
	$autoloader = new \Asgard\Core\Autoloader;
	$autoloader->goUp($app['config']['global_namespace']);
	$autoloader->preload($app['config']['preload']);
	return $autoloader;
});
spl_autoload_register(array($app['autoloader'], 'autoload')); #asgard autoloader
$app['autoloader']->namespaceMap('Psr\Log', 'log/Psr/Log');

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