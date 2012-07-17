<?php
namespace Coxis\Core;

class FileManager {
	public static function getNewFileName($output) {
		$fileexts = explode('.', $output);
		$filename = implode('.', array_slice($fileexts, 0, -1));
		$ext = $fileexts[sizeof($fileexts)-1];
		$output = $filename.'.'.$ext;
		
		$i=1;
		while(file_exists($output))
			$output = $filename.'_'.($i++).'.'.$ext;
			
		return $output;
	}

	public static function move_uploaded($src, $output) {	
		$output = static::getNewFileName($output);
			
		static::mkdir(dirname($output));
			
		if(!move_uploaded_file($src, $output))
			return false;
		else
			return basename($output);
	}
	
	public static function isUploaded($file) {
		return (isset($file['tmp_name']) && !empty($file['tmp_name']));
	}
	
	public static function unlink($file) {
		if(file_exists($file)) {
			unlink($file);
			return true;
		}
		else
			return false;
	}
	
	public static function mkdir($dir) {		
		if(!file_exists($dir))
			return mkdir($dir, 0777, true);
		return true;
	}
}