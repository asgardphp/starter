<?php
namespace {
	function from($from='') {
		return new \Coxis\Core\Importer($from);
	}
	function import($what, $into='') {
		return from()->import($what, $into);
	}
}

namespace Coxis\Core {
	class Importer {
		public $from = '';
		public $preimported = array();

		public function __construct($from='') {
			$this->from = $from;
		}
		
		public function import($what, $into='') {
			$imports = explode(',', $what);
			foreach($imports as $import) {
				$import = trim($import);
				
				$class = $what;
				$alias = static::basename($class);
				$vals = explode(' as ', $import);
				if(isset($vals[1])) {
					$class = trim($vals[0]);
					$alias = trim($vals[1]);
				}
			
				$alias = preg_replace('/^\\\+/', '', $into.'\\'.$alias);
				$class = preg_replace('/^\\\+/', '', $this->from.'\\'.$class);
				$this->preimported[$alias] = $class;
				
				#import directly or preimport
				//~ if(!static::_import($this->from.'\\'.$class, array('as'=>$alias, 'into'=>$into)))
					//~ throw new \Exception($this->from.'\\'.$class.' not found!');
			}
		
			return $this;
		}
		
		public function _import($class, $params=array()) {
			$class = preg_replace('/^\\\+/', '', $class);
			$alias = isset($params['as']) ? $params['as']:null;
			$intoNamespace= isset($params['into']) ? $params['into']:null;
			
			if($intoNamespace == '.')
				$intoNamespace = '';

			if(isset($this->preimported[$class])) {
				if(static::_import($this->preimported[$class], array('as'=>false))) {
					$toload = $this->preimported[$class];
					
					#import as ..
					if($alias !== false) {
						if(!$alias)
							$alias = ($intoNamespace ? $intoNamespace.'\\':'').static::basename($class);
							
						if($toload != $alias)
							try {
								class_alias($toload, $alias);
							} catch(\Exception $e) {
								return false;
							}
					}
					
					return true;
				}
			}	
			
			if($res = static::loadClass($class)) {
				if($res !== true)
					$class = $res;
				#import as ..
				if($alias !== false) {
					if(!$alias)
						$alias = ($intoNamespace ? $intoNamespace.'\\':'').static::basename($class);
					if($class != $alias) {
						try {
							class_alias($class, $alias);
						} catch(\Exception $e) {
							return false;
						}
					}
				}
					
				return true;
			}
			else {
				$dir = static::dirname($class);

				if($dir != '.') {
					$base = static::basename($class);
					if(dirname($dir) == '.')
						$next = $base;
					else
						$next = str_replace(DIRECTORY_SEPARATOR, '\\', dirname($dir)).'\\'.$base;

					return static::_import($next, array('into'=>$intoNamespace, 'as'=>$alias));
				}
			
				return false;
			}
		}
		
		#todo replace with namespaceutils
		public static function dirname($v) {
			return dirname(str_replace('\\', DIRECTORY_SEPARATOR, $v));
		}
		
		public static function basename($v) {
			return basename(str_replace('\\', DIRECTORY_SEPARATOR, $v));
		}
		
		public static function class2path($class) {
			#remove vendor prefix
			$namespace = preg_replace('/^[a-zA-Z0-9]+\\\/', '', $class, -1, $count);
			if($count == 0)
				$namespace = preg_replace('/[a-zA-Z0-9]+_/', '', $namespace);
			$namespace = str_replace('\\', DIRECTORY_SEPARATOR , $namespace);
			
			$className = basename($namespace);
			$namespace = strtolower(dirname($namespace));

			if($namespace != '.')
				$path = $namespace.DIRECTORY_SEPARATOR;
			else
				$path = '';
			$path .= str_replace('_', DIRECTORY_SEPARATOR , $className);	

			return $path.'.php';
		}
		
		public static function loadClassFile($file) {
			$before = array_merge(get_declared_classes(), get_declared_interfaces());
			require_once $file;
			$after = array_merge(get_declared_classes(), get_declared_interfaces());
			
			$diff = array_diff($after, $before);
			foreach($diff as $class)		
				if(method_exists($class, '_autoload')) {
					try {
						call_user_func(array($class, '_autoload'));
					} catch(\Exception $e) {
						d($e); #todo error report this exception cause autoloader does not let it bubble up
					}
				}
			return get(array_values($diff), sizeof($diff)-1);
		}

		public static function loadClass($class) {
		// 			if($class == 'Coxis\Core\FrontController')
		// die($class);
			#already loaded
			if(class_exists($class, false) || interface_exists($class, false))
				return true;
			#file map
			// elseif(isset(Autoloader::$map[$class])) {
			// elseif(isset(\Coxis\Core\Context::get('coxis\core\autoloader')->map[$class])) {
			elseif(isset(\Coxis\Core\Context::get('autoloader')->map[$class])) {
				$result = static::loadClassFile(\Coxis\Core\Context::get('autoloader')->map[$class]);
				class_alias($result, $class);
				return true;
			}
			else {
				#directory map
				// foreach(Autoloader::$directories as $prefix=>$dir) {
				// foreach(\Coxis\Core\Context::get('coxis\core\autoloader')->directories as $prefix=>$dir) {
				foreach(\Coxis\Core\Context::get('autoloader')->directories as $prefix=>$dir) {
					if(preg_match('/^'.preg_quote($prefix).'/', $class)) {
						$rest = preg_replace('/^'.preg_quote($prefix).'\\\?/', '', $class);
						$path = $dir.DIRECTORY_SEPARATOR.static::class2path($rest);
						
						if(file_exists($path)) {
							static::loadClassFile($path);
							return true;
						}
					}
				}
				
				#to load from namespace
				if(file_exists(($path = static::class2path($class)))) {
					static::loadClassFile($path);
					if(class_exists($class, false) || interface_exists($class, false))
						return true;
				}
				
				// d($class);#only to test importer

				#lookup for global classes
				if(dirname($class) == '.') {
					$classes = array();
					
					#check if there is any corresponding class already loaded
					foreach(array_merge(get_declared_classes(), get_declared_interfaces()) as $v)
						if(strtolower(static::basename($class)) == strtolower(static::basename($v))) {
							class_alias($v, $class);
							return true;
						}
					
					#remove, only for testing class loading
					// d();

					// foreach(Autoloader::$preloaded as $v)
					// foreach(\Coxis\Core\Context::get('coxis\core\autoloader')->preloaded as $v)
					foreach(\Coxis\Core\Context::get('autoloader')->preloaded as $v)
					// foreach(Context::get('Autoloader')->preloaded as $v)
						if(strtolower(static::basename($class)) == $v[0])
							$classes[] = $v;
					if(sizeof($classes) == 1) {
						$before = array_merge(get_declared_classes(), get_declared_interfaces());
						static::loadClassFile($classes[0][1]);
						$after = array_merge(get_declared_classes(), get_declared_interfaces());
						
						$diff = array_diff($after, $before);

						if(class_exists(static::basename($class), false) || interface_exists(static::basename($class), false)) {
							#return true;
							#return static::basename($class);
						}
						else {
							#maybe the loaded class uses another namespace?
							$res = array_values(preg_grep('/'.static::basename($class).'$/i', $diff));
							try {
								$loadedClass = $res[sizeof($res)-1];
								class_alias($loadedClass, $class);
								return true;
							} catch(\ErrorException $e) {
								return false;
							}
						}
					}
					#if multiple classes, don't load
					elseif(sizeof($classes) > 1)
						throw new \Exception('There are multiple classes '.$class);
					#if no class, don't load
					else
						return false;
				}
			}
			
			return false;
		}
	}
}
