<?php
class BooleanField extends \Coxis\Core\Form\Fields\Field {
	protected $default_render = 'checkbox';

	public function getValue() {
		return !!$this->value;
	}
}