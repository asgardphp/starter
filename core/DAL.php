<?php
namespace Coxis\Core;

#todo select only some columns
class DAL {
	public $db = null;
	public $tables = null;
	
	public $where = null;
	public $offset = null;
	public $limit = null;
	public $orderBy = null;
	public $leftjoin = array();
	public $rightjoin = array();
		
	function __construct($tables) {
		$this->db = DB::getInstance();
		//~ $a = $tables;
		$this->setTables($tables);
		//~ if($a == 'arpa_actualite')
			//~ d($tables, $this->tables);
	}
	
	public function setTable($table, $alias='a') {
		$this->tables = array($table=>$alias);
		
		return $this;
	}
	
	public function setTables($tables) {
		if(!is_array($tables))
			$tables = array($tables);

		foreach($tables as $table=>$alias)
			if(is_int($table)) {
				$tables[$alias] = null;
				unset($tables[$table]);
			}
			
		$this->tables = $tables;
		
		return $this;
	}
	
	public function rightjoin($jointures) {
		$this->rightjoin = array_merge($this->rightjoin, $jointures);
		
		return $this;
	}
	
	public function leftjoin($jointures) {
		$this->leftjoin = array_merge($this->leftjoin, $jointures);
		
		return $this;
	}
	
	public function reset() {
		$this->where = null;
		$this->offset = null;
		$this->limit = null;
		
		return $this;
	}
	
	public function query($sql, $args) {
		$sql = static::replace($sql, $args);
		
		return $this->db->query($sql);
	}
		
	public function where($conditions) {
		if($this->where === null)
			$this->where = array();
			
		if(!$conditions)
			return $this;
		
		$this->where[] = static::parseConditions($conditions);
		
		return $this;
	}
	
	private static function parseConditions($conditions) {
		$res = array();
		
		if(is_array($conditions)) {
			foreach($conditions as $k=>$v)
				if(!is_int($k)) {
					$ar = array();
					$ar[$k] = static::parseConditions($v);
					$res[] = $ar;
				}
				else
					$res[] = static::parseConditions($v);
			return $res;
		}
		else
			return $conditions;
	}
		
	public function offset($offset) {
		$this->offset = $offset;
		return $this;
	}
		
	public function limit($limit) {
		$this->limit = $limit;
		return $this;
	}
		
	public function orderBy($orderBy) {
		$this->orderBy = $orderBy;
		return $this;
	}
		
	//~ public function with($arg1, $arg2) {
		//~ if(!is_array($arg1))
			//~ $with = array($arg1 => $arg2);
		//~ else
			//~ $with = $arg1;
		
		//~ if($this->with === null)
			//~ $this->with = array();
		//~ $this->with = array_merge($this->with, $with);
		
		//~ return $this;
	//~ }
	
	private static function processConditions($conditions, $join = 'and', $brackets=false, $table=null) {
		if(sizeof($conditions) == 0)
			return '';
		
		$string_conditions = '';
		
		if(!is_array($conditions))
			if($join == 'and')
				return $conditions;
			else
				return static::replace($join, $conditions, $table);
		else
			foreach($conditions as $key=>$value)
				if(is_int($key))
					$string_conditions[] = static::processConditions($value, 'and', false, $table);
				else
					$string_conditions[] = static::processConditions($value, $key, true, $table);
			
		$result = implode(' '.$join.' ', $string_conditions);
		
		if($brackets)
			return '('.$result.')';
		else
			return $result;
	}
	
	private static function replace($condition, $params, $table='') {
		if(!is_array($params))
			$params = array($params);
		
		#TODO MUST FIX THIS
		//~ foreach($params as $k=>$v)
			//~ $params[$k] = mysql_real_escape_string($v, $this->db);
			
		if(strpos($condition, '?') === false)
			if(preg_match('/^[a-zA-Z]+$/', $condition))
				if($table)
					$condition = $table.'.`'.$condition.'` = ?';
				else
					$condition = '`'.$condition.'` = ?';
			else
				$condition = $condition.' = ?';
		
		$format_str = str_replace('%', '%%', $condition);
		$format_str = str_replace('?', "'%s'", $format_str);
		
		if($params) {
			array_unshift($params, $format_str);
			$condition = call_user_func_array('sprintf', $params);
		}
		
		return $condition;
	}
	
