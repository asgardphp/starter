<?php
namespace Coxis\Core;

class ORM {
	private $model = null;
	private $with = null;
	private $dal = null;
		
	function __construct($model, $table=null) {
		$this->model = $model;
		if(!$table)
			$this->dal = new DAL($model::getTable());
		else
			$this->dal = new DAL($table);
	}
	
	public function setTable($table) {
		$this->dal->table = $table;
		
		return $this;
	}
	
	public function where($conditions) {
		$this->dal->where($conditions);
		
		return $this;
	}
	
	public function offset($offset) {
		$this->dal->offset($offset);
		
		return $this;
	}
	
	public function limit($limit) {
		$this->dal->limit($limit);
		
		return $this;
	}
	
	public function orderBy($orderBy) {
		$this->dal->orderBy($orderBy);
		
		return $this;
	}
	
	public function insert($values) {
		return $this->dal->insert($values);
	}
	
	public function delete() {
		return $this->dal->delete();
	}
	
	public function update($values) {
		return $this->dal->update($values);
	}
	
	public function count($group_by=null) {
		return $this->dal->count($group_by);
	}
	
	public function min($what, $group_by=null) {
		return $this->dal->min($what, $group_by);
	}
	
	public function max($what, $group_by=null) {
		return $this->dal->max($what, $group_by);
	}
	
	public function avg($what, $group_by=null) {
		return $this->dal->avg($what, $group_by);
	}
	
	public function sum($what, $group_by=null) {
		return $this->dal->sum($what, $group_by);
	}
	
	public function dal() {
		return $this->dal; 
	}
	
	public function first() {
		$model = $this->model;
		$this->limit(1);
		
		$res = $this->get();
		if(!sizeof($res))
			return false;
		return $res[0];
		
		//~ $res = $this->dal()->first();
		//~ if($res)
			//~ return new $model($res);
		//~ return false;
	}
	
	public function reset() {
		$this->dal->reset();
		
		return $this;
	}
	
	public function all() {
		$this->reset();
		return static::get();
	}
	
	public function get() {
		$models = array();
		$ids = array();
		$current_model = $this->model;
		
		$rows = $this->dal->get();
		foreach($rows as $row) {
			$models[] = new $current_model($row);
			$ids[] = $row['id'];
		}
		
		if(sizeof($this->with)) {
			foreach($this->with as $relation_name=>$closure) {
				$rel = Model::relationData($current_model, $relation_name);
				$relation_type = $rel['type'];
				$relation_model = $rel['model'];
				
				switch($relation_type) {
					case 'hasOne':
					case 'belongsTo':
						$link = $rel['link'];
						
						$res = $relation_model::where(array('id IN ('.implode(', ', $ids).')'))->get();
						foreach($models as $model) {
							$id = $model->$link;
							$filter = array_filter($res, function($result) use ($id) {
								return ($id == $result->id);
							});
							if(isset($filter[0]))
								$model->$relation_name = $filter[0];
							else
								$model->$relation_name = null;
						}
						break;
					case 'hasMany':
						$link = $rel['link'];
						
						$orm = $relation_model::where(array($link.' IN ('.implode(', ', $ids).')'));
						if(is_callable($closure))
							$closure($orm);
						$res = $orm->get();
						foreach($models as $model) {
							$id = $model->id;
							$model->$relation_name = array_filter($res, function($result) use ($id, $link) {
								return ($id == $result->$link);
							});
						}
						break;
					case 'HMABT':
						$join_table = $rel['join_table'];
						$currentmodel_idfield = $rel['link_a'];
						$relationmodel_idfield = $rel['link_b'];
						
						$orm = $relation_model::setTable($join_table.' as a, '.$relation_model::getTable().' as b')->where(array(
							'a.'.$currentmodel_idfield.' IN ('.implode(', ', $ids).')',#
							'a.'.$relationmodel_idfield.' = b.id',
						));
						if(is_callable($closure))
							$closure($orm);
						$res = $orm->get();
						foreach($models as $model) {
							$id = $model->id;
							$model->$relation_name = array_filter($res, function($result) use ($id, $currentmodel_idfield) {
								return ($id == $result->$currentmodel_idfield);
							});
						}
						break;
					default:
						throw new \Exception('Relation type '.$relation_type.' does not exist');
				}
			}
		}
		
		return $models;
	}
	
	public function queryGet($sql, $args) {
		$models = array();
		$model = $this->model;
		
		$rows = $this->dal->query($sql, $args)->all();
		foreach($rows as $row)
			$models[] = new $model($row);
			
		return $models;
	}
	
	public function paginate($page, $per_page=10) {
		$models = array();
		$model = $this->model;
		
		#todo
		#limit, offset and then get().. to have with support.. ?
		
		$rows = $this->dal->paginate($page, $per_page);
		foreach($rows as $row)
			$models[] = new $model($row);
		
		return array($models, new Paginator($per_page, $this->count(), $page));
	}
	
	public function with($with, $closure=null) {
		$this->with[$with] = $closure;
		
		return $this;
	}
}