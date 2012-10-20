<?php
namespace Coxis\Core\DB;

class DBException extends \Exception {}

class DB {
	private $db;
	private static $instance;

	public function __construct($db=null) {
		$config = \Config::get('database');
		if(!$db) {
			$this->db = new \PDO('mysql:host='.$config['host'].';dbname='.$config['database'], 
				$config['user'],
				$config['password'],
				array(\PDO::MYSQL_ATTR_FOUND_ROWS => true)
			);
		}
		else
			$this->db = $db;
		$this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	}
	
	public function getDB() {
		$instance = static::getInstance();
		
		return $instance->db;
	}
	
	public static function import($file) {
		$host = \Config::get('database', 'host');
		$user = \Config::get('database', 'user');
		$pwd = \Config::get('database', 'password');
		$db = \Config::get('database', 'database');
		$cmd = 'mysql -h '.$host.' -u '.$user.($pwd ? ' -p'.$pwd:'').' '.$db.' < '.$file;
		exec($cmd);
	}

	public static function query($sql, $args=array()) {
		$instance = static::getInstance();
	
		return new Query($instance->db, $sql, $args);
	}

	public function id() {
		$instance = static::getInstance();
		
		return $instance->db->lastInsertId();
	}

	public static function getInstance() { 
		if(!static::$instance)
			static::$instance = new static;

		return static::$instance; 
	} 

	public static function newInstance($db=null) { 
		static::$instance = new static($db);

		return static::$instance; 
	} 
}

class Query {
	private $db;
	private $rsc;

	public function __construct($db, $sql, $args=array()) {
		$this->db = $db;
		try {
			if($args) {
				$rsc = $db->prepare($sql);
				$rsc->execute($args);
			}
			else
				$rsc = $db->query($sql);
			$this->rsc = $rsc;
		} catch(\PDOException $e) {
			throw new DBException($e->getMessage().'<br/>'."\n".'SQL: '.$sql);
		}
	}
	
	public function next() {
		return $this->rsc->fetch(\PDO::FETCH_ASSOC);
	}

	public function affected() {
		return $this->rsc->rowCount();
	}

	public function count() {
		return $this->rsc->rowCount();
	}

	public function first() {
		return $this->rsc->fetch(\PDO::FETCH_ASSOC);
	}

	public function all() {
		return $this->rsc->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function id() {
		return $this->db->lastInsertId();
	}
}