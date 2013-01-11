<?php
namespace Coxis\Core\Inputs;

class Session extends InputsBag {
	public function set($name, $value=null) {
		if(is_array($name)) {
			foreach($name as $k=>$v)
				$this->set($k, $v);
			return $this;
		}
		else {
			if(isset($_SESSION))
				$_SESSION[$name] = $value;
			return parent::set($name, $value);
		}
	}
	  
	public function remove($name) {
		if(isset($_SESSION))
			unset($_SESSION[$name]);
		return parent::remove($name);
	}
}