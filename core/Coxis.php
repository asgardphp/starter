<?php
namespace Coxis\Core;

class Coxis {
	public static function getExceptionResponse($e) {
		if($e instanceof \ErrorException) {
			$msg = '('.$e->getCode().') '.$e->getMessage().'<br>'.$e->getFile().' ('.$e->getLine().')';
			return \Error::report($msg, $e->getTrace());
		}
		else {
			$first_trace = array(array(
				'file'	=>	$e->getFile(),
				'line'	=>	$e->getLine(),
			));
			return \Error::report($e->getMessage(), array_merge($first_trace, $e->getTrace()));
		}
	}

	public static function load() {
		if(!defined('_ENV_'))
			if(\Server::get('HTTP_HOST') == '127.0.0.1' || \Server::get('HTTP_HOST') == 'localhost')
				define('_ENV_', 'dev');
			else
				define('_ENV_', 'prod');
			
		BundlesManager::loadBundles();
	}
}