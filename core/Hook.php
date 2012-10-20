<?php
namespace Coxis\Core;

class Hook {
	// private $registry = array();
	public $registry = array();

	#cannot use references with get_func_args
	public function trigger($name, $args=array(), $cb=null) {
		return $this->triggerChain(new \Coxis\Core\HookChain, $name, $args, $cb);
	}

	#cannot use references with get_func_args
	public function triggerChain($chain, $name, $args=array(), $cb=null) {
		if(count(func_get_args()) > 14)
			throw new \Exception("triggerChain() can only accept up to 14 arguments");

		if(is_string($name))
			$name = explode('_', $name);

		$chain->calls = array_merge(
			$this->get(array_merge($name, array('before'))),
			$cb !== null ? array($cb):array(),
			$this->get(array_merge($name, array('on'))),
			$this->get(array_merge($name, array('after')))
		);

		// $filters = array(&$filter1, &$filter2, &$filter3, &$filter4, &$filter5, &$filter6, &$filter7, &$filter8, &$filter9, &$filter10);
		
		if(!is_array($args))
			$args = array($args);

		// return $chain->run($args, $filters);
		return $chain->run($args);
	}

	protected function set($path, $cb) {
		$arr =& $this->registry;
		$key = array_pop($path);
		foreach($path as $next)
			$arr =& $arr[$next];
		$arr[$key][] = $cb;
	}
	
	public function get($path=array()) {
		$result = $this->registry;
		foreach($path as $key)
			if(!isset($result[$key]))
				return array();
			else
				$result = $result[$key];
		
		return $result;
	}

	private function createhook($name, $cb, $type='on') {
		if(is_string($name))
			$name = explode('_', $name);
		$name[] = $type;

		$this->set($name, $cb);
	}

	public function hook() {
		return call_user_func_array(array(get_called_class(), 'hookOn'), func_get_args());
	}

	public function hookOn($hookName, $cb) {
		$this->createhook($hookName, $cb, 'on');
	}

	public function hookBefore($hookName, $cb) {
		$this->createhook($hookName, $cb, 'before');
	}

	public function hookAfter($hookName, $cb) {
		$this->createhook($hookName, $cb, 'after');
	}

	public function hooks($allhooks) {
		foreach($allhooks as $name=>$hooks)
			foreach($hooks as $cb)
				$this->createhook($name, $cb);
	}
}
#todo priority : see bundlesmanager
