<?php
namespace Coxis\Bundles\Files\Libs;

class FileProperty extends \Coxis\Core\Properties\BaseProperty {
	public static $defaultallowed = array('pdf', 'doc', 'jpg', 'jpeg', 'png', 'docx', 'gif', 'rtf', 'ppt', 'xls', 'zip', 'txt');

	public function getRules() {
		$rules = parent::getRules();
		if(isset($rules['required'])) {
			$rules['filerequired'] = $rules['required'];
			unset($rules['required']);
		}
		unset($rules['dir']);
		unset($rules['multiple']);
		if(!isset($rules['allowed']))
			$rules['allowed'] = static::$defaultallowed;

		return $rules;
	}

	public function getSQLType() {
		return 'varchar(255)';
	}

	public function getDefault() {
		return new \Coxis\Bundles\Files\Libs\ModelFile($this->model, $this->name, null);
	}

	public function serialize($obj) {
		if($this->multiple)
			return serialize($obj->name);
		else
			return $obj->name;
	}

	public function unserialize($str) {
		if($this->multiple)
			return new \Coxis\Bundles\Files\Libs\ModelFile($this->model, $this->name, unserialize($str));
		else
			return new \Coxis\Bundles\Files\Libs\ModelFile($this->model, $this->name, $str);
	}

	public function set($val) {
		if($val instanceof \Coxis\Bundles\Files\Libs\ModelFile)
			return $val;
		if(is_array($val))
			return new \Coxis\Bundles\Files\Libs\ModelFile($this->model, $this->name, null, $val);
		#todo should use unserialize instead..
		elseif($this->multiple)
			return new \Coxis\Bundles\Files\Libs\ModelFile($this->model, $this->name, unserialize($val));
		else
			return new \Coxis\Bundles\Files\Libs\ModelFile($this->model, $this->name, $val);
	}

	public function get() {
		d();
		return Date::fromDatetime($val);
	}
}