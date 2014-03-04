<?php
class EntityTest extends PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		if(!defined('_ENV_'))
			define('_ENV_', 'test');
		require_once(_CORE_DIR_.'core.php');
		\Asgard\Core\App::instance(true)->config->set('bundles', array(
			_ASGARD_DIR_.'core',
			_ASGARD_DIR_.'orm',
			_ASGARD_DIR_.'files',
		));
		\Asgard\Core\App::loadDefaultApp();

		\Asgard\Core\App::get('db')->import('tests/asgard.sql');
	}
	
	#hasMany
	public function test1() {
		$actu = new \Tests\App\Actualite\Entities\Actualite(2);
		$coms = $actu->commentaires;
		$this->assertCount(1, $coms);
		$this->assertInstanceOf('Tests\App\Actualite\Entities\Commentaire', $coms[0]);
	}
    
	#belongsTo
	public function test2() {
		$com = new \Tests\App\Actualite\Entities\Commentaire(2);
		$actu = $com->actualite;
		$this->assertInstanceOf('Tests\App\Actualite\Entities\Actualite', $actu);
	}
    
	#HMABT
	public function test3() {
		$article = new \Tests\App\Article\Entities\Article(1);
		$authors = $article->authors;
		$this->assertTrue(is_array($authors));
		$this->assertInstanceOf('Tests\App\Article\Entities\Author', $authors[0]);
	}
    
	#load
	public function test4() {
		$article = \Tests\App\Article\Entities\Article::load(1);
	}
    
	#loadBy
	public function test5() {
		$article = \Tests\App\Article\Entities\Article::loadByTitle('Introduction');
		$this->assertEquals($article->id, 2);
	}

	#Entity errors
	public function test6() {
		$this->setExpectedException('Asgard\Core\EntityException');
		
		$actu = new \Tests\App\Actualite\Entities\Actualite(array(
			'titre'=>'le titre',
			'introduction'=>'introduction',
		));
		$actu->save();
	}
    
	#Entity save
	public function test7() {
		$actu = new \Tests\App\Actualite\Entities\Actualite(array(
			'titre'=>'le titre',
			'introduction'=>'introduction',
			'contenu'=>'contenu',
		));
		$actu->save();
	}
}
