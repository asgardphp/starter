<?php
namespace Coxis\Core;

class Hookable {
	/* INSTANCE */
	public function trigger($name, $args=array(), $cb=null) {
		return \Hook::trigger(array('instances', spl_object_hash($this), $name), $args, $cb);
	}

	public function triggerChain($chain, $name, $args=array(), $cb=null) {
		return \Hook::triggerChain($chain, array('instances', spl_object_hash($this), $name), $args, $cb);
	}

	public function hook() {
		return call_user_func_array(array('Hook', 'hook'), func_get_args());
	}

	public function hookOn($hookName, $cb) {
		$args = array(array('instances', spl_object_hash($this), $hookName), $cb);
		return call_user_func_array(array('Hook', 'hookOn'), $args);
	}

	public function hookBefore($hookName, $cb) {
		$args = array(array('instances', spl_object_hash($this), $hookName), $cb);
		return call_user_func_array(array('Hook', 'hookBefore'), $args);
	}

	public function hookAfter($hookName, $cb) {
		$args = array(array('instances', spl_object_hash($this), $hookName), $cb);
		return call_user_func_array(array('Hook', 'hookBefore'), $args);
	}
}
