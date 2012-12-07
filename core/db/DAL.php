<?php
namespace Coxis\Core\DB;

#todo select only some columns
class DAL {
	public $db = null;
	public $tables = null;
	
	public $where = null;
	public $offset = null;
	public $limit = null;
	public $orderBy = null;
	public $groupBy = null;
	public $leftjoin = array();
	public $rightjoin = array();
	public $innerjoin = array();
		
	function __construct($tables, $db=null) {
		if($db === null)
			$this->db = \DB::inst();
		else
			$this->db = $db;
		$this->setTables($tables);
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
	
	public function innerjoin($jointures) {
		$this->innerjoin = array_merge($this->innerjoin, $jointures);
		
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
				if(is_int($k))
					$res[] = static::parseConditions($v);
				else {
					$ar = array();
					$ar[$k] = static::parseConditions($v);
					$res[] = $ar;
				}
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
		
	public function groupBy($groupBy) {
		$this->groupBy = $groupBy;
		return $this;
	}
	
	private static function processConditions($params, $condition = 'and', $brackets=false, $table=null) {
		if(sizeof($params) == 0)
			return array('', array());
		
		$string_conditions = '';
		
		if(!is_array($params))
			if($condition == 'and')
				return array($params, array());
			else
				return array(static::replace($condition, $table), array());

		$pdoparams = array();

		foreach($params as $key=>$value) {
			if(!is_array($value)) {
				if(is_int($key))
					$string_conditions[] = $value;
				else {
					$string_conditions[] = static::replace($key, $table);
					$pdoparams[] = $value;
				}
			}
			else {
				if(is_int($key)) {
					$r = static::processConditions($value, 'and', false, $table);
					$string_conditions[] = $r[0];
					$pdoparams[] = $r[1];
				}
				else {
					$r = static::processConditions($value, $key, true, $table);
					$string_conditions[] = $r[0];
					$pdoparams[] = $r[1];
				}
			}
		}

		$result = implode(' '.$condition.' ', $string_conditions);
		
		if($brackets)
			$result = '('.$result.')';
		
		return array($result, Tools::flateArray($pdoparams));
	}
	
	private static function replace($condition, $table='') {
		if(strpos($condition, '?') === false)
			if(preg_match('/^[a-zA-Z0-9_]+$/', $condition))
				if($table)
					$condition = $table.'.`'.$condition.'` = ?';
				else
					$condition = '`'.$condition.'` = ?';
			else
				$condition = $condition.' = ?';
		
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
		$groupBy = '';
		$leftjoin = '';
		$rightjoin = '';
		$innerjoin = '';
		$limit = null;

		$params = array();
		
		$tables = array();
		foreach($this->tables as $table=>$alias)
			if($alias)
				$tables[] = '`'.$table.'` '.$alias;
			else
				$tables[] = '`'.$table.'`';
		$sqltable = implode(', ', $tables);
		
		if(get(array_values($this->tables), 0))
			$default = get(array_values($this->tables), 0);
		else
			$default = get(array_keys($this->tables), 0);
		
		if($this->orderBy) {
			$orderBy = ' ORDER BY ';
			if(!is_array($this->orderBy))
				$orders = array($this->orderBy);
			else
				$orders = $this->orderBy;
			
			foreach($orders as $k=>$v)
				if(preg_match('/^[a-zA-Z0-9_ ]+$/', $v))
					$orders[$k] = $default.'.'.$v;
					
			$orderBy .= implode(', ', $orders);
		}
				
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
				
		#todo put ` around table only, ie `like` l and not `like l`
		foreach($this->rightjoin as $tableName=>$conditions) {
			$table = $tableName;
			if(preg_match('/^([a-zA-Z]+).Translation /', $tableName, $matches)) {
				$ref_table = $this->getTable($matches[1]);
				$table = preg_replace('/^([a-zA-Z]+)/', $ref_table, $table);
				$table = str_replace('.Translation', '_translation', $table);
			}
			$r = static::processConditions($conditions);
			$rightjoin .= ' RIGHT JOIN '.$table.' ON '.$r[0];
			$params = array_merge($params, $r[1]);
		}
		
		foreach($this->leftjoin as $tableName=>$conditions) {
			$table = $tableName;
			if(preg_match('/^([a-zA-Z]+).Translation /', $tableName, $matches)) {
				$ref_table = $this->getTable($matches[1]);
				$table = preg_replace('/^([a-zA-Z]+)/', $ref_table, $table);
				$table = str_replace('.Translation', '_translation', $table);
				#todo move it into ORM
			}
			$r = static::processConditions($conditions);
			$leftjoin .= ' LEFT JOIN '.$table.' ON '.$r[0];
			$params = array_merge($params, $r[1]);
		}
				
		foreach($this->innerjoin as $tableName=>$conditions) {
			$table = $tableName;
			if(preg_match('/^([a-zA-Z]+).Translation /', $tableName, $matches)) {
				$ref_table = $this->getTable($matches[1]);
				$table = preg_replace('/^([a-zA-Z]+)/', $ref_table, $table);
				$table = str_replace('.Translation', '_translation', $table);
				#todo move it into ORM
			}
			$r = static::processConditions($conditions);
			$innerjoin .= ' INNER JOIN '.$table.' ON '.$r[0];
			$params = array_merge($params, $r[1]);
		}
		
		$r = static::processConditions($this->where, 'and', false, $default);
		if($where = $r[0]) {
			$where = ' WHERE '.$where;
			$params = array_merge($params, $r[1]);
		}

		if($this->groupBy)
			$groupBy = ' GROUP BY '.$this->groupBy;
	
		return array('SELECT * FROM '.$sqltable.$rightjoin.$leftjoin.$innerjoin.$where.$orderBy.$limit, $params);
	}
	
	public function first() {
		list($sql, $params) = $this->limit(1)->buildSQL();
		return $this->db->query($sql, $params)->first();
	}
	
	public function get() {
		list($sql, $params) = $this->buildSQL();
		return $this->db->query($sql, $params, $params)->all();
	}
	
	public function paginate($page, $per_page=10) {
		$page = $page ? $page:1;
		$this->offset(($page-1)*$per_page);
		$this->limit($per_page);
		
		return $this;
	}
	
	public function update($values) {
		$where = '';
		$params = array();

		$r = static::processConditions($this->where);
		if($where = $r[0]) {
 			$where = ' WHERE '.$where;
			$params = array_merge($params, $r[1]);
		}
		
		if(sizeof($values) == 0)
			throw new Exception('Update values should not be empty.');
		
		$table = '`'.get(array_keys($this->tables), 0).'`';
			
		$vals = array();
		foreach($values as $k=>$v)
			$set[] = '`'.$k.'`=?';
		$set = ' SET '.implode(', ', $set);
	
		$sql = 'UPDATE '.$table.$set.$where;

		$params = array_merge(array_values($values), $params);
		
		return $this->db->query($sql, $params)->affected();
	}
	
	public function insert($values) {
		if(sizeof($values) == 0)
			throw new Exception('Insert values should not be empty.');
		
		$table = '`'.get(array_keys($this->tables), 0).'`';
			
		$columns = array();
		$vals = array();
		foreach($values as $k=>$v)
			$columns[] = '`'.$k.'`';
		
		$str = ' ('.implode(', ', $columns).') VALUES ('.implode(', ', array_fill(0, sizeof($values), '?')).')';
	
		$sql = 'INSERT INTO '.$table.$str;
		
		return $this->db->query($sql, array_values($values))->id();
	}
	
	public function delete() {
		$where = '';
		$params = array();
		
		$table = get(array_keys($this->tables), 0);

		$r = static::processConditions($this->where);
		if($where = $r[0]) {
 			$where = ' WHERE '.$where;
			$params = array_merge($params, $r[1]);
		}
	
		$sql = 'DELETE FROM '.$table.$where;
		
		return $this->db->query($sql, $params)->affected();
	}
	
	public function count($group_by=null) {
		$where = '';
		$params = array();

		$r = static::processConditions($this->where);
		if($where = $r[0]) {
 			$where = ' WHERE '.$where;
			$params = array_merge($params, $r[1]);
		}
	
		$tables = array();
		foreach($this->tables as $table=>$alias)
			if($alias)
				$tables[] = '`'.$table.'` '.$alias;
			else
				$tables[] = '`'.$table.'`';
		$sqltable = implode(', ', $tables);
		
		if($group_by) {
			$sql = 'SELECT `'.$group_by.'` as groupby, count(*) as total FROM '.$sqltable.$where.' GROUP BY '.$group_by;
			$res = array();
			foreach($this->db->query($sql, $params)->all() as $v)
				$res[$v['groupby']] = $v['total'];
			return $res;
		}
		else {
			$sql = 'SELECT count(*) as total FROM '.$sqltable.$where;
			$res = $this->db->query($sql, $params)->first();
			return $res['total'];
		}
	}
	
	public function min($what, $group_by=null) {
		$where = '';
		$params = array();

		$r = static::processConditions($this->where);

		if($where = $r[0]) {
			$where = ' WHERE '.$where;
			$params = array_merge($params, $r[1]);
		}
		
		$tables = array();
		foreach($this->tables as $table=>$alias)
			if($alias)
				$tables[] = $table.' '.$alias;
			else
				$tables[] = $table;
		$sqltable = implode(', ', $tables);
	
		if($group_by) {
			$sql = 'SELECT `'.$group_by.'` as groupby, min(`'.$what.'`) as min FROM `'.$this->table.'`'.$where.' GROUP BY '.$group_by;
			$res = array();
			foreach($this->db->query($sql, $params)->all() as $v)
				$res[$v['groupby']] = $v['min'];
			return $res;
		}
		else {
			$sql = 'SELECT min(`'.$what.'`) as min FROM '.$sqltable.$where;
			$res = $this->db->query($sql, $params)->first();
			return $res['min'];
		}
	}
	
	public function max($what, $group_by=null) {
		$where = '';
		$params = array();

		$r = static::processConditions($this->where);

		if($where = $r[0]) {
			$where = ' WHERE '.$where;
			$params = array_merge($params, $r[1]);
		}
		
		$tables = array();
		foreach($this->tables as $table=>$alias)
			if($alias)
				$tables[] = $table.' '.$alias;
			else
				$tables[] = $table;
		$sqltable = implode(', ', $tables);
	
		if($group_by) {
			$sql = 'SELECT `'.$group_by.'` as groupby, max(`'.$what.'`) as max FROM `'.$this->table.'`'.$where.' GROUP BY '.$group_by;
			$res = array();
			foreach($this->db->query($sql, $params)->all() as $v)
				$res[$v['groupby']] = $v['max'];
			return $res;
		}
		else {
			$sql = 'SELECT max(`'.$what.'`) as max FROM '.$sqltable.$where;
			$res = $this->db->query($sql, $params)->first();
			return $res['max'];
		}
	}
	
	public function avg($what, $group_by=null) {
		$where = '';
		$params = array();

		$r = static::processConditions($this->where);

		if($where = $r[0]) {
			$where = ' WHERE '.$where;
			$params = array_merge($params, $r[1]);
		}
		
		$tables = array();
		foreach($this->tables as $table=>$alias)
			if($alias)
				$tables[] = $table.' '.$alias;
			else
				$tables[] = $table;
		$sqltable = implode(', ', $tables);
	
		if($group_by) {
			$sql = 'SELECT `'.$group_by.'` as groupby, avg(`'.$what.'`) as avg FROM `'.$this->table.'`'.$where.' GROUP BY '.$group_by;
			$res = array();
			foreach($this->db->query($sql, $params)->all() as $v)
				$res[$v['groupby']] = $v['avg'];
			return $res;
		}
		else {
			$sql = 'SELECT avg(`'.$what.'`) as avg FROM '.$sqltable.$where;
			$res = $this->db->query($sql, $params)->first();
			return $res['avg'];
		}
	}
	
	public function sum($what, $group_by=null) {
		$where = '';
		$params = array();

		$r = static::processConditions($this->where);

		if($where = $r[0]) {
			$where = ' WHERE '.$where;
			$params = array_merge($params, $r[1]);
		}
		
		$tables = array();
		foreach($this->tables as $table=>$alias)
			if($alias)
				$tables[] = $table.' '.$alias;
			else
				$tables[] = $table;
		$sqltable = implode(', ', $tables);
	
		if($group_by) {
			$sql = 'SELECT `'.$group_by.'` as groupby, sum(`'.$what.'`) as sum FROM `'.$this->table.'`'.$where.' GROUP BY '.$group_by;
			$res = array();
			foreach($this->db->query($sql, $params)->all() as $v)
				$res[$v['groupby']] = $v['sum'];
			return $res;
		}
		else {
			$sql = 'SELECT sum(`'.$what.'`) as sum FROM '.$sqltable.$where;
			$res = $this->db->query($sql, $params)->first();
			return $res['sum'];
		}
	}
}