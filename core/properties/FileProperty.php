<?php
class FileProperty extends BaseProperty {
	public function getRules() {
		// d();
		$rules = parent::getRules();
		if(isset($rules['required'])) {
			$rules['filerequired'] = $rules['required'];
			unset($rules['required']);
		}
		unset($rules['dir']);
		unset($rules['multiple']);
		// $rules['date'] = true;

		return $rules;
	}

	public function getDefault() {
		d();
		return new Date;
	}

	public function serialize($obj) {
		// d($obj->name);
		return $obj->name;
	}

	public function unserialize($str) {
		// return new ModelFile($this->model, $this->name, $str);
		d();
		// return Date::fromDatetime($str);
	}

	public function set($val) {
		if(is_array($val))
			$file = new ModelFile($this->model, $this->name, $val['name'], $val['tmp_name']);
		else
			$file = new ModelFile($this->model, $this->name, $val);
		// d($file);
		return $file;
		// d($val);
		// return Date::fromDatetime($val);
	}

	public function get() {
		d();
		return Date::fromDatetime($val);
	}
}