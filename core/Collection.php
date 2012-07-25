<?php
class Collection extends ORM {
	private $current_model = null;
	private $relation = null;
	private $link = null;
	private $link_a = null;
	private $link_b = null;
	private $join_table = null;

	function __construct($model, $relation_name) {
		$this->current_model = $model;
		$relationships = $model::$relationships;
		$this->relation = $relationships[$relation_name];
		
		$rel = Model::relationData($model, $relation_name);
		$relation_type = $rel['type'];
		$relation_model = $rel['model'];
		
		parent::__construct($relation_model::getModelName());
		
		switch($relation_type) {
			case 'hasMany':
				$this->link = $rel['link'];
				break;
			case 'HMABT':
				$this->join_table = $rel['join_table'];
				$this->link_a = $rel['link_a'];
				$this->link_b = $rel['link_b'];
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
				$this->where(array($this->link.' = ?' => $this->current_model->id));
				break;
			case 'HMABT':
				$relation_model = $this->relation['model'];
				$currentmodel_idfield = $this->link_a;
				$relationmodel_idfield = $this->link_b;
				$this->setTable($this->join_table.' as a, '.$relation_model::getTable().' as b')->where(array(
					'a.'.$currentmodel_idfield.' = ?' => $this->current_model->id,
					'a.'.$relationmodel_idfield.' = b.id',
				));
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
				$dal->where(array($link.' = ?' => $this->current_model->id))->update(array($link => 0));
				if($ids)
					$dal->reset()->where(array('ID IN ('.implode(', ', $ids).')'))->update(array($link => $this->current_model->id));
				break;
			case 'HMABT':
				$dal = new DAL($this->join_table);
				$dal->where(array($this->link_a.' = ?' => $this->current_model->id))->delete();
				$dal->reset();
				foreach($ids as $id)
					$dal->insert(array($this->link_a => $this->current_model->id, $this->link_b => $id));
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
					$dal->reset()->where(array('id = ?' => $id))->update(array($this->link => $this->current_model->id));
				break;
			case 'HMABT':
				$dal = new DAL($this->join_table);
				foreach($ids as $id) {
					$dal->reset()->where(array($this->link_a => $this->current_model->id, $this->link_b => $id))->delete();
					$dal->insert(array($this->link_a => $this->current_model->id, $this->link_b => $id));
				}
				break;
			default:
				throw new \Exception('Collection only works with hasMany and HMABT');
		}
		
		return $this;
	}
	
	public function remove($ids) {
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
					$dal->reset()->where(array('id = ?' => $id))->update(array($this->link => 0));
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
	
	//~ public function insert($values) {
		//~ d();
		//~ return $this->dal->insert($values);
	//~ }
	
	//~ public function delete($id) {
		//~ if($id instanceof \Coxis\Core\Model)
			//~ $id = (int)$id->id;
			
		//~ switch($this->relation['type']) {
			//~ case 'hasMany':
				//~ parent::delete();
				//~ break;
			//~ case 'HMABT':
				//~ $dal = new DAL($this->join_table);
				//~ $in = $dal->where(array($this->link_a => $this->current_model->id))->get();
				//~ d($in);
				//~ break;
			//~ default:
				//~ throw new \Exception('Collection only works with hasMany and HMABT');
		//~ }
		
		//~ return $this;
	//~ }
	
	//~ public function update($values) {
		//~ d();
		//~ return $this->dal->update($values);
	//~ }
}