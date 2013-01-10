<?php
namespace Coxis\Core\Filters;
class PageLayout extends Filter {
	public function after($chain, $controller, &$result) {
		if(!is_string($result))
			return;

		if(function_exists('getallheaders'))
			if(\get(\getallheaders(), 'X-Requested-With') == 'XMLHttpRequest')
				return;

		try {
			if(\Response::getHeader('Content-Type') && \Response::getHeader('Content-Type')!='text/html')
				return;
		} catch(\Exception $e) {}

		if(is_array(\Memory::get('layout')) && sizeof(\Memory::get('layout')) >= 2 && $result !== null) {
			$result = Viewable::staticDoRun(\Memory::get('layout', 0), \Memory::get('layout', 1), $result);
			if(\Memory::get('htmlLayout') !== false)
				$result = View::render('app/standard/views/default/html.php', array('content'=>$result));
		}
	}
}