<?php
namespace Coxis\Core\Tools;

class Log {
	public static function add($filename, $msg) {
		\Coxis\Core\Tools\FileManager::mkdir(dirname('logs/'.$filename));
		$filename = \Coxis\Core\Tools\FileManager::getNewFileName('logs/'.$filename);
		file_put_contents($filename, $msg);
	}
	
	public static function write($filename, $msg) {
		\Coxis\Core\Tools\FileManager::mkdir(dirname('logs/'.$filename));
		file_put_contents('logs/'.$filename, "\n".$msg, FILE_APPEND|LOCK_EX);
		#todo concurrent writing problem?!
	}
}