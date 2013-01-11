<?php
namespace Coxis\SEO;

class SEO {
	public static function canonical($controller, $canonical, $relative=true, $redirect=true) {
		if(!$controller->request->isInitial)
			return;
		if($relative)
			$uri = $controller->request->url->get();
		else
			$uri = $controller->request->url->current();
		if($redirect && $canonical != $uri)
			throw new ControllerException('Wrong location', $controller->response->setCode(301)->redirect($canonical, $relative));
		if($relative)
			HTML::code('<link rel="canonical" href="'.$controller->request->url->to($canonical).'">');
		else
			HTML::code('<link rel="canonical" href="'.$canonical.'">');
	}
}