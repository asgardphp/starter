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
if(!function_exists('from')) {
	function from($from='') {
		return new \Asgard\Core\Importer($from);
	}
}
if(!function_exists('import')) {
	function import($what, $into='') {
		return from()->import($what, $into);
	}
}

\Asgard\Core\Autoloader::$directories['Asgard'] = 'bundles';
\Asgard\Core\Autoloader::$directories['Psr\Log'] = 'log/Psr/Log';
\Asgard\Core\App::set('logger', function() {
	return new \App\Logger;
});