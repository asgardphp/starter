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

		public $basedir = 'vendor/';

		public function __construct($from='') {
			$this->from = $from;
		}
		
		public function import($what, $into='') {
			$imports = explode(',', $what);
			foreach($imports as $import) {
				$import = trim($import);
				
				$class = $what;
				$alias = \Coxis\Core\NamespaceUtils::basename($class);
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

			#preimported
			if(isset($this->preimported[$class])) {
				if(static::_import($this->preimported[$class], array('as'=>false))) {
					$toload = $this->preimported[$class];
					
					#import as ..
					if($alias !== false) {
						if(!$alias)
							$alias = ($intoNamespace ? $intoNamespace.'\\':'').\Coxis\Core\NamespaceUtils::basename($class);
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
			
			#look for the class
			if($res = $this->loadClass($class)) {
				if($res !== true)
					$class = $res;
				#import as ..
				if($alias !== false) {
					if(!$alias)
						$alias = ($intoNamespace ? $intoNamespace.'\\':'').\Coxis\Core\NamespaceUtils::basename($class);
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
			#go to upper level
			else {
				$dir = \Coxis\Core\NamespaceUtils::dirname($class);

				if($dir != '.') {
					$base = \Coxis\Core\NamespaceUtils::basename($class);
					if(\Coxis\Core\NamespaceUtils::dirname($dir) == '.')
						$next = $base;
					else
						$next = str_replace(DIRECTORY_SEPARATOR, '\\', \Coxis\Core\NamespaceUtils::dirname($dir)).'\\'.$base;

					return static::_import($next, array('into'=>$intoNamespace, 'as'=>$alias));
				}
			
				return false;
			}
		}
		
		public static function class2path($class) {
			$namespace = str_replace('\\', DIRECTORY_SEPARATOR , $class);
			
			$className = \Coxis\Core\NamespaceUtils::basename($namespace);
			$namespace = strtolower(\Coxis\Core\NamespaceUtils::dirname($namespace));

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
			foreach($diff as $class) {	
				if(method_exists($class, '_autoload')) {
					try {
						call_user_func(array($class, '_autoload'));
					} catch(\Exception $e) {
						d($e); #todo error report this exception cause autoloader does not let it bubble up
					}
				}
			}
			return get(array_values($diff), sizeof($diff)-1);
		}

		public function loadClass($class) {
			#already loaded
			if(class_exists($class, false) || interface_exists($class, false))
				return true;
			#file map
			elseif(isset(Autoloader::$map[strtolower($class)])) {
				$result = static::loadClassFile(Autoloader::$map[strtolower($class)]);
						return static::createAlias($result, $class);
				class_alias($result, $class);
				return true;
			}
			else {
				#directory map
				foreach(Autoloader::$directories as $prefix=>$dir) {
					if(preg_match('/^'.preg_quote($prefix).'/', $class)) {
						$rest = preg_replace('/^'.preg_quote($prefix).'\\\?/', '', $class);
						$path = $dir.DIRECTORY_SEPARATOR.static::class2path($rest);
						
						if(file_exists(_DIR_.$path)) {
							$result = static::loadClassFile($path);

						return static::createAlias($result, $class);
							return true;
						}
					}
				}

				if(file_exists(_DIR_.$this->basedir.($path = static::class2path($class)))) {
					$result = static::loadClassFile($this->basedir.$path);
						return static::createAlias($result, $class);
				}
				
				// d($class);#only to test importer

				#lookup for global classes
				if(\Coxis\Core\NamespaceUtils::dirname($class) == '.') {
					$classes = array();
					
					#check if there is any corresponding class already loaded
					foreach(array_merge(get_declared_classes(), get_declared_interfaces()) as $v)
						if(strtolower(\Coxis\Core\NamespaceUtils::basename($class)) == strtolower(\Coxis\Core\NamespaceUtils::basename($v))) {
							class_alias($v, $class);
							return true;
						}
					
					#remove, only for testing class loading
					// d();
					foreach(Autoloader::$preloaded as $v)
						if(strtolower(\Coxis\Core\NamespaceUtils::basename($class)) == $v[0])
							$classes[] = $v;
					if(sizeof($classes) == 1) {
						$loadedClass = static::loadClassFile($classes[0][1]);
						return static::createAlias($loadedClass, $class);
					}
					#if multiple classes, don't load
					elseif(sizeof($classes) > 1) {
						$classfiles = array();
						foreach($classes as $classname)
							$classfiles[] = $classname[1];
						throw new \Exception('There are multiple classes '.$class.': '.implode(', ', $classfiles));
					}
					#if no class, don't load
					else
						return false;
				}
			}
			
			return false;
		}

		public static function createAlias($loadedClass, $class) {
			if(strtolower(\Coxis\Core\NamespaceUtils::basename($class)) != strtolower(\Coxis\Core\NamespaceUtils::basename($loadedClass)))
				return false;
			try {
				if($loadedClass != $class)
					class_alias($loadedClass, $class);
				return true;
			} catch(\ErrorException $e) {
				return false;
			}
		}
	}
}
