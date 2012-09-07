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

	public function getDefault() {
		return new ModelFile($this->model, $this->name, null);
	}

	public function serialize($obj) {
		return $obj->name;
	}

	public function unserialize($str) {
		return new ModelFile($this->model, $this->name, $str);
	}

	public function set($val) {
		if(is_array($val))
			$file = new ModelFile($this->model, $this->name, $val['name'], $val['tmp_name']);
		else
			$file = new ModelFile($this->model, $this->name, $val);
		return $file;
	}

	public function get() {
		d();
		return Date::fromDatetime($val);
	}
}