<?php
if(!defined('_ENV_'))
	define('_ENV_', 'test');
require_once(dirname(__FILE__).'/../../../coxis/core/core.php');
Coxis::load();

class ActualiteAdminTest extends PHPUnit_Framework_TestCase {
	public function setUp(){
		\Schema::dropAll();
		ORMManager::autobuild();
		\BundlesManager::loadModelFixturesAll();
	}
	public function tearDown(){}

	public function test1() {
		$browser = new Browser;
		$browser->session['admin_id'] = 1;

		$browser->get('admin/actualites/1/edit');
		if($browser->last->getCode() == 404)
			return;
		// $res = $browser->submit(0, 'admin/actualites/1/edit', array('actualite'=>array('content'=>'ééé')));
		$res = $browser->submit(0, 'admin/actualites/1/edit');
		$this->assertTrue($res->getCode() < 300);
	}
}
