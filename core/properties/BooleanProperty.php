<?php
class BooleanProperty extends BaseProperty {
	public function getRules() {
		$rules = parent::getRules();
		$rules['required'] = false;
		return $rules;
	}

	public function getDefault() {
		return false;
	}
}