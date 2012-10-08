<?php
namespace Coxis\Core\Test;

class Browser {
	public static function get($url) {
		$res = exec('php console get '.$url);
		d($res);
		return exec('php console get '.$url);
	}
}