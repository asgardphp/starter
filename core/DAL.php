<?php
namespace Coxis\Core;

#todo select only some columns
class DAL {
	public $db = null;
	public $table = null;
	
	public $where = null;
	public $offset = null;
	public $limit = null;
	public $orderBy = null;
		
	function __construct($table) {
		$this->db = DB::getInstance();
		$this->table = $table;
	}
	
	public function setTable($table) {
		$this->table = $table;
		
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
	
	private static function processConditions($conditions, $join = 'and', $brackets=false) {
		if(sizeof($conditions) == 0)
			return '';
		
		$string_conditions = '';
		
		if(!is_array($conditions))
			if($join == 'and')
				return $conditions;
			else
				return static::replace($join, $conditions);
		else
			foreach($conditions as $key=>$value)
				if(is_int($key))
					$string_conditions[] = static::processConditions($value, 'and', false);
				else
					$string_conditions[] = static::processConditions($value, $key, true);
			
		$result = implode(' '.$join.' ', $string_conditions);
		
		if($brackets)
			return '('.$result.')';
		else
			return $result;
	}
	
	private static function replace($condition, $params) {
		if(!is_array($params))
			$params = array($params);
			
		//~ foreach($params as $k=>$v)
			//~ $params[$k] = mysql_real_escape_string($v, $this->db);
			
		if(strpos($condition, '?') === false)
			if(preg_match('/^[a-zA-Z]+$/', $condition))
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
	
	public function first() {
		$where = '';
		$orderBy = '';
		$limit = 1;
		
		if($where = static::processConditions($this->where))
			$where = ' WHERE '.$where;
		
		if($this->orderBy)
			$orderBy = ' ORDER BY '.$this->orderBy;
			
		if($this->offset)
			$limit = ' LIMIT '.$this->offset.', 1';
		else
			$limit = ' LIMIT 1';
	
		$sql = 'SELECT * FROM '.$this->table.$where.$orderBy.$limit;
		
		return $this->db->query($sql)->first();
	}
	
	public function get() {
		$where = '';
		$orderBy = '';
		$limit = '';
		
		if($where = static::processConditions($this->where))
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
	
		$sql = 'SELECT * FROM '.$this->table.$where.$orderBy.$limit;
		
		return $this->db->query($sql)->all();
	}
	
	public function paginate($page, $per_page=10) {
		$offset = ($page-1)*$per_page;
		$limit = $per_page;
		
		#late binding for non-static..
		//~ $dal = new DAL($this->table);
		//~ return $dal->where($this->where)->orderBy($this->orderBy)->offset($offset)->limit($limit)->get();
		return static::where($this->where)->orderBy($this->orderBy)->offset($offset)->limit($limit)->get();
	}
	
	public function update($values) {
		$where = '';
		if($where = static::processConditions($this->where))
			$where = ' WHERE '.$where;
		
		if(sizeof($values) == 0)
			throw new Exception('Update values should not be empty.');
			
		$set = array();
		foreach($values as $k=>$v)
			$set[] = static::replace('`'.$k.'`=?', $v);
		$set = ' SET '.implode(', ', $set);
	
		$sql = 'UPDATE '.$this->table.$set.$where;
		
		return $this->db->query($sql)->affected();
	}
	
	public function insert($values) {
		if(sizeof($values) == 0)
			throw new Exception('Insert values should not be empty.');
		
		$columns = array();
		$vals = array();
		foreach($values as $k=>$v)
			$columns[] = '`'.$k.'`';
		foreach($values as $k=>$v)
			$vals[] = "'".$v."'";
		
		$str = ' ('.implode(', ', $columns).') VALUES ('.implode(', ', $vals).')';
	
		$sql = 'INSERT INTO '.$this->table.$str;
		
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
	
		if($group_by) {
			$sql = 'SELECT `'.$group_by.'` as groupby, count(*) as total FROM '.$this->table.$where.' GROUP BY '.$group_by;
			$res = array();
			foreach($this->db->query($sql)->all() as $v)
				$res[$v['groupby']] = $v['total'];
			return $res;
		}
		else {
			$sql = 'SELECT count(*) as total FROM '.$this->table.$where;
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