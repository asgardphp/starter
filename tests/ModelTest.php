<?php
if(!defined('_ENV_'))
	define('_ENV_', 'test');
require_once(dirname(__FILE__).'/../coxis/core/core.php');
// \Config::set('bundle_directories', array_merge(\Config::get('bundle_directories'), array('tests/app')));
\Coxis::load();

class ModelTest extends PHPUnit_Framework_TestCase {
	public function setUp(){
		\DB::import('tests/coxis.sql');
	}

	public function tearDown(){}

	public function test0() {
	}

	#model errors
	public function test1() {
		$this->setExpectedException('Coxis\Core\ModelException');
		
		$actu = new \Tests\App\Actualite\Models\Actualite(array(
			'titre'=>'le titre',
			'introduction'=>'introduction',
		));
		$actu->save();
	}
    
	#model save
	public function test2() {
		$actu = new \Tests\App\Actualite\Models\Actualite(array(
			'titre'=>'le titre',
			'introduction'=>'introduction',
			'contenu'=>'contenu',
		));
		$actu->save();
	}
    
	#hasMany
	public function test3() {
		$actu = new \Tests\App\Actualite\Models\Actualite(2);
		$coms = $actu->commentaires;
		$this->assertCount(1, $coms);
		$this->assertInstanceOf('Tests\App\Actualite\Models\Commentaire', $coms[0]);
	}
    
	#belongsTo
	public function test4() {
		$com = new \Tests\App\Actualite\Models\Commentaire(2);
		$actu = $com->actualite;
		$this->assertInstanceOf('Tests\App\Actualite\Models\Actualite', $actu);
	}
    
	#HMABT
	public function test5() {
		$article = new \Tests\App\Article\Models\Article(1);
		$authors = $article->authors;
		$this->assertTrue(is_array($authors));
		$this->assertInstanceOf('Tests\App\Article\Models\Author', $authors[0]);
	}
    
	#load
	public function test6() {
		$article = \Tests\App\Article\Models\Article::load(1);
	}
    
	#loadBy
	public function test7() {
		$article = \Tests\App\Article\Models\Article::loadByTitle('Introduction');
		$this->assertEquals($article->id, 2);
	}
}
