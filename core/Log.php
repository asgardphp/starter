<?php
namespace Coxis\Core;

import('Coxis\Core\Tools\FileManager');

class Log {
	public static function add($filename, $msg) {
		FileManager::mkdir(dirname('logs/'.$filename));
		$filename = FileManager::getNewFileName('logs/'.$filename);
		file_put_contents($filename, $msg);
	}
	
	public static function write($filename, $msg) {
		FileManager::mkdir(dirname('logs/'.$filename));
		file_put_contents('logs/'.$filename, $msg, FILE_APPEND);
	}
}