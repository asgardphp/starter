<?php
namespace Coxis\Bundles\Admin\Libs;

class CoxisAdmin {
	public static function getModelFor($controller) {
		return $controller::getModel();
	}

	public static function getIndexURLFor($controller) {
		try {
			return $controller::getIndexURL();
		} catch(PHPErrorException $e) {
			throw new \Exception('Admin Controller does not exist for model '.$controller);
		}
	}

	public static function getEditURLFor($controller, $id) {
		try {
			return $controller::getEditURL($id);
		} catch(PHPErrorException $e) {
			throw new \Exception('Admin Controller does not exist for model '.$controller);
		}
	}
}