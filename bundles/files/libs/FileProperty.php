<?php
class FileProperty extends BaseProperty {
	public function getRules() {
		$rules = parent::getRules();
		if(isset($rules['required'])) {
			$rules['filerequired'] = $rules['required'];
			unset($rules['required']);
		}
		unset($rules['dir']);
		unset($rules['multiple']);

		return $rules;
	}

	public function getSQLType() {
		return 'varchar(255)';
	}

	public function getDefault() {
		return new ModelFile($this->model, $this->name, null);
	}

	public function serialize($obj) {
		if($this->multiple)
			return serialize($obj->name);
		else
			return $obj->name;
	}

	public function unserialize($str) {
		if($this->multiple)
			return new ModelFile($this->model, $this->name, unserialize($str));
		else
			return new ModelFile($this->model, $this->name, $str);
	}

	public function set($val) {
		if($val instanceof \ModelFile)
			return $val;
		if(is_array($val))
			$file = new ModelFile($this->model, $this->name, null, $val);
		#todo should use unserialize instead..
		elseif($this->multiple)
			$file = new ModelFile($this->model, $this->name, unserialize($val));
		else
			$file = new ModelFile($this->model, $this->name, $val);
		return $file;
	}

	public function get() {
		d();
		return Date::fromDatetime($val);
	}
}