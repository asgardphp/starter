<?php
class DateProperty extends BaseProperty {
	public function getRules() {
		$rules = parent::getRules();
		$rules['date'] = true;

		return $rules;
	}

	public function getDefault() {
		return new Date;
	}

	public function serialize($obj) {
		return $obj->datetime();
	}

	public function unserialize($str) {
		return Date::fromDatetime($str);
	}

	public function set($val) {
		return Date::fromDatetime($val);
	}

	public function getSQLType() {
		return 'date';
	}
}