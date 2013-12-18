<?php
if(!defined('_ENV_'))
	define('_ENV_', 'test');
require_once(_CORE_DIR_.'core.php');
\Coxis\Core\App::load();

class BrowserTest extends PHPUnit_Framework_TestCase {
	public function setUp(){
		\DB::import('tests/coxis.sql');
	}

	public function tearDown(){}

	public function test0() {
	}

	public function test1() {
		$browser = new \Coxis\Utils\Browser;
		$doc = new Coxis\Xpath\Doc($browser->get('')->content);
		$this->assertEquals($doc->text('//h1'), 'Coxis');
	}
}