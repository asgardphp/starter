<?php
if(!defined('_ENV_'))
	define('_ENV_', 'test');
require_once(_CORE_DIR_.'core.php');
\Coxis\Core\Coxis::load();

require_once _VENDOR_DIR_.'duvanmonsa/php-query/src/phpQuery.php';

if(!function_exists('_pq')) {
	function _pq($html, $selector) {
		$doc = phpQuery::newDocument($html);
		phpQuery::selectDocument($doc);
		return pq($selector);
	}
}

class BrowserTest extends PHPUnit_Framework_TestCase {
	public function setUp(){
		\DB::import('tests/coxis.sql');
	}

	public function tearDown(){}

	public function test0() {
	}

	public function test1() {
		$browser = new \Coxis\Utils\Browser;
		$this->assertEquals(_pq($browser->get('')->content, 'h1')->html(), 'Coxis');
	}
}