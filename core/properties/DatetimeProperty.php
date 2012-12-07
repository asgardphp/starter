<?php
namespace Coxis\Core\Properties;

class DatetimeProperty extends BaseProperty {
	public function getRules() {
		$rules = parent::getRules();
		$rules['date'] = true;

		return $rules;
	}

	public function _getDefault() {
		return new \Coxis\Core\Tools\Datetime;
	}

	public function serialize($obj) {
		if($obj == null)
			return '';
		return $obj->datetime();
	}

	public function unserialize($str) {
		preg_match('/([0-9]+)\/([0-9]+)\/([0-9]+) ([0-9]+):([0-9]+):([0-9]+)/', $str, $r);
		d($r);
	}

	public function set($val) {
		if(!$val)
			return null;
		preg_match('/([0-9]+)-([0-9]+)-([0-9]+) ([0-9]+):([0-9]+):([0-9]+)/', $val, $r);
		$t = mktime($r[4], $r[5], $r[6], $r[2], $r[3], $r[1]);
		return new \Coxis\Core\Tools\Datetime($t);
	}

	public function getSQLType() {
		return 'datetime';
	}
}