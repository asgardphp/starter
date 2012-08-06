<?php
class BuildCol {
	private $name;
	private $type;
	private $length;
	private $nullable = false;
	private $autoincrement = false;
	private $def;
	
	function __construct($name, $type, $length=null) {
		$this->name = $name;
		$this->type = $type;
		$this->length = $length;
	}
	
	public function autoincrement() {
		$this->autoincrement = true;
		return $this;
	}
	
	public function nullable() {
		$this->nullable = true;
		return $this;
	}
	
	public function def($def) {
		$this->$def = $def;
		return $this;
	}
	
	public function sql() {
		$sql = '`'.$this->name.'` ';
		
		if($this->length)
			$sql .= $this->type.'('.$this->length.')';
		else
			$sql .= $this->type;
			
		if(!$this->nullable)
			$sql .= ' NOT NULL';
			
		if($this->def)
			$sql .= " DEFAULT '".$this->def."'";
			
		if($this->autoincrement)
			$sql .= ' AUTO_INCREMENT';
			
		return $sql;
	}
}

class BuildTable {
	private $name;
	private $cols = array();
	private $primary;
	private $indexes;
	private $uniques;
	
	function __construct($name) {
		$this->name = $name;
	}
	
	public function unique($keys) {
		$this->uniques = $keys;
		return $this;
	}
	
	public function index($keys) {
		$this->indexes = $keys;
		return $this;
	}
	
	public function primary($keys) {
		$this->primary = $keys;
		return $this;
	}
	
	public function add($colName, $colType, $colLength=null) {
		$col = new BuildCol($colName, $colType, $colLength);
		$this->cols[] = $col;
		return $col;
	}
	
	public function sql() {
		$sql = 'CREATE TABLE `'.$this->name.'` (';
		
		$i = 0;
		foreach($this->cols as $col) {
			if($i++ > 0)
				$sql .= ",\n";
			$sql .= $col->sql();
		}
			
		if($this->primary) {
			$sql .= ",\n".'PRIMARY KEY (';
			if(is_array($this->primary))
				foreach($this->primary as $v)
					$sql .= '`'.$v.'`';
			else
				$sql .= '`'.$this->primary.'`';
			$sql .= ')';
		}
		
		if($this->indexes) {
			$sql .= ",\n".'INDEX KEY (';
			if(is_array($this->indexes))
				foreach($this->indexes as $v)
					$sql .= '`'.$v.'`';
			else
				$sql .= '`'.$this->indexes.'`';
			$sql .= ')';
		}
		
		if($this->uniques) {
			$sql .= ",\n".'UNIQUE KEY (';
			if(is_array($this->uniques))
				foreach($this->uniques as $v)
					$sql .= '`'.$v.'`';
			else
				$sql .= '`'.$this->uniques.'`';
			$sql .= ')';
		}
		
		$sql .= "\n".')';
		
		//~ d($sql);
		return $sql;
	}
}

class Table {
	private $name;
	
	function __construct($name) {
		$this->name = $name;
	}
	
	public function add($name, $type, $length=null) {
		$col = new Column($this->name, $name, $type, $length);
		$col->create();
		return $col;
	}
	
	public function col($name) {
		$col = new Column($this->name, $name);
		return $col;
	}
	
	public function primary($keys) {
		try {
			DB::query('ALTER TABLE  `'.$this->name.'` DROP PRIMARY KEY');
		} catch(\Coxis\Core\DBException $e) {}
	
		if(!is_array($keys))
			$keys = array($keys);
		$sql = 'ALTER TABLE  `'.$this->name.'` ADD PRIMARY KEY (';
		foreach($keys as $k=>$v)
			$keys[$k] = '`'.$v.'`';
		$sql .= implode(', ', $keys);
		$sql .= ')';
		DB::query($sql);
		
		return $this;
	}
}

class Column {
	private $table;
	private $name;
	private $type;
	private $length;
	
	function __construct($table, $name, $type=null, $length=null) {
		$this->table = $table;
		$this->name = $name;
		$this->type = $type;
		$this->length = $length;
	}
	
	public function create() {
		if($this->length)
			$sql = 'ALTER TABLE `'.$this->table.'` ADD `'.$this->name.'` '.$this->type.'('.$type->length.')';
		else
			$sql = 'ALTER TABLE `'.$this->table.'` ADD `'.$this->name.'` '.$this->type;
		DB::query($sql);
		
		return $this;
	}
	
	#name
	#type
	#nullable
	#default
	#autoincrement
	