	public function getTable($ref) {
		foreach($this->tables as $table=>$alias)
			if($alias == $ref)
				return $table;
		return null;
	}
	
	public function buildSQL() {
		$where = '';
		$orderBy = '';
		$leftjoin = '';
		$rightjoin = '';
		$limit = null;
		
		$tables = array();
		foreach($this->tables as $table=>$alias)
			if($alias)
				$tables[] = $table.' '.$alias;
			else
				$tables[] = $table;
		$sqltable = implode(', ', $tables);
		
		if(get(array_values($this->tables), 0))
			$default = get(array_values($this->tables), 0);
		else
			$default = get(array_keys($this->tables), 0);
		
		if($where = static::processConditions($this->where, 'and', false, $default))
			$where = ' WHERE '.$where;
		
		if($this->orderBy)
			$orderBy = ' ORDER BY '.$this->orderBy;
				
		if($this->limit || $this->offset) {
			$limit = ' LIMIT ';
			if($this->offset) {
				$limit .= $this->offset;
				if($this->limit)
					$limit .= ', '.$this->limit;
				else
					$limit .= ', 99999999';
			}
			else
				$limit .= $this->limit;
		}
				
		foreach($this->rightjoin as $tableName=>$conditions) {
			$table = $tableName;
			if(preg_match('/^([a-zA-Z]+).Translation /', $tableName, $matches)) {
				$ref_table = $this->getTable($matches[1]);
				$table = preg_replace('/^([a-zA-Z]+)/', $ref_table, $table);
				$table = str_replace('.Translation', '_translation', $table);
			}
			$rightjoin .= ' RIGHT JOIN '.$table.' ON '.static::processConditions($conditions);
		}
				
		foreach($this->leftjoin as $tableName=>$conditions) {
			$table = $tableName;
			if(preg_match('/^([a-zA-Z]+).Translation /', $tableName, $matches)) {
				$ref_table = $this->getTable($matches[1]);
				$table = preg_replace('/^([a-zA-Z]+)/', $ref_table, $table);
				$table = str_replace('.Translation', '_translation', $table);
			}
			$leftjoin .= ' LEFT JOIN '.$table.' ON '.static::processConditions($conditions);
		}
	
		return 'SELECT * FROM '.$sqltable.$rightjoin.$leftjoin.$where.$orderBy.$limit;
	}
	
	public function first() {
		$sql = $this->limit(1)->buildSQL();
		return $this->db->query($sql)->first();
	}
	
	public function get() {
		$sql = $this->buildSQL();
		return $this->db->query($sql)->all();
	}
	
	public function paginate($page, $per_page=10) {
		$offset = ($page-1)*$per_page;
		$limit = $per_page;
		
		//~ return static::where($this->where)->orderBy($this->orderBy)->offset($offset)->limit($limit)->get();
		return $this->offset($offset)->limit($limit)->get();
	}
	
	public function update($values) {
		$where = '';
		if($where = static::processConditions($this->where))
			$where = ' WHERE '.$where;
		
		if(sizeof($values) == 0)
			throw new Exception('Update values should not be empty.');
		
		$table = get(array_keys($this->tables), 0);
			
		$set = array();
		foreach($values as $k=>$v)
			$set[] = static::replace('`'.$k.'`=?', $v);
		$set = ' SET '.implode(', ', $set);
	
		$sql = 'UPDATE '.$table.$set.$where;
		
		return $this->db->query($sql)->affected();
	}
	
	public function insert($values) {
		if(sizeof($values) == 0)
			throw new Exception('Insert values should not be empty.');
		
		$table = get(array_keys($this->tables), 0);
			
		$columns = array();
		$vals = array();
		foreach($values as $k=>$v)
			$columns[] = '`'.$k.'`';
		foreach($values as $k=>$v)
			$vals[] = "'".$v."'";
		
		$str = ' ('.implode(', ', $columns).') VALUES ('.implode(', ', $vals).')';
	
		$sql = 'INSERT INTO '.$table.$str;
		
		return $this->db->query($sql)->id();
	}
	
