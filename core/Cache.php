<?php
namespace Coxis\Core;

class Cache {
	public static function clear() {
		if(Config::get('cache', 'method') == 'apc') {
			apc_clear_cache(Config::get('key').'-'.'user');
		}
		elseif(Config::get('cache', 'method') == 'file') {
			FileManager::unlink('cache');
		}
	}

	public static function get($file) {
		if(Config::get('cache', 'method') == 'apc') {
			return apc_fetch(Config::get('key').'-'.$file);
		}
		elseif(Config::get('cache', 'method') == 'file') {
			try {
				include 'cache/'.$file.'.php';
				return $cache;
			} catch(\ErrorException $e) {
				return false;
			}
		}
	}
	
	public static function set($file, $var) {
		if(Config::get('cache', 'method') == 'apc') {
			apc_store(Config::get('key').'-'.$file, $var);
		}
		elseif(Config::get('cache', 'method') == 'file') {
			try {
				if(var_export($var, true) == '')
					$res = "null";
				else
					$res = var_export($var, true);
				$res = '<?php'."\n".'$cache = '.$res.';';
				$output = 'cache/'.$file.'.php';
				FileManager::mkdir(dirname($output));
				file_put_contents($output, $res);
			} catch(\ErrorException $e) {
				return false;
			}
		}
		return true;
	}
	
	public static function delete($file) {
		if(Config::get('cache', 'method') == 'apc') {
			apc_delete(Config::get('key').'-'.$file);
		}
		elseif(Config::get('cache', 'method') == 'file') {
			$path = 'cache/'.$file.'.php';
			FileManager::unlink($path);
		}
	}
}