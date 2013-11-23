<?php
if(!function_exists('d')) {
	function d() {
		call_user_func_array(array('Coxis\Utils\Debug', 'd'), func_get_args());
	}
}
if(!function_exists('__')) {
	function __($key, $params=array()) {
		return \Coxis\Core\Context::get('locale')->translate($key, $params);
	}
}
if(!function_exists('from')) {
	function from($from='') {
		return new \Coxis\Core\Importer($from);
	}
}
if(!function_exists('import')) {
	function import($what, $into='') {
		return from()->import($what, $into);
	}
}