<?php
namespace Tests;

class BrowserTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		if(!defined('_ENV_'))
			define('_ENV_', 'test');
		require_once(_CORE_DIR_.'core.php');
		\Coxis\Core\App::instance(true)->config->set('bundles', array(
			_COXIS_DIR_.'core',
			'app',
		));
		\Coxis\Core\App::loadDefaultApp();
	}
	
	public function test1() {
		$browser = new \Coxis\Utils\Browser;
		$doc = new \Coxis\Xpath\Doc($browser->get('')->content);
		$this->assertEquals($doc->text('//h1'), 'Coxis');
	}
}