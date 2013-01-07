<?php
namespace Coxis\Core;

class Coxis {
	public static $facades = array(
		'URL'				=>	'\Coxis\Core\URL',
		'Router'			=>	'\Coxis\Core\Router',
		'Config'			=>	'\Coxis\Core\Config',
		'Hook'				=>	'\Coxis\Core\Hook',
		'Response'			=>	'\Coxis\Core\Response',
		'Memory'			=>	'\Coxis\Core\Memory',
		'Flash'				=>	'\Coxis\Core\Tools\Flash',
		'DB'				=>	'\Coxis\Core\DB\DB',
		'CLIRouter'			=>	'\Coxis\Core\CLI\Router',
		'Validation'		=>	'\Coxis\Core\Validation',
		'ModelsManager'		=>	'\Coxis\Core\ModelsManager',

		'Locale'			=>	'\Coxis\Core\Tools\Locale',

		'HTML'				=>	'\Coxis\Core\Tools\HTML',
		'Importer'			=>	'\Coxis\Core\Importer',

		'Request'		=>	'\Coxis\Core\Request',
	);

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