<?php
class Database {
	private $db;
	private static $instance;

	public function __construct() {
	//d(MyConfig::$database);
		$config = Config::get('database');
		$this->db = mysql_connect($config['host'], $config['username'], $config['password']);
		mysql_select_db($config['database'], $this->db);
	}
	
	public function getDB() {
		return $this->db;
	}
	
	public function select($tables, $conditions=array(), $fields=null) {
		$sql = 'SELECT ';
		if(!$fields)
			$sql .= '* ';
		else
			$sql .= implode($fields, ', ').' ';
		$sql .= 'FROM ';
		
		if(!is_array($tables))
			$tables = array($tables);
		$i=1;
		foreach($tables as $key => $table) {
			if(is_int($key))
				$sql .= '`'.Config::get('database', 'prefix').$table.'`';
			else
				$sql .= '`'.Config::get('database', 'prefix').$table.'` '.$key.($i++ < sizeof($tables) ? ', ':'');
		}
		if(isset($conditions['conditions'])) {
			$sql .= ' WHERE '.static::formatConditions('AND', $conditions['conditions']);
		}
		
		if(isset($conditions['order_by']))
			$sql .= ' ORDER BY '.$conditions['order_by'];
		
		return $this->query($sql, array());
	}

	public function update($model, $conditions, $values) {
		unset($values['id']);
		$args = array();

		$sql = 'UPDATE `'.Config::get('database', 'prefix').$model.'` SET ';

		foreach($values as $key=>$value) {
			$arr[] = '`'.$key.'`=?';
			$args[] = $value;
		}
		$sql .= implode(', ', $arr).' WHERE ';

		$arr = array();
		foreach($conditions as $key=>$value) {
			$arr[] = '`'.$key.'`=?';
			$args[] = $value;
		}
		$sql .= implode(' AND ', $arr);

		return $this->query($sql, $args);
	}

	public function insert($model, $values) {
		foreach($values as $key=>$value) {
			$keysArr[] = '`'.$key.'`';
			$valuesArr[] = '?';
		}
		$keysStr = implode(', ', $keysArr);
		$valuesStr = implode(', ', $valuesArr);
		
		$sql = 'INSERT INTO `'.Config::get('database', 'prefix').$model.'` ('.$keysStr.') VALUES ('.$valuesStr.')';
		
		return $this->query($sql, array_values($values));
	}

	public function delete($model, $conditions=array()) {
		$args = array();

		$sql = 'DELETE FROM `'.Config::get('database', 'prefix').$model.'`';
		if(sizeof($conditions) > 0) {
			$sql .= ' WHERE ';

			$arr = array();
			foreach($conditions as $key=>$value) {
				$arr[] = '`'.$key.'`=?';
				$args[] = $value;
			}
			$sql .= implode(' AND ', $arr);
		}

		return $this->query($sql, $args);
	}

	public function query($sql, $args=array()) {
		$sql = $this->format($sql, $args);

		return new Query($this->db, $sql);
	}

	public function id() {
		return mysql_insert_id($this->db);
	}
	
	public function format($sql, $args=array()) {
		if(!is_array($args))
			$args = array();
			
		foreach($args as $k=>$v)
			$args[$k] = mysql_real_escape_string($v, $this->db);
			
		$format_str = str_replace('%', '%%', $sql);
		$format_str = str_replace('?', "'%s'", $format_str);
		
		if($args) {
			array_unshift($args, $format_str);
			
			$sql = call_user_func_array('sprintf', $args);
		}
		
		return $sql;
	}

	public static function getInstance() { 
		if(!static::$instance)	static::$instance = new self();

		return static::$instance; 
	} 
	
	public static function formatConditions($context, $conditions) {
		switch(strtolower($context)) {
			case 'or':
				$statements = array();
				foreach($conditions as $this_context=>$this_condition) {
					$statements[] = static::formatConditions($this_context, $this_condition);
				}
				$sql = implode(' OR ', $statements);
				break;
			case 'and':
				$statements = array();
				foreach($conditions as $this_context=>$this_condition) {
					$statements[] = static::formatConditions($this_context, $this_condition);
				}
				$sql = implode(' AND ', $statements);
				break;
			default:
				#e.g. 'a.'.$relation_id_field.'=b.id',
				if(is_int($context))
					$sql = Database::getInstance()->format($conditions);
				#e.g. 'a.'.$model_id_field.'=?'	=>	array($this->id),
				else
					$sql = Database::getInstance()->format($context, $conditions);
		}
		
		return '('.$sql.')';
	}
}

class Query {
	private $res;
	private $db;

	public function __construct($db, $sql) {
		$this->db = $db;
		$res = mysql_query($sql, $db);
		
		if(!$res)
			throw new Exception(mysql_error().'<br/>'."\n".'SQL: '.$sql);
		$this->res = $res;
	}

	public function affected_rows() {
		return mysql_affected_rows($this->db);
	}

	public function num() {
		return mysql_num_rows($this->res);
	}

	public function fetchOne() {
		$res = mysql_fetch_assoc($this->res);
		if(!$res)
			throw new Exception('no result found');
		return $res;
	}

	public function fetchAll() {
		$results = array();
		while($c = mysql_fetch_assoc($this->res)) {
			foreach($c as $k=>$v)
				if(is_numeric($v))
					$c[$k] = intval($v);
			$results[] = $c;
		}
		return $results;
	}
}