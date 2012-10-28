<?php
if(!defined('_ENV_'))
	define('_ENV_', 'test');
require_once(dirname(__FILE__).'/../coxis.php');
\Config::set('bundle_directories', array('bundles', 'tests/app'));
\BundlesManager::loadBundles();

require_once 'vendors/phpQuery/phpQuery/phpQuery.php';

if(!function_exists('_pq')) {
	function _pq($html, $selector) {
		$doc = phpQuery::newDocument($html);
		phpQuery::selectDocument($doc);
		return pq($selector);
	}
}

class AppTest extends PHPUnit_Framework_TestCase {
	public function setUp(){
		\Schema::dropAll();
		ORMManager::autobuild();
		\BundlesManager::loadModelFixturesAll();
	}

	public function tearDown(){}

	public function test0() {
				$browser = new Browser;
				$this->assertEquals(200, $browser->get('/actualites')->getCode(), 'GET /actualites');
				
				/*
				$browser = new Browser;
				$this->assertEquals(200, $browser->get('/actualites/:id/:slug')->getCode(), 'GET /actualites/:id/:slug');
				*/
				
				$browser = new Browser;
				$this->assertEquals(200, $browser->get('/admin/actualites')->getCode(), 'GET /admin/actualites');
				
				/*
				$browser = new Browser;
				$this->assertEquals(200, $browser->get('/admin/actualites/:id/:file/add')->getCode(), 'GET /admin/actualites/:id/:file/add');
				*/
				
				/*
				$browser = new Browser;
				$this->assertEquals(200, $browser->get('/admin/actualites/:id/:file/delete/:pos')->getCode(), 'GET /admin/actualites/:id/:file/delete/:pos');
				*/
				
				/*
				$browser = new Browser;
				$this->assertEquals(200, $browser->get('/admin/actualites/:id/delete')->getCode(), 'GET /admin/actualites/:id/delete');
				*/
				
				/*
				$browser = new Browser;
				$this->assertEquals(200, $browser->get('/admin/actualites/:id/deletefile/:file')->getCode(), 'GET /admin/actualites/:id/deletefile/:file');
				*/
				
				/*
				$browser = new Browser;
				$this->assertEquals(200, $browser->get('/admin/actualites/:id/edit')->getCode(), 'GET /admin/actualites/:id/edit');
				*/
				
				/*
				$browser = new Browser;
				$this->assertEquals(200, $browser->get('/admin/actualites/hooks/:route')->getCode(), 'GET /admin/actualites/hooks/:route');
				*/
				
				$browser = new Browser;
				$this->assertEquals(200, $browser->get('/admin/actualites/new')->getCode(), 'GET /admin/actualites/new');
				
			}
		}
		