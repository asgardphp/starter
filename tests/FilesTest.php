<?php
if(!defined('_ENV_'))
	define('_ENV_', 'test');
require_once(dirname(__FILE__).'/../coxis.php');
// \Config::set('bundle_directories', array_merge(\Config::get('bundle_directories'), array('tests/app')));
\Coxis::load();

class FilesTest extends PHPUnit_Framework_TestCase {
	public function setUp(){
		\DB::import('tests/coxis.sql');
	}

	public function tearDown(){}

	public function test0() {
		try {
			unlink('web\upload\actualite\test.jpg');
		} catch(\Exception $e) {}
		$actualite = new \Tests\App\Actualite\Models\Actualite(2);
		// d();
		// d(actualite::$properties);
		// d($actualite);
		$file = array(
			'path' => 'tests\test.jpg',
			'name' => 'test.jpg',
			// 'tmp_name' => 'tests\test.jpg',
			// 'name' => 'test.jpg',
			// 'type' => 'image/png',
			// 'size'	=>	'10',
			// 'error'	=>	'0',
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
		$this->assertTrue(file_exists('web\upload\actualite\test.jpg'), "Upload of test.jpg failed");
	}
}
?>