	private function change($params) {
		$table = $this->table;
		$oldcol = $this->name;
		$newcol = isset($params['name']) ? $params['name']:$this->name;
		$type = isset($params['type']) ? $params['type']:$this->getType();
		$nullable = isset($params['nullable']) ? $params['nullable']:$this->getNullable();
		if($nullable)
			$nullable = 'NULL';
		else
			$nullable = 'NOT NULL';
		$default = isset($params['default']) ? $params['default']:$this->getDefault();
		if($default)
			$default = "DEFAULT '$default'";
		else
			$default = '';
		$autoincrement = isset($params['autoincrement']) ? $params['autoincrement']:$this->getAutoincrement();
		if($autoincrement)
			$autoincrement = 'auto_increment';
		else
			$autoincrement = '';
		
		
		
		$sql = 'ALTER TABLE `'.$table.'` CHANGE `'.$oldcol.'` `'.$newcol.'` '.$type.' '.$default.' '.$nullable.' '.$autoincrement;
		//~ d($sql);
		DB::query($sql);
		//~ ALTER TABLE `test` CHANGE `title2` `title3` varchar(100) DEFAULT 'bob' NOT NULL auto_increment
		//~ ALTER TABLE `test` CHANGE `title2` `title3` varchar(100) NOT NULL auto_increment DEFAULT 'bob'
	}
	
	public function type($type, $length=null) {
		$this->type = $type;
		$this->length = $length;
		if($length)
			$type = $this->type.'('.$this->length.')';
		else
			$type = $this->type;
			
		$this->change(array('type'=>$type));
	
		//~ Schema::renameColumn($this->table, $this->name, $this->name, $type);
		
		return $this;
	}
	
	public function rename($name) {
			
		$this->change(array('name'=>$name));
		
		//~ Schema::renameColumn($this->table, $this->name, $name);
		$this->name = $name;
		
		return $this;
	}
	
	public function nullable() {
		//~ $type = $this->getType();
		//~ $sql = 'ALTER TABLE `'.$this->table.'` CHANGE `'.$this->name.'` '.$type;
		//~ DB::query($sql);
		
		$this->change(array('nullable'=>true));
		
		return $this;
	}
	
	public function notNullable() {
		$sql = 'UPDATE `'.$this->table.'` set `'.$this->name.'` = 0 where `'.$this->name.'` is null';
		DB::query($sql);
		//~ $type = $this->getType();
		//~ $sql = 'ALTER TABLE `'.$this->table.'` CHANGE `'.$this->name.'` `'.$this->name.'` '.$type.' NOT NULL';
		//~ DB::query($sql);
		
		$this->change(array('nullable'=>false));
		
		return $this;
	}
	
	public function def($val) {
		//~ $type = $this->getType();
		//~ $sql = 'ALTER table `'.$this->table.'` CHANGE `'.$this->name.'` `'.$this->name.'` '.$type.' DEFAULT \''.$val.'\'';
		//~ DB::query($sql);
		//~ d($sql);
		
		$this->change(array('default'=>$val));
		
		return $this;
	}
	
	private function getType() {
		$r = DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = '".Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$this->table'
		 AND COLUMN_NAME = '$this->name'")->first();
		 
		return $r['COLUMN_TYPE'];
	}
	
	private function getNullable() {
		$r = DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = '".Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$this->table'
		 AND COLUMN_NAME = '$this->name'")->first();
		 
		return $r['IS_NULLABLE'] === 'YES';
	}
	
	private function getDefault() {
		$r = DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = '".Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$this->table'
		 AND COLUMN_NAME = '$this->name'")->first();
		 
		return $r['COLUMN_DEFAULT'];
	}
	
	private function getAutoincrement() {
		$r = DB::query("SELECT * 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = '".Config::get('database', 'database')."' 
                 AND  TABLE_NAME = '$this->table'
		 AND COLUMN_NAME = '$this->name'")->first();
		 
		return strpos($r['EXTRA'], 'auto_increment') !== false;
	}
	
	public function dropIndex() {
		$sql = 'alter table `'.$this->table.'` drop index `'.$this->name.'`';
		try {
		DB::query($sql);
		} catch(\Coxis\Core\DBException $e) {}
		
		return $this;
	}
	
	public function index() {
		#todo length ADD INDEX(title(50))
		$sql = 'ALTER TABLE `'.$this->table.'` ADD INDEX(`'.$this->name.'`)';
		DB::query($sql);
		
		return $this;
	}
	
	public function unique() {
		#todo length ADD INDEX(title(50))
		$sql = 'ALTER TABLE `'.$this->table.'` ADD UNIQUE(`'.$this->name.'`)';
		DB::query($sql);
		
		return $this;
	}
}

class Schema {
	public static function create($tableName, $cb) {
		$table = new BuildTable($tableName);
		$cb($table);
		$sql = $table->sql();
		DB::query($sql);
	}
	
	public static function dropColumn($table, $col) {
		$sql = 'alter table `'.$table.'` drop column `'.$col.'`';
		DB::query($sql);
	}
	
	public static function drop($table) {
		$sql = 'DROP TABLE `'.$table.'`';
		DB::query($sql);
	}
	
	public static function rename($from, $to) {
		$sql = 'RENAME TABLE `'.$from.'` TO `'.$to.'`';
		DB::query($sql);
	}
	
	public static function table($tableName, $cb) {
		$table = new Table($tableName);
		$cb($table);
	}
	
	public static function renameColumn($table, $old, $new, $type=null) {
		$table = new Table($table);
		$col = $table->col($old);
		$col->rename($new);
		if($type)
			$col->type($type);
	}
	
	public static function getType($table, $column) {
		$table = new BuildTable($tableName);
		$col = $table->col($old);
		
		return $col->getType();
	}
}