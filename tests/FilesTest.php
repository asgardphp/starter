<?php
if(!defined('_ENV_'))
	define('_ENV_', 'test');
require_once(dirname(__FILE__).'/../coxis.php');
\Coxis\Core\BundlesManager::$directories[] = 'tests/app';
require_once('core/load.php');

class FilesTest extends PHPUnit_Framework_TestCase {
	public function setUp(){
		\Coxis\Core\DB\DB::import('tests/coxis.sql');
	}

	public function tearDown(){}

	public function test0() {
		try {
			unlink('web\upload\actualite\test.jpg');
		} catch(\Exception $e) {}
		$actualite = new \Coxis\Tests\App\Actualite\Models\Actualite(2);
		// d();
		// d(actualite::$properties);
		// d($actualite);
		$file = array(
			'tmp_name' => 'tests\test.jpg',
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
		$this->assertTrue(file_exists('web\upload\actualite\test.jpg'), "Upload of test.jpg failed");
	}
}
?>