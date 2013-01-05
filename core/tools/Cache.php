<?php
namespace Coxis\Core\Tools;

class Cache {
	public static function clear() {
		if(\Config::get('cache', 'method') == 'apc') {
			apc_clear_cache(\Config::get('key').'-'.'user');
		}
		elseif(\Config::get('cache', 'method') == 'file') {
			FileManager::unlink('cache');
		}
	}

	public static function get($file, $default=null) {
		if(\Config::get('phpcache')) {
			if(\Config::get('cache', 'method') == 'apc') {
				$success = null;
				$res = apc_fetch(\Config::get('key').'-'.$file, $success);
				if($success)
					return $res;
			}
			elseif(\Config::get('cache', 'method') == 'file') {
				try {
					return include 'cache/'.$file.'.php';
				} catch(\ErrorException $e) {}
			}
		}

		if(is_function($default)) {
			$r = $default();
			static::set($file, $r);
			return $r;
		}
		else
			return $default;
	}

	public static function sizeofvar($var) {
		$start_memory = memory_get_usage();
		$tmp = unserialize(serialize($var));
		return memory_get_usage() - $start_memory;
	}
	
	public static function set($file, $var) {
		if(!\Config::get('phpcache'))
			return;
		if(\Config::get('cache', 'method') == 'apc') {
			apc_store(\Config::get('key').'-'.$file, $var);
		}
		elseif(\Config::get('cache', 'method') == 'file') {
			if(static::sizeofvar($var) > 5*1024*1024)
				return;
			try {
				if(is_object($var))
					$res = 'unserialize(\''.serialize($var).'\')';
				elseif(($ve = var_export($var, true)) == '')
					$res = 'null';
				else
					$res = $ve;
				$res = '<?php'."\n".'return '.$res.';';
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
		if(\Config::get('cache', 'method') == 'apc') {
			apc_delete(\Config::get('key').'-'.$file);
		}
		elseif(\Config::get('cache', 'method') == 'file') {
			$path = 'cache/'.$file.'.php';
			FileManager::unlink($path);
		}
	}
}