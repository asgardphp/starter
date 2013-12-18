<?php
if(!defined('_ENV_'))
	define('_ENV_', 'test');
require_once(_CORE_DIR_.'core.php');
\Coxis\Core\App::load();

class EntityTest extends PHPUnit_Framework_TestCase {
	public function setUp(){
		\DB::import('tests/coxis.sql');
	}

	public function tearDown(){}

	public function test0() {
	}

	#Entity errors
	public function test1() {
		$this->setExpectedException('Coxis\Core\EntityException');
		
		$actu = new \Tests\App\Actualite\Entities\Actualite(array(
			'titre'=>'le titre',
			'introduction'=>'introduction',
		));
		$actu->save();
	}
    
	#Entity save
	public function test2() {
		$actu = new \Tests\App\Actualite\Entities\Actualite(array(
			'titre'=>'le titre',
			'introduction'=>'introduction',
			'contenu'=>'contenu',
		));
		$actu->save();
	}
    
	#hasMany
	public function test3() {
		$actu = new \Tests\App\Actualite\Entities\Actualite(2);
		$coms = $actu->commentaires;
		$this->assertCount(1, $coms);
		$this->assertInstanceOf('Tests\App\Actualite\Entities\Commentaire', $coms[0]);
	}
    
	#belongsTo
	public function test4() {
		$com = new \Tests\App\Actualite\Entities\Commentaire(2);
		$actu = $com->actualite;
		$this->assertInstanceOf('Tests\App\Actualite\Entities\Actualite', $actu);
	}
    
	#HMABT
	public function test5() {
		$article = new \Tests\App\Article\Entities\Article(1);
		$authors = $article->authors;
		$this->assertTrue(is_array($authors));
		$this->assertInstanceOf('Tests\App\Article\Entities\Author', $authors[0]);
	}
    
	#load
	public function test6() {
		$article = \Tests\App\Article\Entities\Article::load(1);
	}
    
	#loadBy
	public function test7() {
		$article = \Tests\App\Article\Entities\Article::loadByTitle('Introduction');
		$this->assertEquals($article->id, 2);
	}
}
