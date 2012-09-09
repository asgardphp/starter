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
		$this->assertEquals('Bonjour', $actu->test);
	}
    
	#save french text
	public function test2() {
		$com = new \Coxis\Tests\App\Actualite\Models\Commentaire(2);
		$actu = $com->actualite;
		$this->assertEquals('Bonjour', $actu->get('test', 'fr'));
	}
    
	#get english text
	public function test3() {
		$com = new \Coxis\Tests\App\Actualite\Models\Commentaire(2);
		$actu = $com->actualite;
		$this->assertEquals('Hello', $actu->get('test', 'en'));
	}
    
	#get all
	public function test4() {
		$com = new \Coxis\Tests\App\Actualite\Models\Commentaire(2);
		$actu = $com->actualite;
		$this->assertContains('Bonjour', $actu->get('test', 'all'));
		$this->assertContains('Hello', $actu->get('test', 'all'));
		$this->assertCount(2, $actu->get('test', 'all'));
	}
    
	#save english version
	public function test5() {
		Locale::setLocale('en');
		$actu = new \Coxis\Tests\App\Actualite\Models\Actualite(2);
		$actu->test = 'Hi';
		// d($actu->data['properties']);
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