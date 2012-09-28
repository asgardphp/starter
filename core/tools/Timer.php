<?php
namespace Coxis\Core\Tools;

class Timer {
	private static $time;

	public static function start() {
		$t = time()+microtime();
		static::$time = $t;
	}

	public static function end() {
		$t = time()+microtime();
		return array($t-static::$time, static::$time, $t);
	}
}