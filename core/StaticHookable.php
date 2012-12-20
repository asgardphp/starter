<?php
namespace Coxis\Core;

class StaticHookable {
	public static function trigger($name, $args=array(), $cb=null) {
		return \Hook::trigger(array('classes', get_called_class(), $name), $args, $cb);
	}

	public static function triggerChain($chain, $name, $args=array(), $cb=null) {
		return \Hook::triggerChain($chain, array('classes', get_called_class(), $name), $args, $cb);
	}

	public static function hook() {
		return call_user_func_array(array('Hook', 'hook'), func_get_args());
	}

	public static function hookOn($hookName, $cb) {
		$args = array(array('classes', get_called_class(), $hookName), $cb);
		return call_user_func_array(array('Hook', 'hookOn'), $args);
	}

	public static function hookBefore($hookName, $cb) {
		$args = array(array('classes', get_called_class(), $hookName), $cb);
		return call_user_func_array(array('Hook', 'hookBefore'), $args);
	}

	public static function hookAfter($hookName, $cb) {
		$args = array(array('classes', get_called_class(), $hookName), $cb);
		return call_user_func_array(array('Hook', 'hookBefore'), $args);
	}
}
