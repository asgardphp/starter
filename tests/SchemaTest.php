<?php
if(!defined('_ENV_'))
	define('_ENV_', 'test');
require_once(dirname(__FILE__).'/../coxis.php');
\BundlesManager::inst()->directories[] = 'tests/app';
\BundlesManager::loadBundles();

class SchemaTest extends PHPUnit_Framework_TestCase {
	public function setUp(){
		$db = \Config::get('database', 'database');
		try {
			\Coxis\Core\DB\DB::query('DROP DATABASE `'.$db.'`');
		} catch(Exception $e) {}
		\Coxis\Core\DB\DB::query('CREATE DATABASE `'.$db.'`');
		\Coxis\Core\DB\DB::query('USE `'.$db.'`');
		
		\Coxis\Core\DB\DB::import('tests/coxis.sql');
	}

	public function tearDown(){}

	/*
	create new table
	drop table
	change table name
	indexes
		primary: unique or composite
		unique
		fulltext
		index
	drop index
	#foreign keys
	
	add column
	definition:
		autoincrement
		type: varchar, text, integer, ..
		length:
		nullable
		default
	rename column
	drop column
	
	raw sql
	*/
		
	private function tableExists($table) {
		return \Coxis\Core\DB\DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.TABLES 
                 WHERE TABLE_SCHEMA = '".\Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$table'")->count() > 0;
	}
		
	private function columnExists($table, $column) {
		return \Coxis\Core\DB\DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = '".\Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$table'
		 AND COLUMN_NAME = '$column'")->count() > 0;
	}
	
	private function isAutoincrement($table, $column) {
		return \Coxis\Core\DB\DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = '".\Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$table'
		 AND COLUMN_NAME = '$column'
		 AND EXTRA LIKE '%auto_increment%'")->count() > 0;
	}
	
	private function isNullable($table, $column) {
		return \Coxis\Core\DB\DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = '".\Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$table'
		 AND COLUMN_NAME = '$column'
		 AND IS_NULLABLE = 'YES'")->count() > 0;
	}
	
	private function getDefault($table, $column) {
		$r = \Coxis\Core\DB\DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = '".\Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$table'
		 AND COLUMN_NAME = '$column'")->first();
		return $r['COLUMN_DEFAULT'];
	}
	
	private function getDataType($table, $column) {
		$r = \Coxis\Core\DB\DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = '".\Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$table'
		 AND COLUMN_NAME = '$column'")->first();
		return $r['DATA_TYPE'];
	}
	
	private function getType($table, $column) {
		$r = \Coxis\Core\DB\DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = '".\Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$table'
		 AND COLUMN_NAME = '$column'")->first();
		return $r['COLUMN_TYPE'];
	}
	
	private function getLength($table, $column) {
		$r = \Coxis\Core\DB\DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = '".\Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$table'
		 AND COLUMN_NAME = '$column'")->first();
		return $r['CHARACTER_MAXIMUM_LENGTH'];
	}
		
	private function isPrimary() {
		return \Coxis\Core\DB\DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = '".\Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$table'
		 AND COLUMN_NAME = '$column'
		 AND COLUMN_KEY = 'PRI'")->count() > 0;
	}
		
	private function isUnique() {
		return \Coxis\Core\DB\DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = '".\Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$table'
		 AND COLUMN_NAME = '$column'
		 AND COLUMN_KEY = 'UNI'")->count() > 0;
	}
		
	private function isIndex() {
		return \Coxis\Core\DB\DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = '".\Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$table'
		 AND COLUMN_NAME = '$column'
		 AND COLUMN_KEY = 'MUL'")->count() > 0;
	}
	
	//~ isFulltext()
//~ select group_concat(distinct column_name)
//~ from information_schema.STATISTICS 
//~ where table_schema = 'your_db' 
//~ and table_name = 'your_table' 
//~ and index_type = 'FULLTEXT';

