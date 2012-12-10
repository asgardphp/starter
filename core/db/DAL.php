<?php
namespace Coxis\Core\DB;

#todo select only some columns
class DAL {
	public $db = null;
	public $tables = null;
	
	public $select = null;
	public $where = null;
	public $offset = null;
	public $limit = null;
	public $orderBy = null;
	public $groupBy = null;
	public $leftjoin = array();
	public $rightjoin = array();
	public $innerjoin = array();

	protected $rsc = null;
		
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

	public function rsc() {
		$query = $this->buildSQL();
		return $this->query($query[0], $query[1]);
	}

	public function next() {
		if($this->rsc === null)
			$this->rsc = $this->rsc();
		return $this->rsc->next();
	}
	
	public function reset() {
		$this->where = null;
		$this->offset = null;
		$this->limit = null;
		$this->orderBy = null;
		$this->groupBy = null;
		$this->leftjoin = array();
		$this->rightjoin = array();
		$this->innerjoin = array();
		
		return $this;
	}
	
	public function query($sql, $args=array()) {
		return $this->db->query($sql, $args);
	}
	
	/* GETTERS */
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

	/* SETTERS */
	public function select($select) {
		$this->select = $select;
		return $this;
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
		
	public function where($conditions) {
		if($this->where === null)
			$this->where = array();
			
		if(!$conditions)
			return $this;
		
		$this->where[] = static::parseConditions($conditions);
		
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

	/* CONDITIONS PROCESSING */
	protected static function processConditions($params, $condition = 'and', $brackets=false, $table=null) {
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
	
	protected static function replace($condition, $table='') {
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
	
	protected static function parseConditions($conditions) {
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
	
	public function getTable($ref) {
		foreach($this->tables as $table=>$alias)
			if($alias == $ref)
				return $table;
		return null;
	}
	
	/* BUILDERS */
	public function buildSelect() {
		if($this->select)
			return $this->select;
		else
			return '*';
	}
	public function buildTables() {
		$tables = array();
		foreach($this->tables as $table=>$alias)
			if($alias)
				$tables[] = '`'.$table.'` '.$alias;
			else
				$tables[] = '`'.$table.'`';
		return implode(', ', $tables);
	}
	public function getDefaultTable() {
		if(get(array_values($this->tables), 0))
			return get(array_values($this->tables), 0);
		else
			return get(array_keys($this->tables), 0);
	}
	public function buildWhere($default=null) {
		if(!$default)
			$default = $this->getDefaultTable();

		$params = array();
		$r = static::processConditions($this->where, 'and', false, $default);
		if($r[0])
			return array(' WHERE '.$r[0], $r[1]);
		else
			return array('', array());
	}
	public function buildGroupby() {
		if($this->groupBy)
			return ' GROUP BY '.$this->groupBy;
	}
	public function buildOrderby($default) {
		if(!$this->orderBy)
			return '';

		$orderBy = ' ORDER BY ';
		if(!is_array($this->orderBy))
			$orders = array($this->orderBy);
		else
			$orders = $this->orderBy;
		
		foreach($orders as $k=>$v)
			if(preg_match('/^[a-zA-Z0-9_ ]+$/', $v))
				$orders[$k] = $default.'.'.$v;
				
		$orderBy .= implode(', ', $orders);
		return $orderBy;
	}
	public function buildLeftjoin() {
		$params = array();
		$leftjoin = '';
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
		return array($leftjoin, $params);
	}
	public function buildRightjoin() {
		$params = array();
		$rightjoin = '';
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
		return array($rightjoin, $params);
	}
	public function buildInnerjoin() {
		$params = array();
		$innerjoin = '';
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
		return array($innerjoin, $params);
	}
	public function buildLimit() {
		if(!$this->limit && !$this->offset)
			return '';

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
		return $limit;
	}

	public function buildSQL() {
		$params = array();
		
		$tables = $this->buildTables();
		
		$default = $this->getDefaultTable();
		
		$select = $this->buildSelect();
		
		$orderBy = $this->buildorderby($default);
				
		$limit = $this->buildLimit();
				
		#todo put ` around table only, ie `like` l and not `like l`
		list($rightjoin, $rjparams) = $this->buildRightjoin();
		$params = array_merge($params, $rjparams);
		
		list($leftjoin, $ljparams) = $this->buildLeftjoin();
		$params = array_merge($params, $ljparams);
				
		list($innerjoin, $ijparams) = $this->buildInnerjoin();
		$params = array_merge($params, $ijparams);
		
		list($where, $whereparams) = $this->buildWhere($default);
		$params = array_merge($params, $whereparams);

		$groupby = $this->buildGroupby();

		return array('SELECT '.$select.' FROM '.$tables.$rightjoin.$leftjoin.$innerjoin.$where.$groupby.$orderBy.$limit, $params);
	}
	
	/* FUNCTIONS */
	public function update($values) {
		$params = array();
		
		if(sizeof($values) == 0)
			throw new Exception('Update values should not be empty.');
		
		$table = '`'.get(array_keys($this->tables), 0).'`';

		list($where, $whereparams) = $this->buildWhere($table);
		$params = array_merge($params, $whereparams);
			
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
		$params = array();
		
		$table = get(array_keys($this->tables), 0);

		list($where, $whereparams) = $this->buildWhere($table);
		$params = array_merge($params, $whereparams);
	
		$sql = 'DELETE FROM '.$table.$where;
		
		return $this->db->query($sql, $params)->affected();
	}

	protected function _function($fct, $what=null, $group_by=null) {
		$params = array();

		list($where, $whereparams) = $this->buildWhere();
		$params = array_merge($params, $whereparams);
	
		$tables = $this->buildTables();
	
		if($what)
			$what = '`'.$what.'`';
		else
			$what = '*';

		if($group_by) {
			$sql = 'SELECT `'.$group_by.'` as groupby, '.$fct.'('.$what.') as '.$fct.' FROM `'.$this->table.'`'.$where.' GROUP BY '.$group_by;#todo $this->table
			$res = array();
			foreach($this->db->query($sql, $params)->all() as $v)
				$res[$v['groupby']] = $v[$fct];
			return $res;
		}
		else {
			$sql = 'SELECT '.$fct.'('.$what.') as '.$fct.' FROM '.$tables.$where;
			$res = $this->db->query($sql, $params)->first();
			return $res[$fct];
		}
	}
	
	public function count($group_by=null) {
		return $this->_function('count', null, $group_by);
	}
	
	public function min($what, $group_by=null) {
		return $this->_function('min', $what, $group_by);
	}
	
	public function max($what, $group_by=null) {
		return $this->_function('max', $what, $group_by);
	}
	
	public function avg($what, $group_by=null) {
		return $this->_function('avg', $what, $group_by);
	}
	
	public function sum($what, $group_by=null) {
		return $this->_function('sum', $what, $group_by);
	}
}