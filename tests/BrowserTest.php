<?php
if(!defined('_ENV_'))
	define('_ENV_', 'test');
require_once(dirname(__FILE__).'/../coxis.php');
\BundlesManager::loadBundles();

require 'vendors/phpQuery/phpQuery/phpQuery.php';

function _pq($html, $selector) {
	$doc = phpQuery::newDocument($html);
	phpQuery::selectDocument($doc);
	return pq($selector);
}

class BrowserTest extends PHPUnit_Framework_TestCase {
	public function setUp(){
		\DB::import('tests/coxis.sql');
	}

	public function tearDown(){}

	public function test0() {
		$browser = new Browser;
		$this->assertEquals(_pq($browser->get('')->content, 'h1')->html(), 'Coxis');

		$browser = new Browser;
		$this->assertEquals($browser->get('admin')->getCode(), 401);

		$browser = new Browser;
		$browser->session['admin_id'] = 1;
		$this->assertEquals($browser->get('admin')->getCode(), 200);
	}
}