	#create table
	//~ public function test1() {
		//~ $this->assertTrue($this->tableExists('arpa_actualite'));
		//~ $this->assertTrue($this->columnExists('arpa_actualite', 'id'));
	//~ }
	
	#nullable
	//~ ALTER TABLE mytable MODIFY mycolumn VARCHAR(255);
	#primary
	#index
	#unique
	#set type
	#rename
	#drop
	#add
	#create table
//~ CREATE TABLE `arpa_actualite` (
  //~ `id` int(11) NOT NULL AUTO_INCREMENT,
  //~ `titre` text,
  //~ `date` text,
  //~ `lieu` text,
  //~ `introduction` text,
  //~ `contenu` text,
  //~ `slug` text,
  //~ `position` int(11) NOT NULL,
  //~ `created_at` datetime NOT NULL,
  //~ `updated_at` datetime NOT NULL,
  //~ `filename_image` text,
  //~ `commentaire_id` int(1) NOT NULL,
  //~ PRIMARY KEY (`id`)
//~ );
	
	public function test0() {
		\Coxis\Core\DB\Schema::create('test', function($table) {
			$table->add('id', 'int', 11)
				->autoincrement();
			$table->add('title', 'varchar', 50)
				->nullable()
				->def('The title');
				
			$table->primary('id');
			$table->unique('title');
		});
	}
	
	public function test1() {
		\Coxis\Core\DB\Schema::create('test', function($table) {
			$table->add('id', 'int', 11);
		});
		
		\Coxis\Core\DB\Schema::drop('test');
	}
	
	public function test2() {
		\Coxis\Core\DB\Schema::create('test', function($table) {
			$table->add('id', 'int', 11);
		});
		
		\Coxis\Core\DB\Schema::rename('test', 'test2');
	}
	
	public function test3() {
		\Coxis\Core\DB\Schema::create('test', function($table) {
			$table->add('id', 'int', 11);
		});
		
		\Coxis\Core\DB\Schema::table('test', function($table) {
			$table->add('title', 'text');
		});
	}
	
	public function test4() {
		\Coxis\Core\DB\Schema::create('test', function($table) {
			$table->add('id', 'int', 11);
			$table->add('title', 'text');
		});
		
		\Coxis\Core\DB\Schema::dropColumn('test', 'title');
	}
	
	public function test5() {
		\Coxis\Core\DB\Schema::create('test', function($table) {
			$table->add('id', 'int', 11);
			$table->add('title', 'text');
		});
		
		\Coxis\Core\DB\Schema::renameColumn('test', 'title', 'title2');
	}
	
	public function test6() {
		\Coxis\Core\DB\Schema::create('test', function($table) {
			$table->add('id', 'int', 11);
			$table->add('title', 'text');
		});
		
		\Coxis\Core\DB\Schema::table('test', function($table) {
			$table->col('title')
				->type('varchar', 50)
				->rename('title2')
				->nullable()
				->notNullable()
				->def('the title');
		});
	}
	
	public function test7() {
		\Coxis\Core\DB\Schema::create('test', function($table) {
			$table->add('id', 'int', 11);
			$table->add('title', 'varchar', 50);
		});
		
		\Coxis\Core\DB\Schema::table('test', function($table) {
			$table->col('title')
				->dropIndex()
				->unique();
		});
	}
	
	public function test8() {
		\Coxis\Core\DB\Schema::create('test', function($table) {
			$table->add('id', 'int', 11);
			$table->add('title', 'varchar', 50);
		});
		
		\Coxis\Core\DB\Schema::table('test', function($table) {
			$table->col('title')
				->dropIndex()
				->index();
		});
	}
	
	public function test9() {
		\Coxis\Core\DB\Schema::create('test', function($table) {
			$table->add('id', 'int', 11);
			$table->add('title', 'varchar', 50);
		});
		
		\Coxis\Core\DB\Schema::table('test', function($table) {
			$table->primary(array('id', 'title'));
		});
	}
}
