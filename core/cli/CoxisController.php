<?php
namespace Coxis\Core\Cli;

class CoxisController extends CLIController {
	public function testAction($request) {
		//~ d($request);
		
		echo 'here';
	}
	
	public function setAction($request) {
		die('TODO');
		$config = file_get_contents(_PROJECT_DIR_.'/config.php');
		$config = preg_replace("/('$key'\s*=>\s*)'.*?'/", "$1'$value'", $config);
		file_put_contents(_PROJECT_DIR_.'/config.php', $config);
	}
	
	public function importAction($request) {
		die('TODO');
	}
	
	public function buildAction($request) {
		die('TODO');
	}
}