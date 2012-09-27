<?php
namespace Coxis\Core;

class Hook {
	private static $registry = array();

	#cannot use references with get_func_args
	public static function trigger($name, $cb=null, $args=array(), &$filter1=null, &$filter2=null, &$filter3=null, &$filter4=null, &$filter5=null, &$filter6=null,
		&$filter7=null, &$filter8=null, &$filter9=null, &$filter10=null) {
		return static::triggerChain(new \Coxis\Core\HookChain, $name, $cb, $args, $filter1, $filter2, $filter3, $filter4, $filter5, $filter6,
			$filter7, $filter8, $filter9, $filter10);
	}

	#cannot use references with get_func_args
	public static function triggerChain($chain, $name, $cb=null, $args=array(), &$filter1=null, &$filter2=null, &$filter3=null, &$filter4=null, &$filter5=null, &$filter6=null,
		&$filter7=null, &$filter8=null, &$filter9=null, &$filter10=null) {
		if(count(func_get_args()) > 14)
			throw new \Exception("triggerChain() can only accept up to 14 arguments");

		$chain->calls = array_merge(
			static::get(array_merge($name, array('before'))),
			$cb !== null ? array($cb):array(),
			static::get(array_merge($name, array('on'))),
			static::get(array_merge($name, array('after')))
		);

		$filters = array(&$filter1, &$filter2, &$filter3, &$filter4, &$filter5, &$filter6, &$filter7, &$filter8, &$filter9, &$filter10);
		
		return $chain->run($args, $filters);
	}

	private static function set($path, $cb) {
		$arr =& static::$registry;
		$key = array_pop($path);
		foreach($path as $next)
			$arr =& $arr[$next];
		$arr[$key][] = $cb;
	}
	
	public static function get($path) {
		$result = static::$registry;
		foreach($path as $key)
			if(!isset($result[$key]))
				return array();
			else
				$result = $result[$key];
		
		return $result;
	}

	private static function createhook($args, $type='on') {
		$last = $args[sizeof($args)-1];
			$cb = array_pop($args);
		$args[] = $type;
		static::set($args, $cb);
	}

	public static function hook() {
		return call_user_func_array(array(get_called_class(), 'hookOn'), func_get_args());
	}

	public static function hookOn() {
		static::createhook(func_get_args(), 'on');
	}

	public static function hookBefore() {
		static::createhook(func_get_args(), 'before');
	}

	public static function hookAfter() {
		static::createhook(func_get_args(), 'after');
	}
}