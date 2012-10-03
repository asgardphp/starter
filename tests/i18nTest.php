<?php
if(!defined('_ENV_'))
	define('_ENV_', 'test');
require_once(dirname(__FILE__).'/../coxis.php');
\Coxis\Core\BundlesManager::$directories[] = 'tests/app';
require_once('core/load.php');

class i18nTest extends PHPUnit_Framework_TestCase {
	public function setUp(){
		\Coxis\Core\DB\DB::import('tests/coxis.sql');
				
		\Coxis\Core\Tools\Locale::setLocale('fr');
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
		\Coxis\Core\Tools\Locale::setLocale('en');
		$actu = new \Coxis\Tests\App\Actualite\Models\Actualite(2);
		$actu->test = 'Hi';
		// d($actu->data['properties']);
		$actu->save(null, true);
		$dal = new \Coxis\Core\DB\DAL(Config::get('database', 'prefix').'actualite_translation');
		$r = $dal->where(array('locale'=>'en', 'id'=>2))->first();
		$this->assertEquals('Hi', $r['test']);
	}
	
	#translation
	public function test6() {
		\Coxis\Core\Tools\Locale::importLocales('tests/locales');
		$this->assertEquals(__('Hello :name!', array('name' => 'Michel')), 'Bonjour Michel !');
	}
}
?>