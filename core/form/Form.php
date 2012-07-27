<?php
namespace Coxis\Core\Form;

class Form extends AbstractGroup {
	public $params = array(
		'method'	=>	'post',
		'action'	=>	'',
	);
	private $callbacks = array();

	function __construct($param1=null, $param2=array(), $param3=array()) {
		static::generateTokens();
	
		//$param1		=	form params
		//$param2	=	form widgets
		//OR
		//$param1		=	form name
		//$param2	=	form params
		//$param3	=	form widgets
		if(is_array($param1)) {
			$name = null;
			$params = $param1;
			$this->params = array_merge($this->params, $params);
			$this->fetch();
			$this->setWidgets($param2);
		}
		else {
			$name = $param1;
			$params = $param2;
			$this->params = array_merge($this->params, $params);
			$this->groupName = $name;
			$this->fetch();
			$this->setWidgets($param3);
		}
		
		$this->addWidget(
			new Widget(array(
				'rules'	=>	array(
					'eq'	=>	$_SESSION['_csrf_token'],
				),
				'messages'	=>	array(
					'eq'	=>	'Invalid CSRF token.',
				),
				'view'	=>	array(
					'value'	=>	$_SESSION['_csrf_token'],
				),
			)),
			'_csrf_token'
		);
	}
	
	public function setCallback($name, $cb) {
		$this->callbacks[$name] = $cb;
	}
	
	public function callback($name, $args=array()) {
		if(isset($this->callbacks[$name])) {
			$args = array_merge(array($this), $args);
			return call_user_func_array($this->callbacks[$name], $args);
		}
		else
			return null;
	}
	
	private static function generateTokens() {	
		if(!isset($_SESSION['_csrf_token']))
			$_SESSION['_csrf_token'] = Tools::randstr();
	}
	
	public function __toString() {
		return $this->params['name'];
	}
	
	private function convertTo($type, $files) {
		$res = array();
		foreach($files as $name=>$file)
			if(is_array($file))
				$res[$name] = $this->convertTo($type, $file);
			else
				$res[$name][$type] = $file;
				
		return $res;
	}
	
	private function merge_all($name, $type, $tmp_name, $error, $size) {
		foreach($name as $k=>$v) {
			if(isset($v['name']) && !is_array($v['name'])) {
				$name[$k] = array_merge($v, $type[$k], $tmp_name[$k], $error[$k], $size[$k]);
			}
			else {
				//continue recursive
				$name[$k] = $this->merge_all($name[$k], $type[$k], $tmp_name[$k], $error[$k], $size[$k]);
			}
		}
		
		return $name;
	}
	
	public function fetch() {
		$raw = array();
		$files = array();
			
		if($this->groupName)
			if(isset($_FILES[$this->groupName]))
				$raw = $_FILES[$this->groupName];
			else
				$raw = array();
		else
			$raw = $_FILES;
			
		if(isset($raw['name'])) {
			$name = $this->convertTo('name', $raw['name']);
			$type = $this->convertTo('type', $raw['type']);
			$tmp_name = $this->convertTo('tmp_name', $raw['tmp_name']);
			$error = $this->convertTo('error', $raw['error']);
			$size = $this->convertTo('size', $raw['size']);
			
			$files = $this->merge_all($name, $type, $tmp_name, $error, $size);
		}
	
		$this->data = array();
		if($this->groupName)
				$this->setData(
					(isset($_POST[$this->groupName]) ? $_POST[$this->groupName]:array()),
					$files
				);
		else
			if(isset($_POST))
				$this->setData($_POST, $_FILES);
						
		return $this;
	}
	
	//todo should not pass this args here but when defining the form
	public function start($action='', $method='post') {
	//~ d($this->hasFile());
		echo '<form action="'.$action.'" method="'.$method.'"'.($this->hasFile() ? ' enctype="multipart/form-data"':'').'>';
		
		return $this;
	}
	
	public function end() {
		$this->_csrf_token->hidden();
		echo '</form>';
		
		return $this;
	}
	
	public function submit($value) {
		echo HTMLHelper::tag('input', array(
			'type'		=>	'submit',
			'value'	=>	$value,
		));
		
		return $this;
	}
	
	public function getData() {
		$res = array();
		
		foreach($this->widgets as $widget) {
			if($widget instanceof \Coxis\Core\Form\WidgetHelper) {
				if($widget->params['type'] == 'boolean')
					$res[$widget->name] = (boolean)$widget->val();
				else
					$res[$widget->name] = $widget->val();
			}
		}
		
		return $res;
	}
}