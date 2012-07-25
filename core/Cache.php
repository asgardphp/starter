<?php
namespace Coxis\Core;

class Cache {
	public static function get($file) {
		try {
			require 'cache/'.$file.'.php';
			return $cache;
		} catch(\ErrorException $e) {
			return false;
		}
	}
	
	public static function set($file, $var) {
		try {
			//~ $res = '<?php'."\n".'$cache = '.var_export($var).';';
			if(var_export($var, true) == '')
				$res = "''";
			else
				$res = var_export($var, true);
			$res = '<?php'."\n".'$cache = '.$res.';';
			//~ $res = '<';
			//~ d($res);
			$output = 'cache/'.$file.'.php';
			FileManager::mkdir(dirname($output));
			file_put_contents($output, $res);
			return true;
		} catch(\ErrorException $e) {
			return false;
		}
	}
}