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
		public static $preimported = array();

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
				static::$preimported[$alias] = $class;
				
				#import directly or preimport
				//~ if(!static::_import($this->from.'\\'.$class, array('as'=>$alias, 'into'=>$into)))
					//~ throw new \Exception($this->from.'\\'.$class.' not found!');
			}
		
			return $this;
		}
		
		public static function _import($class, $params=array()) {
			$class = preg_replace('/^\\\+/', '', $class);
			$alias = isset($params['as']) ? $params['as']:null;
			$intoNamespace= isset($params['into']) ? $params['into']:null;
			
			if($intoNamespace == '.')
				$intoNamespace = '';
		
			//~ $path = static::class2path($class);
			//~ if($class == 'Coxis\Core\Config')
	//~ d($class, static::class2path($class), static::$preimported);
			
			if(isset(static::$preimported[$class])) {
				if(static::_import(static::$preimported[$class], array('as'=>false))) {
					$toload = static::$preimported[$class];
					
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
			
			if(static::loadClass($class)) {
				#import as ..
				if($alias !== false) {
					if(!$alias)
						$alias = ($intoNamespace ? $intoNamespace.'\\':'').static::basename($class);
					if($class != $alias)
						try {
#if($class == 'Coxis\Core\BundlesManager')				
#d(12, $alias, class_exists('Coxis\Core\BundlesManager', false));
							class_alias($class, $alias);
						} catch(\Exception $e) {
							return false;
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
#d($params;
#d($intoNamespace, $params);
#$r = static::_import($next, array('into'=>$intoNamespace, 'as'=>$alias));
#d(class_exists('Coxis\Core\Cli\BundlesManager', false), class_exists('Coxis\Core\BundlesManager', false));
#return $r;
#if($next == 'Coxis\Bundles\Controller')
#d($next);
					return static::_import($next, array('into'=>$intoNamespace, 'as'=>$alias));
				}
			
				return false;
			}
		}
		
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
				#$path = str_replace('\\', DIRECTORY_SEPARATOR , $namespace).DIRECTORY_SEPARATOR ;
				$path = $namespace.DIRECTORY_SEPARATOR;
			else
				$path = '';
			$path .= str_replace('_', DIRECTORY_SEPARATOR , $className);	

			return $path.'.php';
		}
		
		public static function loadClassFile($file) {
			$before = get_declared_classes();
			require_once $file;
			$after = get_declared_classes();
			
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
				
				//~ d();#only to test importer

				#lookup for global classes
				if(dirname($class) == '.') {
					$classes = array();
					
					#check if there is any corresponding class already loaded
					foreach(get_declared_classes() as $v)
						if(strtolower(static::basename($class)) == strtolower(static::basename($v))) {
							class_alias($v, $class);
							return true;
						}
					
					#remove, only for testing class loading
					//~ return false;
#if($class == 'Controller')					
#d(Autoloader::$preloaded);

#if($class == 'Controller')
#	d(strtolower(static::basename($class)));
					foreach(Autoloader::$preloaded as $v)
#if($v[0] == 'controller')
#d(strtolower(static::basename($class)), $v);
						if(strtolower(static::basename($class)) == $v[0])
							#if($class == 'Controller')
						#		d(1,$v);
#else
							$classes[] = $v;
#d($classes);		
#if($class == 'Controller')
#	d($classes);
					if(sizeof($classes) == 1) {
						$before = get_declared_classes();
						static::loadClassFile($classes[0][1]);
#require_once('core/Controller.php');
						$after = get_declared_classes();
						
						$diff = array_diff($after, $before);
#if($class == 'Controller')
#d($diff, $after);

#if($class == 'Controller')						{
#	require_once($classes[0][1]);
#d(static::basename($class), class_exists(static::basename($class), false), $classes[0][1]);
#}
						if(class_exists(static::basename($class), false))
							return true;
						else {
							#maybe the loaded class uses another namespace?
#if($class == 'Controller')
#d($diff);
							$res = array_values(preg_grep('/'.static::basename($class).'$/i', $diff));
							try {
#if($class == 'Controller')
#d($res);
								$loadedClass = $res[sizeof($res)-1];
#if($class == 'Controller')
#d($loadedClass, $class);
								class_alias($loadedClass, $class);
								return true;
							} catch(\ErrorException $e) {
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