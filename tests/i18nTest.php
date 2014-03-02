<?php
class i18nTest extends PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		if(!defined('_ENV_'))
			define('_ENV_', 'test');
		require_once(_CORE_DIR_.'core.php');
		\Coxis\Core\App::instance(true)->config->set('bundles', array(
			_COXIS_DIR_.'core',
			_COXIS_DIR_.'files',
			_COXIS_DIR_.'orm',
		));
		\Coxis\Core\App::loadDefaultApp();

		\Coxis\Core\App::get('db')->import('tests/coxis.sql');
	}

	#get default
	public function test1() {
		$com = new \Tests\App\Actualite\Entities\Commentaire(2);
		$actu = $com->actualite;
		$this->assertEquals('Hello', $actu->test); #default language is english
	}
    
	#save french text
	public function test2() {
		$com = new \Tests\App\Actualite\Entities\Commentaire(2);
		$actu = $com->actualite;
		$this->assertEquals('Bonjour', $actu->get('test', 'fr'));
	}
    
	#get english text
	public function test3() {
		$com = new \Tests\App\Actualite\Entities\Commentaire(2);
		$actu = $com->actualite;
		$this->assertEquals('Hello', $actu->get('test', 'en'));
	}
    
	#get all
	public function test4() {
		$com = new \Tests\App\Actualite\Entities\Commentaire(2);
		$actu = $com->actualite;
		$this->assertContains('Bonjour', $actu->get('test', 'all'));
		$this->assertContains('Hello', $actu->get('test', 'all'));
		$this->assertCount(2, $actu->get('test', 'all'));
	}
    
	#save english version
	public function test5() {
		\Coxis\Core\App::get('locale')->setLocale('en');
		$actu = new \Tests\App\Actualite\Entities\Actualite(2);
		$actu->test = 'Hi';
		// d($actu->data['properties']);
		$actu->save(null, true);
		$dal = new \Coxis\DB\DAL(\Coxis\Core\App::get('db'), \Coxis\Core\App::get('config')->get('database/prefix').'actualite_translation');
		$r = $dal->where(array('locale'=>'en', 'id'=>2))->first();
		$this->assertEquals('Hi', $r['test']);
	}
	
	#translation
	public function test6() {
		\Coxis\Core\App::get('locale')->setLocale('fr');
		\Coxis\Core\App::get('locale')->importLocales('tests/locales');
		$this->assertEquals(__('Hello :name!', array('name' => 'Michel')), 'Bonjour Michel !');
	}
}
?>