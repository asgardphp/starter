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
		return DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.TABLES 
                 WHERE TABLE_SCHEMA = '".Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$table'")->count() > 0;
	}
		
	private function columnExists($table, $column) {
		return DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = '".Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$table'
		 AND COLUMN_NAME = '$column'")->count() > 0;
	}
	
	private function isAutoincrement($table, $column) {
		return DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = '".Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$table'
		 AND COLUMN_NAME = '$column'
		 AND EXTRA LIKE '%auto_increment%'")->count() > 0;
	}
	
	private function isNullable($table, $column) {
		return DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = '".Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$table'
		 AND COLUMN_NAME = '$column'
		 AND IS_NULLABLE = 'YES'")->count() > 0;
	}
	
	private function getDefault($table, $column) {
		$r = DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = '".Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$table'
		 AND COLUMN_NAME = '$column'")->first();
		return $r['COLUMN_DEFAULT'];
	}
	
	private function getDataType($table, $column) {
		$r = DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = '".Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$table'
		 AND COLUMN_NAME = '$column'")->first();
		return $r['DATA_TYPE'];
	}
	
	private function getType($table, $column) {
		$r = DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = '".Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$table'
		 AND COLUMN_NAME = '$column'")->first();
		return $r['COLUMN_TYPE'];
	}
	
	private function getLength($table, $column) {
		$r = DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = '".Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$table'
		 AND COLUMN_NAME = '$column'")->first();
		return $r['CHARACTER_MAXIMUM_LENGTH'];
	}
		
	private function isPrimary() {
		return DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = '".Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$table'
		 AND COLUMN_NAME = '$column'
		 AND COLUMN_KEY = 'PRI'")->count() > 0;
	}
		
	private function isUnique() {
		return DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = '".Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$table'
		 AND COLUMN_NAME = '$column'
		 AND COLUMN_KEY = 'UNI'")->count() > 0;
	}
		
	private function isIndex() {
		return DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = '".Config::get('database', 'database')."' 
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
	public function test1() {
		$this->assertTrue($this->tableExists('arpa_actualite'));
		$this->assertTrue($this->columnExists('arpa_actualite', 'id'));
	}
	
	
}
?>