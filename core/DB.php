<?php
namespace Coxis\Core;

class DBException extends \Exception {}

class DB {
	private $db;
	private static $instance;

	public function __construct() {
		$config = Config::get('database');
		$this->db = new \PDO('mysql:host='.$config['host'].';dbname='.$config['database'], 
			$config['user'],
			$config['password'],
			array(\PDO::MYSQL_ATTR_FOUND_ROWS => true)
		);
		$this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	}
	
	public function getDB() {
		$instance = static::getInstance();
		
		return $instance->db;
	}

	public function query($sql) {
		$instance = static::getInstance();
	
		return new Query($instance->db, $sql);
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
}

class Query {
	private $db;
	private $rsc;

	public function __construct($db, $sql) {
		$this->db = $db;
		try {
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