<?php
if(!defined('_ENV_'))
	define('_ENV_', 'test');
require_once(_CORE_DIR_.'core.php');
\Coxis::load();

class SchemaTest extends PHPUnit_Framework_TestCase {
	public function setUp(){
		$db = \Config::get('database', 'database');
		try {
			\DB::query('DROP DATABASE `'.$db.'`');
		} catch(Exception $e) {}
		\DB::query('CREATE DATABASE `'.$db.'`');
		\DB::query('USE `'.$db.'`');
		
		\DB::import('tests/coxis.sql');
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
		
	protected function tableExists($table) {
		return \DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.TABLES 
                 WHERE TABLE_SCHEMA = '".\Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$table'")->count() > 0;
	}
		
	protected function columnExists($table, $column) {
		return \DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = '".\Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$table'
		 AND COLUMN_NAME = '$column'")->count() > 0;
	}
	
	protected function isAutoincrement($table, $column) {
		return \DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = '".\Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$table'
		 AND COLUMN_NAME = '$column'
		 AND EXTRA LIKE '%auto_increment%'")->count() > 0;
	}
	
	protected function isNullable($table, $column) {
		return \DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = '".\Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$table'
		 AND COLUMN_NAME = '$column'
		 AND IS_NULLABLE = 'YES'")->count() > 0;
	}
	
	protected function getDefault($table, $column) {
		$r = \DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = '".\Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$table'
		 AND COLUMN_NAME = '$column'")->first();
		return $r['COLUMN_DEFAULT'];
	}
	
	protected function getDataType($table, $column) {
		$r = \DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = '".\Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$table'
		 AND COLUMN_NAME = '$column'")->first();
		return $r['DATA_TYPE'];
	}
	
	protected function getType($table, $column) {
		$r = \DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = '".\Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$table'
		 AND COLUMN_NAME = '$column'")->first();
		return $r['COLUMN_TYPE'];
	}
	
	protected function getLength($table, $column) {
		$r = \DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = '".\Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$table'
		 AND COLUMN_NAME = '$column'")->first();
		return $r['CHARACTER_MAXIMUM_LENGTH'];
	}
		
	protected function isPrimary() {
		return \DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = '".\Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$table'
		 AND COLUMN_NAME = '$column'
		 AND COLUMN_KEY = 'PRI'")->count() > 0;
	}
		
	protected function isUnique() {
		return \DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = '".\Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$table'
		 AND COLUMN_NAME = '$column'
		 AND COLUMN_KEY = 'UNI'")->count() > 0;
	}
		
	protected function isIndex() {
		return \DB::query("SELECT * 
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
		\Coxis\DB\Schema::create('test', function($table) {
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
		\Coxis\DB\Schema::create('test', function($table) {
			$table->add('id', 'int', 11);
		});
		
		\Coxis\DB\Schema::drop('test');
	}
	
	public function test2() {
		\Coxis\DB\Schema::create('test', function($table) {
			$table->add('id', 'int', 11);
		});
		
		\Coxis\DB\Schema::rename('test', 'test2');
	}
	
	public function test3() {
		\Coxis\DB\Schema::create('test', function($table) {
			$table->add('id', 'int', 11);
		});
		
		\Coxis\DB\Schema::table('test', function($table) {
			$table->add('title', 'text');
		});
	}
	
	public function test4() {
		\Coxis\DB\Schema::create('test', function($table) {
			$table->add('id', 'int', 11);
			$table->add('title', 'text');
		});
		
		\Coxis\DB\Schema::dropColumn('test', 'title');
	}
	
	public function test5() {
		\Coxis\DB\Schema::create('test', function($table) {
			$table->add('id', 'int', 11);
			$table->add('title', 'text');
		});
		
		\Coxis\DB\Schema::renameColumn('test', 'title', 'title2');
	}
	
	public function test6() {
		\Coxis\DB\Schema::create('test', function($table) {
			$table->add('id', 'int', 11);
			$table->add('title', 'text');
		});
		
		\Coxis\DB\Schema::table('test', function($table) {
			$table->col('title')
				->type('varchar', 50)
				->rename('title2')
				->nullable()
				->notNullable()
				->def('the title');
		});
	}
	
	public function test7() {
		\Coxis\DB\Schema::create('test', function($table) {
			$table->add('id', 'int', 11);
			$table->add('title', 'varchar', 50);
		});
		
		\Coxis\DB\Schema::table('test', function($table) {
			$table->col('title')
				->dropIndex()
				->unique();
		});
	}
	
	public function test8() {
		\Coxis\DB\Schema::create('test', function($table) {
			$table->add('id', 'int', 11);
			$table->add('title', 'varchar', 50);
		});
		
		\Coxis\DB\Schema::table('test', function($table) {
			$table->col('title')
				->dropIndex()
				->index();
		});
	}
	
	public function test9() {
		\Coxis\DB\Schema::create('test', function($table) {
			$table->add('id', 'int', 11);
			$table->add('title', 'varchar', 50);
		});
		
		\Coxis\DB\Schema::table('test', function($table) {
			$table->primary(array('id', 'title'));
		});
	}
}
