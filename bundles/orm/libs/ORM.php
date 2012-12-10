<?php
namespace Coxis\Bundles\ORM\Libs;

class ORM {
	protected $model = null;
	protected $with = null;
	protected $dal = null;
		
	function __construct($model) {
		$this->model = $model;
		
		$this->dal = new \Coxis\Core\DB\DAL(array($model::getTable() => 'a'));

		if($model::isI18N()){
			$select = 'a . *';
			foreach($model::getDefinition()->properties() as $name=>$property)
				if($property->i18n)
					$select .= ', t.`'.$name.'`';
			$this->select($select);
			$this->leftjoin(array(
				'a.Translation t'	=>	array(
					'a.id = t.id',
					't.locale'	=>	\Config::get('locale'),
				),
			));
		}
		$this->orderBy($model::getDefinition()->meta['order_by']);
	}

	public function toModel($raw) {
		$current_model = $this->model;
		$new = new $current_model;
		return ORMHandler::unserializeSet($new, $raw);
	}

	public function next() {
		if(!($r = $this->dal()->next()))
			return false;
		else
			return $this->toModel($r);
	}

	public function ids() {
		return $this->values('id');
	}

	public function values($attr) {
		$res = array();
		foreach($this->get() as $one)
			$res[] = $one->$attr;
		return $res;
	}
	
	public function dal() {
		return $this->dal; 
	}
	
	public function first() {
		$this->limit(1);
		
		$res = $this->get();
		if(!sizeof($res))
			return false;
		return $res[0];
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
			$models[] = $this->toModel($row);
			$ids[] = $row['id'];
		}
		
		if(sizeof($this->with)) {
			foreach($this->with as $relation_name=>$closure) {
				$rel = ORMHandler::relationData($current_model, $relation_name);
				$relation_type = $rel['type'];
				$relation_model = $rel['model'];
				
				switch($relation_type) {
					case 'hasOne':
					case 'belongsTo':
						$link = $rel['link'];
						
						$res = $relation_model::where(array('a.id IN ('.implode(', ', $ids).')'))->get();
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
						
						$orm = $relation_model::where(array('a.'.$link.' IN ('.implode(', ', $ids).')'));
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

						$orm = $relation_model::innerjoin(array(
							'arpa_actualite_commentaire b' => array(
								'a.id = b.actualite_id',
							),
						))->where(array(
							'b.'.$currentmodel_idfield.' IN ('.implode(', ', $ids).')',#
							'b.'.$relationmodel_idfield.' = a.id',
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
	
	public function queryGet($sql, $args=array()) {
		$models = array();
		$model = $this->model;
		
		$rows = $this->dal->query($sql, $args)->all();
		foreach($rows as $row)
			$models[] = ORMHandler::unserializeSet(new $model, $row);
			
		return $models;
	}
	
	public function paginate($page, $per_page=10, &$paginator=null) {
		$page = $page ? $page:1;
		$this->dal->paginate($page, $per_page);
		$paginator = new \Coxis\Core\Tools\Paginator($per_page, $this->count(), $page);
		
		return $this->get();
	}
	
	public function with($with, $closure=null) {
		$this->with[$with] = $closure;
		
		return $this;
	}
	
	public function setTable($table, $alias=null) {
		$this->dal->setTable($table, $alias);
		
		return $this;
	}
	
	public function setTables($tables) {
		$this->dal->setTables($tables);
		
		return $this;
	}
	
	public function select($select) {
		$this->dal->select($select);
		
		return $this;
	}
	
	public function leftjoin($jointures) {
		$this->dal->leftjoin($jointures);
		
		return $this;
	}
	
	public function rightjoin($jointures) {
		$this->dal->rightjoin($jointures);
		
		return $this;
	}
	
	public function innerjoin($jointures) {
		$this->dal->innerjoin($jointures);
		
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
	
	public function groupBy($groupBy) {
		$this->dal->groupBy($groupBy);
		
		return $this;
	}
	
	public function insert($values) {
		return $this->dal->insert($values);
	}
	
	public function delete() {
		$count = 0;
		while($model = $this->next())
			$count += $model->destroy();

		return $count;
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
	
	public function reset() {
		$this->dal->reset();
		
		return $this;
	}
}