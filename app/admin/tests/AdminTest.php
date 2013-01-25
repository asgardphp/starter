<?php
if(!defined('_ENV_'))
	define('_ENV_', 'test');
require_once(dirname(__FILE__).'/../../../coxis/core/core.php');
// \Config::set('bundle_directories', array_merge(\Config::get('bundle_directories'), array('tests/app')));
\BundlesManager::loadBundles();

class AdminTest extends PHPUnit_Framework_TestCase {
	public function setUp(){
		DB::import('tests/coxis.sql');
	}

	public function tearDown(){}

// controllers/AdminController.php
// controllers/AdministratorAdminController.php
// controllers/DefaultAdminController.php
// controllers/LoginController.php
// libs/AdminMenu.php
// libs/CoxisAdmin.php
// libs/controller/AdminParentController.php
// libs/controller/ModelAdminController.php
// libs/form/AdminForm.php
// libs/form/AdminModelForm.php
// libs/form/AdminSimpleForm.php
// libs/form/SimpleAdminForm.php

	public function test0() {
	}
}
?>