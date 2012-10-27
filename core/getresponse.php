<?php
try {
	try {
		\BundlesManager::loadBundles();
		\Router::parseRoutes();

		\Hook::trigger('start');

		$output = \Router::dispatch();
		if($output instanceof \Coxis\Core\Response) {
			$content = $output->content;
			$response = $output;
		}
		else {
			$content = $output;
			$response = \Response::setContent($output);
		}
	} catch(\Coxis\Core\ControllerException $e) {
		if($e->response)
			$response = $e->response;
		else
			$response = \Response::setCode(500);
		$content = '';
	} catch(\Exception $e) {
		$response = Coxis\Core\Coxis::getExceptionResponse($e);
		$content = '';
	}
	\Hook::trigger('filter_response', array($response));
	return $response;
} catch(\Exception $e) {
	// while(ob_get_level()){ ob_end_clean(); } #todo
	return Coxis\Core\Coxis::getExceptionResponse($e);
}