	public function delete() {
		$where = '';
		if($where = static::processConditions($this->where))
			$where = ' WHERE '.$where;
	
		$sql = 'DELETE FROM '.$this->table.$where;
		
		return $this->db->query($sql)->affected();
	}
	
	public function count($group_by=null) {
		$where = '';
		if($where = static::processConditions($this->where))
			$where = ' WHERE '.$where;
	
		$tables = array();
		foreach($this->tables as $table=>$alias)
			if($alias)
				$tables[] = $table.' '.$alias;
			else
				$tables[] = $table;
		$sqltable = implode(', ', $tables);
		
		if($group_by) {
			$sql = 'SELECT `'.$group_by.'` as groupby, count(*) as total FROM '.$sqltable.$where.' GROUP BY '.$group_by;
			$res = array();
			foreach($this->db->query($sql)->all() as $v)
				$res[$v['groupby']] = $v['total'];
			return $res;
		}
		else {
			$sql = 'SELECT count(*) as total FROM '.$sqltable.$where;
			$res = $this->db->query($sql)->first();
			return $res['total'];
		}
	}
	
	public function min($what, $group_by=null) {
		$where = '';
		
		if($this->where)
			$where = ' WHERE '.static::processConditions($this->where);
	
		if($group_by) {
			$sql = 'SELECT `'.$group_by.'` as groupby, min(`'.$what.'`) as min FROM '.$this->table.$where.' GROUP BY '.$group_by;
			$res = array();
			foreach($this->db->query($sql)->all() as $v)
				$res[$v['groupby']] = $v['min'];
			return $res;
		}
		else {
			$sql = 'SELECT min(`'.$what.'`) as min FROM '.$this->table.$where;
			$res = $this->db->query($sql)->first();
			return $res['min'];
		}
	}
	
	public function max($what, $group_by=null) {
		$where = '';
		
		if($this->where)
			$where = ' WHERE '.static::processConditions($this->where);
	
		if($group_by) {
			$sql = 'SELECT `'.$group_by.'` as groupby, max(`'.$what.'`) as max FROM '.$this->table.$where.' GROUP BY '.$group_by;
			$res = array();
			foreach($this->db->query($sql)->all() as $v)
				$res[$v['groupby']] = $v['max'];
			return $res;
		}
		else {
			$sql = 'SELECT max(`'.$what.'`) as max FROM '.$this->table.$where;
			$res = $this->db->query($sql)->first();
			return $res['max'];
		}
	}
	
	public function avg($what, $group_by=null) {
		$where = '';
		
		if($this->where)
			$where = ' WHERE '.static::processConditions($this->where);
	
		if($group_by) {
			$sql = 'SELECT `'.$group_by.'` as groupby, avg(`'.$what.'`) as avg FROM '.$this->table.$where.' GROUP BY '.$group_by;
			$res = array();
			foreach($this->db->query($sql)->all() as $v)
				$res[$v['groupby']] = $v['avg'];
			return $res;
		}
		else {
			$sql = 'SELECT avg(`'.$what.'`) as avg FROM '.$this->table.$where;
			$res = $this->db->query($sql)->first();
			return $res['avg'];
		}
	}
	
	public function sum($what, $group_by=null) {
		$where = '';
		
		if($this->where)
			$where = ' WHERE '.static::processConditions($this->where);
	
		if($group_by) {
			$sql = 'SELECT `'.$group_by.'` as groupby, sum(`'.$what.'`) as sum FROM '.$this->table.$where.' GROUP BY '.$group_by;
			$res = array();
			foreach($this->db->query($sql)->all() as $v)
				$res[$v['groupby']] = $v['sum'];
			return $res;
		}
		else {
			$sql = 'SELECT sum(`'.$what.'`) as sum FROM '.$this->table.$where;
			$res = $this->db->query($sql)->first();
			return $res['sum'];
		}
	}
}