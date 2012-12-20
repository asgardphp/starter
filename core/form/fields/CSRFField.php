<?php
class CSRFField extends \Coxis\Core\Form\Fields\HiddenField {
	function __construct($options=array()) {
		parent::__construct($options);
		$this->options['validation']['csrf_check'] = array($this, 'error');

		$this->default_render = function($field, $options) {
			if(\Coxis\Core\Inputs\Session::has('_csrf_token'))
				$token = \Coxis\Core\Inputs\Session::get('_csrf_token');
			else {
				$token = Tools::randstr();
				\Coxis\Core\Inputs\Session::set('_csrf_token', $token);
			}

			return HTMLWidget::hidden($field->getName(), $token, $options)->render();
		};
	}

	protected function generateToken() {
		if(\Coxis\Core\Inputs\Session::has('_csrf_token'))
			return \Coxis\Core\Inputs\Session::get('_csrf_token');
		else {
			$token = Tools::randstr();
			\Coxis\Core\Inputs\Session::set('_csrf_token', $token);
			return $token;
		}
	}

	public function error($attr, $value) {
		if($this->value != \Coxis\Core\Inputs\Session::get('_csrf_token'))
			return __('CSRF token is invalid.');
	}
}