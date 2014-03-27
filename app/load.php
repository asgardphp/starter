<?php
if(!function_exists('d')) {
	function d() {
		call_user_func_array(array('Asgard\Utils\Debug', 'dWithTrace'), array_merge(array(debug_backtrace()), func_get_args()));
	}
}
if(!function_exists('__')) {
	function __($key, $params=array()) {
		return \Asgard\Core\App::get('locale')->translate($key, $params);
	}
}

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