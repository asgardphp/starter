<?php
class Browser {
	public static function get($url) {
		URL::$url = $url;
		
		ob_start();
		try {
			Router::run('Front', 'main');
		}
		catch(EndException $e) {
			ob_end_clean();
			return $e->result;
		}
		catch(Exception $e) {}
		
		ob_end_clean();
		return null;
	}
}