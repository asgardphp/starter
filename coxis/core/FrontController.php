<?php
namespace Coxis\Core;

class FrontController extends Controller {
	public function mainAction() {
		Profiler::checkpoint('Before loading coxis');
		\Coxis::load();
		Profiler::checkpoint('After loading coxis');
		return static::getResponse();
	}

	public static function getResponse() {
		try {
			try {
				\Hook::trigger('start');
				Profiler::checkpoint('Before dispatching');

				preg_match('/\.([a-zA-Z0-9]{1,5})$/', \URL::get(), $matches);#todo move it into request constructor?
				if(isset($matches[1]))
					\Request::setParam('format', $matches[1]);

				$response = \Router::dispatch();
				Profiler::checkpoint('After dispatching');
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
			return $response;
		} catch(\Exception $e) {
			return Coxis\Core\Coxis::getExceptionResponse($e);
		}
	}
}