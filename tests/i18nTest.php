<?php
if(!defined('_ENV_'))
	define('_ENV_', 'test');
require_once(dirname(__FILE__).'/../coxis.php');
BundlesManager::$directories[] = 'tests/app';
Coxis::load();

class i18nTest extends PHPUnit_Framework_TestCase {
	public function setUp(){
		DB::import('tests/coxis.sql');
				
		Locale::setLocale('fr');
	}

	public function tearDown(){}
	
	#get default
	public function test1() {
		$com = new \Coxis\Tests\App\Actualite\Models\Commentaire(2);
		$actu = $com->actualite;
		$this->assertEquals($actu->test, 'Bonjour');
	}
    
	#save french text
	public function test2() {
		$com = new \Coxis\Tests\App\Actualite\Models\Commentaire(2);
		$actu = $com->actualite;
		$this->assertEquals($actu->getTest('fr'), 'Bonjour');
	}
    
	#get english text
	public function test3() {
		$com = new \Coxis\Tests\App\Actualite\Models\Commentaire(2);
		$actu = $com->actualite;
		$this->assertEquals($actu->getTest('en'), 'Hello');
	}
    
	#get all
	public function test4() {
		$com = new \Coxis\Tests\App\Actualite\Models\Commentaire(2);
		$actu = $com->actualite;
		$this->assertContains('Bonjour', $actu->getTest('all'));
		$this->assertContains('Hello', $actu->getTest('all'));
		$this->assertCount(2, $actu->getTest('all'));
	}
    
	#save english version
	public function test5() {
		//~ Config::set('locale', 'en');
		Locale::setLocale('en');
		$actu = new \Coxis\Tests\App\Actualite\Models\Actualite(2);
		$actu->test = 'Hi';
		$actu->save(null, true);
		$dal = new DAL(Config::get('database', 'prefix').'actualite_translation');
		$r = $dal->where(array('locale'=>'en', 'id'=>2))->first();
		$this->assertEquals('Hi', $r['test']);
	}
	
	#translation
	public function test6() {
		Locale::importLocales('tests/locales');
		$this->assertEquals(__('Hello :name!', array('name' => 'Michel')), 'Bonjour Michel !');
	}
}
?>