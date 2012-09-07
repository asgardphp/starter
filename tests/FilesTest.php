<?php
if(!defined('_ENV_'))
	define('_ENV_', 'test');
require_once(dirname(__FILE__).'/../coxis.php');
BundlesManager::$directories[] = 'tests/app';
Coxis::load();

class FilesTest extends PHPUnit_Framework_TestCase {
	public function setUp(){
		DB::import('tests/coxis.sql');
	}

	public function tearDown(){}

	public function test0() {
		try {
			unlink('C:\Users\leyou\Documents\projects\coxis3\web\upload\actualite\test.jpg');
		} catch(\Exception $e) {}
		$actualite = new \Coxis\Tests\App\Actualite\Models\Actualite(2);
		// d();
		// d(actualite::$properties);
		// d($actualite);
		$file = array(
			'tmp_name' => 'C:\Users\leyou\Documents\projects\coxis3\tests\test.jpg',
			'name' => 'test.jpg',
			'type' => 'image/png',
			'size'	=>	'10',
			'error'	=>	'0',
		);
		#tmp path
		#tmp name
		#final path
		// d($actualite);
		$actualite->image = $file;
		// d($actualite->errors());
		// d($actualite);
		$actualite->save();
		// d($actualite);
		$this->assertTrue(file_exists('C:\Users\leyou\Documents\projects\coxis3\web\upload\actualite\test.jpg'));
	}
}
?>