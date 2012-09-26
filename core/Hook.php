<?php
namespace Coxis\Core;

class Hook {
	private static $registry = array();

	#cannot use references with get_func_args
	public static function trigger($name, $args=array(), $cb=null, &$filter1=null, &$filter2=null, &$filter3=null, &$filter4=null, &$filter5=null, &$filter6=null,
		&$filter7=null, &$filter8=null, &$filter9=null, &$filter10=null) {
		return static::triggerChain(new hookChain, $name, $args, $cb, $filter1, $filter2, $filter3, $filter4, $filter5, $filter6,
			$filter7, $filter8, $filter9, $filter10);
	}

	#cannot use references with get_func_args
	public static function triggerChain($chain, $name, $args=array(), $cb=null, &$filter1=null, &$filter2=null, &$filter3=null, &$filter4=null, &$filter5=null, &$filter6=null,
		&$filter7=null, &$filter8=null, &$filter9=null, &$filter10=null) {
		if(count(func_get_args()) > 14)
			throw new \Exception("triggerChain() can only accept up to 14 arguments");

		// if($name == "coxis_\\Coxis\\App\\Actualite\\Models\\Actualite_actions")
		// 	d($name, static::get(array()));

		if(is_string($name))
			$name = explode('_', $name);

		$chain->calls = array_merge(
			static::get(array_merge($name, array('before'))),
			$cb !== null ? array($cb):array(),
			static::get(array_merge($name, array('on'))),
			static::get(array_merge($name, array('after')))
		);

		$filters = array(&$filter1, &$filter2, &$filter3, &$filter4, &$filter5, &$filter6, &$filter7, &$filter8, &$filter9, &$filter10);
		
		if(!is_array($args))
			$args = array($args);

		return $chain->run($args, $filters);
	}

	private static function set($path, $cb) {
		$arr =& static::$registry;
		$key = array_pop($path);
		foreach($path as $next)
			$arr =& $arr[$next];
		$arr[$key][] = $cb;
	}
	
	public static function get($path=array()) {
		$result = static::$registry;
		foreach($path as $key)
			if(!isset($result[$key]))
				return array();
			else
				$result = $result[$key];
		
		return $result;
	}

	private static function createhook($name, $cb, $type='on') {
		if(is_string($name))
			$name = explode('_', $name);
		$name[] = $type;

		static::set($name, $cb);
	}

	public static function hook() {
		return call_user_func_array(array(get_called_class(), 'hookOn'), func_get_args());
	}

	public static function hookOn($hookName, $cb) {
		static::createhook($hookName, $cb, 'on');
	}

	public static function hookBefore($hookName, $cb) {
		static::createhook($hookName, $cb, 'before');
	}

	public static function hookAfter($hookName, $cb) {
		static::createhook($hookName, $cb, 'after');
	}

	public static function hooks($allhooks) {
		foreach($allhooks as $name=>$hooks)
			foreach($hooks as $cb)
				static::createhook($name, $cb);
	}
}
#todo priority : see bundlesmanager