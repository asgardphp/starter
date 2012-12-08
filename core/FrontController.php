<?php
namespace Coxis\Core;

class FrontController extends Controller {
	public function mainAction() {
		if(!defined('_ENV_'))
			if(\Server::get('HTTP_HOST') == '127.0.0.1' || \Server::get('HTTP_HOST') == 'localhost')
				define('_ENV_', 'dev');
			else
				define('_ENV_', 'prod');

		$response = require('core/getresponse.php');
		$response->send();
	}
}