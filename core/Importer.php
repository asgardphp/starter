<?php
namespace {
	function from($from='') {
		return new \Coxis\Core\Importer($from);
	}
	function import($what, $into='') {
	//~ d();
		return from()->import($what, $into);
		//~ return from()->preimport($what, $into);
	}
}

namespace Coxis\Core {
	class Importer {
		public $from = '';
		public static $preimported = array(
			//~ array('Somewhere', 'there/somewhere.php'),
		);

		public function __construct($from='') {
			$this->from = $from;
		}
		
		//~ public function preimport($what, $into) {
			
		//~ }
		
		public function import($what, $into='') {
			$imports = explode(',', $what);
			foreach($imports as $import) {
				$import = trim($import);
				
				$class = $what;
				$alias = basename($class);
				$vals = explode(' as ', $import);
				if(isset($vals[1])) {
					$class = trim($vals[0]);
					$alias = trim($vals[1]);
				}
			
				//~ $alias = $into.'\\'.$alias;
				$alias = preg_replace('/^\\\+/', '', $into.'\\'.$alias);
				$class = preg_replace('/^\\\+/', '', $this->from.'\\'.$class);
				static::$preimported[$alias] = $class;
				
				//~ if(!static::_import($this->from.'\\'.$class, array('as'=>$alias, 'into'=>$into)))
					//~ throw new \Exception($this->from.'\\'.$class.' not found!');
			}
		
			return $this;
		}
		
		public static function _import($class, $params=array()) {
			$class = preg_replace('/^\\\+/', '', $class);
			$alias = isset($params['as']) ? $params['as']:null;
			$intoNamespace= isset($params['into']) ? $params['into']:null;
		//~ if(defined('asd'))
			//~ d($intoNamespace);
			if($intoNamespace == '.')
				$intoNamespace = '';
		
			$path = static::class2path($class);
		
		//~ if($class == 'Coxis\\Core\\Config')
		//~ d('asdf');
			
			if(isset(static::$preimported[$class])) {
				if(static::_import(static::$preimported[$class], array('as'=>false))) {
					$toload = static::$preimported[$class];
		//~ if(defined('asd'))
			//~ d($class, static::$preimported[$class], class_exists('Config'), class_exists('Coxis\\Core\\Config'));
			//~ try {
					//~ unset(static::$preimported[$class]);
					//~ } catch(Exception $e){}
					
					#import as ..
					if($alias !== false) {
						if(!$alias)
							$alias = ($intoNamespace ? $intoNamespace.'\\':'').basename($class);
		//~ if(defined('asd'))
		//~ d($toload, $alias);
						if($toload != $alias)
							try {
		//~ if(defined('asd'))
			//~ d($class, static::$preimported[$class], $toload, $alias);
								
								class_alias($toload, $alias);
							} catch(\Exception $e) {
								return false;
							}
					}
					
					return true;
				}
			}
			
			if(static::loadClass($class)) {
				#import as ..
				if($alias !== false) {
					if(!$alias)
						$alias = ($intoNamespace ? $intoNamespace.'\\':'').basename($class);
					if($class != $alias)
						try {
							class_alias($class, $alias);
						} catch(\Exception $e) {
							return false;
						}
				}
					
				return true;
			}
			else {
				$dir = dirname($class);
				
				if($dir != '.') {
					if(dirname($dir) == '.')
						$next = basename($class);
					else
						$next = dirname($dir).'\\'.basename($class);
					
					return static::_import($next, array('into'=>$intoNamespace, 'as'=>$alias));
				}
			
				return false;
			}
		}
		
		public static function class2path($class) {
			$namespace = dirname($class);
			
			#remove vendor prefix
			$namespace = preg_replace('/^[a-zA-Z0-9]+\\\/', '', $namespace, -1, $count);
			if($count == 0)
				$namespace = preg_replace('/[a-zA-Z0-9]+_/', '', $namespace);
			
			$className = basename($class);

			if($namespace != '.')
				$path = str_replace('\\', DIRECTORY_SEPARATOR , $namespace).DIRECTORY_SEPARATOR ;
			else
				$path = '';
			$path .= str_replace('_', DIRECTORY_SEPARATOR , $className);
			
			return $path.'.php';
		}
		
		public static function loadClassFile($file) {
		//~ Log::write('loader.txt', $file);
			$before = get_declared_classes();
			require_once $file;
			$after = get_declared_classes();
			
			$diff = array_diff($after, $before);
			foreach($diff as $class)		
				if(method_exists($class, '_autoload'))
					call_user_func(array($class, '_autoload'));
			
			return get(array_values($diff), sizeof($diff)-1);
		}

		public static function loadClass($class) {
			#already loaded
			if(class_exists($class, false))
				return true;
			#file map
			elseif(isset(Autoloader::$map[$class])) {
				static::loadClassFile(Autoloader::$map[$class]);
				return true;
			}
			else {
				#directory map
				foreach(Autoloader::$directories as $prefix=>$dir) {
					if(preg_match('/^'.preg_quote($prefix).'/', $class)) {
						$rest = preg_replace('/^'.preg_quote($prefix).'\\\?/', '', $class);
						$path = $dir.DIRECTORY_SEPARATOR.static::class2path($rest);
						
						if(file_exists($path)) {
							//~ require_once $path;
							static::loadClassFile($path);
							return true;
						}
					}
				}
				
				#to load
				if(file_exists(($path = static::class2path($class)))) {
					static::loadClassFile($path);
					if(class_exists($class, false))
						return true;
				}
				#lookup for global classes
				if(dirname($class) == '.') {
					$classes = array();
					
					#check if there is any corresponding class already loaded
					foreach(get_declared_classes() as $v)
						if(strtolower(basename($class)) == strtolower(basename($v))) {
							class_alias($v, $class);
							return true;
						}
					
					#todo remove, only for testing class loading
					//~ return false;
					
				//~ if($class='DefaultController')
					//~ d($class, Autoloader::$preloaded);
					
					foreach(Autoloader::$preloaded as $v)
						if(strtolower(basename($class)) == $v[0])
							$classes[] = $v;
							
					if(sizeof($classes) == 1) {
						$before = get_declared_classes();
						static::loadClassFile($classes[0][1]);
						$after = get_declared_classes();
						
						$diff = array_diff($after, $before);
						
						//~ if($class=='BundlesManager')
						//~ d(preg_grep('/'.basename($class).'/', $diff));
							//~ d($diff);
						
						if(class_exists(basename($class), false))
							return true;
						else {
							#maybe the loaded class uses another namespace?
							#todo check that basename is equal
							//~ $loadedClass = get(get_declared_classes(), sizeof(get_declared_classes())-1);
							$res = array_values(preg_grep('/'.basename($class).'/', $diff));
							//~ d($res, $diff);
							$loadedClass = $res[sizeof($res)-1];
							//~ d($loadedClass);
							try {
			//~ if($class=='BundlesManager')
			//~ d($class, $loadedClass, array_search('Coxis\Core\BundlesManager', get_declared_classes()), array_slice(get_declared_classes(), -50));
								class_alias($loadedClass, $class);
								return true;
							} catch(PHPErrorException $e) {
								return false;
							}
						}
					}
					#if no or multiple classes, don't load
					else
						return false;
				}
			}
			
			return false;
		}
	}
}