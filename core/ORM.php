<?php
namespace Coxis\Core;

class ORM extends DAL {
	private $model = null;
	private $with = null;
		
	function __construct($model) {
		$this->model = $model;
		
		parent::__construct($model::getTable());
	}
	
	public function dal() {
		$dal = new DAL($this->table);
		$dal->where = $this->where;
		$dal->offset = $this->offset;
		$dal->limit = $this->limit;
		$dal->orderBy = $this->orderBy;
		return $dal; 
	}
	
	public function first() {
		$model = $this->model;
		$res = parent::first();
		if($res)
			return new $model(parent::first());
		return false;
	}
	
	public function all() {
		$this->where = null;
		$this->offset = null;
		$this->limit = null;
		return static::get();
	}
	
	public function get() {
		$models = array();
		$model = $this->model;
		
		$rows = parent::get();
		foreach($rows as $row)
			$models[] = new $model($row);
		
		return $models;
	}
	
	public function queryGet($sql, $args) {
		$models = array();
		$model = $this->model;
		
		$rows = parent::query($sql, $args)->all();
		foreach($rows as $row)
			$models[] = new $model($row);
			
		return $models;
	}
	
	public function paginate($page, $per_page=10) {
		$models = array();
		$model = $this->model;
		
		$rows = parent::paginate($page, $per_page);
		foreach($rows as $row)
			$models[] = new $model($row);
		
		return array($models, new Paginator($per_page, $this->count(), $page));
	}
	
	public function with($with, $closure) {
		
	}
}