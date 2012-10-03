<?php
namespace Coxis\Core\Tools;

class Flash {
	private static $messages = array('success' => array(), 'error' => array());
	private static $instance;

	public static function _autoload() {
		if(isset($_SESSION['messages']))
			static::$messages = $_SESSION['messages'];
	}

	private static function persist() {
		$_SESSION['messages'] = static::$messages;
	}

	public static function addSuccess($message) {
		if(is_array($message))
			foreach($message as $one_message)
				static::$messages['success'][] = $one_message;
		else
			static::$messages['success'][] = $message;
			
		static::persist();
		return true;
	}
	
	public static function addError($message) {
		if(is_array($message))
			foreach($message as $one_message)
				static::$messages['error'][] = $one_message;
		else
			static::$messages['error'][] = $message;
			
		static::persist();
		return true;
	}
	
	public static function showAll() {
		static::showSuccess();
		static::showError();
	}
	
	public static function showSuccess() {
		foreach(Tools::flateArray(static::$messages['success']) as $msg)
			echo '<div class="message success"><p>'.$msg.'</p></div>'."\n";
		static::$messages['success'] = array();	
		static::persist();
	}
	
	public static function showError() {
		foreach(Tools::flateArray(static::$messages['error']) as $msg)
			echo '<div class="message errormsg"><p>'.$msg.'</p></div>'."\n";
		static::$messages['error'] = array();	
		static::persist();
	}

	public static function getInstance() { 
		if(!static::$instance)	static::$instance = new self();

		return static::$instance; 
	} 
}