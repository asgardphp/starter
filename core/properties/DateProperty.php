<?php
namespace Coxis\Core\Properties;

class DateProperty extends BaseProperty {
	public function getRules() {
		$rules = parent::getRules();
		$rules['date'] = true;

		return $rules;
	}

	public function getDefault() {
		return new \Coxis\Core\Tools\Date;
	}

	public function serialize($obj) {
		return $obj->datetime();
	}

	public function unserialize($str) {
		return \Coxis\Core\Tools\Date::fromDatetime($str);
	}

	public function set($val) {
		if(!$val)
			return null;
		return \Coxis\Core\Tools\Date::fromDatetime($val);
	}

	public function getSQLType() {
		return 'date';
	}
}