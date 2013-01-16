<?php
namespace App\Admin\Libs;

class CoxisAdmin {
	public static function getModelFor($controller) {
		return $controller::getModel();
	}

	public static function getIndexURLFor($controller) {
		return $controller::getIndexURL();
	}

	public static function getEditURLFor($controller, $id) {
		return $controller::getEditURL($id);
	}
}