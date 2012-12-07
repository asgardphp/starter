<?php
namespace Coxis\Bundles\ORM\Libs;

class CollectionORM extends ORM implements \Coxis\Core\Collection {
	private $current_model = null;
	private $relation = null;
	private $link = null;
	private $link_a = null;
	private $link_b = null;
	private $join_table = null;
	private $sortfield = null;

	function __construct($model, $relation_name) {
		$this->current_model = $model;
		$relationships = $model->getDefinition()->relationships;
		$this->relation = $relationships[$relation_name];
		
		$rel = ORMHandler::relationData($model->getDefinition(), $relation_name);
		$relation_type = $rel['type'];
		$relation_model = $rel['model'];
		
		parent::__construct($relation_model);
		
		switch($relation_type) {
			case 'hasMany':
				$this->link = $rel['link'];
				break;
			case 'HMABT':
				$this->join_table = $rel['join_table'];
				$this->link_a = $rel['link_a'];
				$this->link_b = $rel['link_b'];
				$this->sortfield = $rel['sortable'];
				break;
			default:
				throw new \Exception('Collection only works with hasMany and HMABT');
		}
		
		$this->reset();
	}

	public function reset() {
		parent::reset();
		
		switch($this->relation['type']) {
			case 'hasMany':
				$this->where(array($this->link => $this->current_model->id));
				break;
			case 'HMABT':
				$relation_model = $this->relation['model'];
				$currentmodel_idfield = $this->link_a;
				$relationmodel_idfield = $this->link_b;
				
				$this->innerjoin(array(
					$this->join_table.' b' => array(
						'b.'.$currentmodel_idfield => $this->current_model->id,
						'b.'.$relationmodel_idfield.' = a.id',
					)
				));
				if($this->sortfield)
					$this->orderBy('b.'.$this->sortfield.' ASC');
				break;
			default:
				throw new \Exception('Collection only works with hasMany and HMABT');
		}
	}
	
	public function sync($ids) {
		if(!$ids)
			$ids = array();
		if(!is_array($ids))
			$ids = array($ids);
		foreach($ids as $k=>$v)
			if($v instanceof \Coxis\Core\Model)
				$ids[$k] = (int)$v->id;
	
		switch($this->relation['type']) {
			case 'hasMany':
				$relation_model = $this->relation['model'];
				$link = $this->link;
				$dal = new DAL($relation_model::getTable());
				$dal->where(array($link => $this->current_model->id))->update(array($link => 0));
				if($ids)
					$dal->reset()->where(array('id IN ('.implode(', ', $ids).')'))->update(array($link => $this->current_model->id));
				break;
			case 'HMABT':
				$dal = new DAL($this->join_table);
				$dal->where(array($this->link_a => $this->current_model->id))->delete();
				$dal->reset();
				$i = 1;
				foreach($ids as $id) {
					if($this->sortfield)
						$dal->insert(array($this->link_a => $this->current_model->id, $this->link_b => $id, $this->sortfield => $i++));
					else
						$dal->insert(array($this->link_a => $this->current_model->id, $this->link_b => $id));
				}
				break;
			default:
				throw new \Exception('Collection only works with hasMany and HMABT');
		}
		
		return $this;
	}
	
	public function add($ids) {
		if(!is_array($ids))
			$ids = array($ids);
		foreach($ids as $k=>$id)
			if($id instanceof \Coxis\Core\Model)
				$ids[$k] = (int)$id->id;
			
		switch($this->relation['type']) {
			case 'hasMany':
				$relation_model = $this->relation['model'];
				$dal = new DAL($relation_model::getTable());
				foreach($ids as $id)
					$dal->reset()->where(array('id' => $id))->update(array($this->link => $this->current_model->id));
				break;
			case 'HMABT':
				$dal = new DAL($this->join_table);
				$i = 1;
				foreach($ids as $id) {
					$dal->reset()->where(array($this->link_a => $this->current_model->id, $this->link_b => $id))->delete();
					if($this->sortfield)
						$dal->insert(array($this->link_a => $this->current_model->id, $this->link_b => $id, $this->sortfield => $i++));
					else
						$dal->insert(array($this->link_a => $this->current_model->id, $this->link_b => $id));
				}
				break;
			default:
				throw new \Exception('Collection only works with hasMany and HMABT');
		}
		
		return $this;
	}

	public function create($params=array()) {
		$relModel = $this->relation['model'];
		$new = new $relModel;
		switch($this->relation['type']) {
			case 'hasMany':
				$params[$this->link] = $this->current_model->id;
				break;
			case 'HMABT':#todo
				break;
		}
		$new->save($params);
		return $new;
	}
	
	public function remove($ids) {
		if(!is_array($ids))
			$ids = array($ids);
		foreach($ids as $k=>$id)
			if($id instanceof \Coxis\Core\Model)
				$ids[$k] = $id->id;
			
		switch($this->relation['type']) {
			case 'hasMany':
				$relation_model = $this->relation['model'];
				$dal = new DAL($relation_model::getTable());
				foreach($ids as $id)
					$dal->reset()->where(array('id' => $id))->update(array($this->link => 0));
				break;
			case 'HMABT':
				$dal = new DAL($this->join_table);
				foreach($ids as $id)
					$dal->reset()->where(array($this->link_a => $this->current_model->id, $this->link_b => $id))->delete();
				break;
			default:
				throw new \Exception('Collection only works with hasMany and HMABT');
		}
		
		return $this;
	}
}