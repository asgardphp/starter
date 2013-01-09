<?php
namespace Coxis\Core\Filters;
class PageCaching extends Filter {
	public function getAfterPriority() {
		return 1000;
	}

	public function before($chain) {
		$key = $this->key = $this->calculateKey();
		if($r = \Cache::get($key))
			return $r;
	}

	public function after($chain, $controller, $result) {
		\Cache::set($this->key, $result);
	}

	protected function calculateKey() {
		$key = $this->controller->request['controller'].$this->controller->request['action'];
		#todo varyBy...

		return md5($key);
	}
}