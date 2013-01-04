<?php
namespace Coxis\Core;

class FrontController extends Controller {
	public function mainAction() {
		\Coxis::load();
		$response = static::getResponse();
		$response->send();
	}

	public static function getResponse() {
		try {
			try {
				\Hook::trigger('start');

				$output = \Router::dispatch();
				if($output instanceof \Coxis\Core\Response)
					$response = $output;
				else 
					$response = \Response::setContent($output);
			} catch(\Coxis\Core\ControllerException $e) {
				if($e->response)
					$response = $e->response;
				else
					$response = \Response::setCode(500);
			} catch(\Exception $e) {
				$response = \Hook::trigger('exception_'.get_class($e), array($e));
				if($response === null)
					$response = Coxis\Core\Coxis::getExceptionResponse($e);
			}
			\Hook::trigger('filter_response', array($response));
			return $response;
		} catch(\Exception $e) {
			return Coxis\Core\Coxis::getExceptionResponse($e);
		}
	}
}