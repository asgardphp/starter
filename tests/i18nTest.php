<?php
if(!defined('_ENV_'))
	define('_ENV_', 'test');
require_once(dirname(__FILE__).'/../coxis/core/core.php');
// \Config::set('bundle_directories', array_merge(\Config::get('bundle_directories'), array('tests/app')));
\Coxis::load();

class i18nTest extends PHPUnit_Framework_TestCase {
	public function setUp(){
		\DB::import('tests/coxis.sql');
				
		\Context::get('locale')->setLocale('fr');
	}

	public function tearDown(){}
	
	public function test0() {
	}

	#get default
	public function test1() {
		$com = new \Tests\App\Actualite\Models\Commentaire(2);
		$actu = $com->actualite;
		$this->assertEquals('Bonjour', $actu->test);
	}
    
	#save french text
	public function test2() {
		$com = new \Tests\App\Actualite\Models\Commentaire(2);
		$actu = $com->actualite;
		$this->assertEquals('Bonjour', $actu->get('test', 'fr'));
	}
    
	#get english text
	public function test3() {
		$com = new \Tests\App\Actualite\Models\Commentaire(2);
		$actu = $com->actualite;
		$this->assertEquals('Hello', $actu->get('test', 'en'));
	}
    
	#get all
	public function test4() {
		$com = new \Tests\App\Actualite\Models\Commentaire(2);
		$actu = $com->actualite;
		$this->assertContains('Bonjour', $actu->get('test', 'all'));
		$this->assertContains('Hello', $actu->get('test', 'all'));
		$this->assertCount(2, $actu->get('test', 'all'));
	}
    
	#save english version
	public function test5() {
		\Context::get('locale')->setLocale('en');
		$actu = new \Tests\App\Actualite\Models\Actualite(2);
		$actu->test = 'Hi';
		// d($actu->data['properties']);
		$actu->save(null, true);
		$dal = new \Coxis\Core\DB\DAL(Config::get('database', 'prefix').'actualite_translation');
		$r = $dal->where(array('locale'=>'en', 'id'=>2))->first();
		$this->assertEquals('Hi', $r['test']);
	}
	
	#translation
	public function test6() {
		\Context::get('locale')->importLocales('tests/locales');
		$this->assertEquals(__('Hello :name!', array('name' => 'Michel')), 'Bonjour Michel !');
	}
}
?>