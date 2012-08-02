<?php
if(!defined('_ENV_'))
	define('_ENV_', 'test');
require_once(dirname(__FILE__).'/../coxis.php');
BundlesManager::$directories[] = 'tests/app';
Coxis::load();

class CoxisTest extends PHPUnit_Framework_TestCase {
	public function setUp(){
		$host = Config::get('database', 'host');
		$user = Config::get('database', 'user');
		$pwd = Config::get('database', 'password');
		$db = Config::get('database', 'database');
		//~ $cmd = 'mysql -h '.$host.' -u '.$user.' -p'.$pwd.' '.$db.' < tests/coxis3.sql';
		$cmd = 'mysql -h '.$host.' -u '.$user.' '.$db.' < tests/coxis.sql';
		exec($cmd);
		#test database
			//~ load it at start
				//~ create test schema like dev schema
					#php definitions
						#don't need migrations for test
					#sql definitions
						#may be optionnaly supported later
				//~ data: fixtures
					#yml <=> sql tables
	}

	public function tearDown(){}

	#model errors
	public function test1() {
		$this->setExpectedException('Coxis\Core\ModelException');
		
		$actu = new \Coxis\Tests\App\Actualite\Models\Actualite(array(
			'titre'=>'le titre',
			'introduction'=>'introduction',
		));
		$actu->save();
	}
    
	#model save
	public function test2() {
		$actu = new \Coxis\Tests\App\Actualite\Models\Actualite(array(
			'titre'=>'le titre',
			'introduction'=>'introduction',
			'contenu'=>'contenu',
		));
		$actu->save();
	}
    
	#hasMany
	public function test3() {
		$actu = new \Coxis\Tests\App\Actualite\Models\Actualite(2);
		$coms = $actu->commentaires;
		$this->assertCount(1, $coms);
		$this->assertInstanceOf('Coxis\Tests\App\Actualite\Models\Commentaire', $coms[0]);
	}
    
	#belongsTo
	public function test4() {
		$com = new \Coxis\Tests\App\Actualite\Models\Commentaire(2);
		$actu = $com->actualite;
		$this->assertInstanceOf('Coxis\Tests\App\Actualite\Models\Actualite', $actu);
	}
    
	#HMABT
	public function test5() {
		$article = new \Coxis\Tests\App\Article\Models\Article(1);
		$authors = $article->authors;
		$this->assertTrue(is_array($authors));
		$this->assertInstanceOf('Coxis\Tests\App\Actualite\Models\Author', $authors[0]);
	}
}
?>