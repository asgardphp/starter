<?php
if(!defined('_ASGARD_START_'))
	define('_ASGARD_START_', time()+microtime());
set_include_path(get_include_path() . PATH_SEPARATOR . _DIR_);

#Utils
if(!function_exists('d')) {
	function d() {
		call_user_func_array(array('Asgard\Utils\Debug', 'dWithTrace'), array_merge(array(debug_backtrace()), func_get_args()));
	}
}
if(!function_exists('__')) {
	function __($key, $params=array()) {
		return \Asgard\Core\App::get('translator')->trans($key, $params);
	}
}

#Error handler
\Asgard\Core\ErrorHandler::initialize();

#Asgard autoloader
\Asgard\Core\App::register('autoloader', function() {
	$autoloader = new \Asgard\Core\Autoloader;
	$autoloader->globalNamespace(\Asgard\Core\App::get('config')->get('global_namespace'));
	$autoloader->preload(\Asgard\Core\App::get('config')->get('preload'));
	return $autoloader;
});
spl_autoload_register(array(\Asgard\Core\App::get('autoloader'), 'autoload')); #asgard autoloader
\Asgard\Core\App::get('autoloader')->namespaceMap('Asgard', 'bundles');
\Asgard\Core\App::get('autoloader')->namespaceMap('Psr\Log', 'log/Psr/Log');
\Asgard\Core\App::set('logger', function() {
	return new \App\Logger;
});

#Loading ORM and Timestamps behavior for all entities
\Asgard\Core\App::get('hook')->hook('behaviors_pre_load', function($chain, $entityDefinition) {
	if(!isset($entityDefinition->behaviors['Asgard\Behaviors\TimestampsBehavior']))
		$entityDefinition->behaviors['Asgard\Behaviors\TimestampsBehavior'] = true;

	if(!isset($entityDefinition->behaviors['orm']))
		$entityDefinition->behaviors['Asgard\Orm\ORMBehavior'] = true;
});