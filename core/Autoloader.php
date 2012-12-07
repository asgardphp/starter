<?php
namespace Coxis\Core;

class Autoloader {
	public $map = array(
		// 'Something'	=>	'there/somewhere.php',
	);
	public $directories = array(
		// 'foo\bar'	=>	'there',
		// 'swift_'	=>	'swift',
	);
	public $preloaded = array(
		// array('Somewhere', 'there/somewhere.php'),
	);
	
	public function map($class, $path) {
		$this->map[$class] = $path;
	}
	
	public function dir() {
	}

	public function preloadClass($class, $file) {
		$this->preloaded[] = array(strtolower($class), $file);
	}
	
	public function preloadDir($file) {
		// if($relative)
		// 	$file = _DIR_.$file;
		if(is_dir($file))
			foreach(glob($file.'/*') as $sub_file)
				// d($sub_file);
				$this->preloadDir($sub_file);
		else {
			if(!preg_match('/\/[a-zA-Z0-9_]+.php$/', $file))
				return;
			list($class) = explode('.', basename($file));
			$this->preloadClass($class, $file);
		}
		#remove duplicate files
		$this->preloaded = array_unique($this->preloaded, SORT_REGULAR);
	}
	
	public static function loadClass($class) {
		if(function_exists('__autoload'))
			__autoload($class);
		if(class_exists($class))
			return;
		
		$dir = str_replace('\\', DIRECTORY_SEPARATOR, $class);
		// $dir = Importer::dirname($dir);
		$dir = Context::get('importer')->dirname($dir);
		$dir = str_replace(DIRECTORY_SEPARATOR, '\\', $dir);

		// require_once 'core/Log.php';
		// require_once 'core/Tools/FileManager.php';
		// \Coxis\Core\Log::write('classes.txt', $class);

		// Importer::_import($class, array('into'=>$dir));
		Context::get('importer')->_import($class, array('into'=>$dir));
	}
}