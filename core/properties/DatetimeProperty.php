<?php
namespace Coxis\Core\Properties;

class DatetimeProperty extends BaseProperty {
	public function getRules() {
		$rules = parent::getRules();
		$rules['date'] = true;

		return $rules;
	}

	public function _getDefault() {
		return new \Coxis\Core\Tools\Date;
	}

	public function serialize($obj) {
		if($obj == null)
			return '';
		return $obj->datetime();
	}

	public function unserialize($str) {
		d('todo');
		list($y, $m, $d) = explode('-', $str);
		$str = $d.'/'.$m.'/'.$y;
		return \Coxis\Core\Tools\Date::fromDatetime($str);
	}

	public function set($val) {
		d('todo');
		if(!$val)
			return null;
		return \Coxis\Core\Tools\Date::fromDatetime($val);
	}

	public function getSQLType() {
		return 'datetime';
	}
}