<?php
namespace App\Intranet\Hooks;

class IntranetHooks extends \Coxis\Hook\HooksContainer {
	/**
	@Hook('start')
	*/
	public function start() {
		Auth::attemptRemember();
	}

	/**
	@Hook('exception_Coxis\Auth\NotAuthenticatedException')
	*/
	public function NotAuthenticatedException($exception) {
		\Response::setCode(401);
		return 'You must be authenticated';
	}
}