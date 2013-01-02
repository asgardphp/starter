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

	public function getDefault($model=null) {
		return new \Coxis\Bundles\Files\Libs\ModelFile($model, $this->name, null);
	}

	public function serialize($obj) {
		if($this->multiple)
			return serialize($obj->name);
		else
			return $obj->file;
	}

	public function unserialize($str, $model=null) {
		if($this->multiple)
			try {
				return new \Coxis\Bundles\Files\Libs\ModelMultipleFile($model, $this->name, unserialize($str));
			} catch(\Exception $e) {
				return $this->getDefault($model);
			}
		return new \Coxis\Bundles\Files\Libs\ModelFile($model, $this->name, $str);
	}

	public function set($val, $model=null) {
		if($val instanceof \Coxis\Bundles\Files\Libs\ModelFile || $val instanceof \Coxis\Bundles\Files\Libs\ModelMultipleFile)
			return $val;

		if($this->multiple)
			return new \Coxis\Bundles\Files\Libs\ModelMultipleFile($model, $this->name, $val);
		else
			return new \Coxis\Bundles\Files\Libs\ModelFile($model, $this->name, $val);

		// if(is_array($val))
		// 	return new \Coxis\Bundles\Files\Libs\ModelFile($model, $this->name, null, $val);
		// #todo should use unserialize instead..
		// elseif($this->multiple)
		// 	return new \Coxis\Bundles\Files\Libs\ModelMultipleFile($model, $this->name, $val);
		// 	// return new \Coxis\Bundles\Files\Libs\ModelFile($model, $this->name, unserialize($val));
		// else
		// 	return new \Coxis\Bundles\Files\Libs\ModelFile($model, $this->name, null, array('name'=>basename($val), 'tmp_name'=>$val));
	}
}