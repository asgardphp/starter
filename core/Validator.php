<?php
namespace Coxis\Core;

class Validator {
	public $constrains = array();
	public $vars = array();

	public function setVars($vars) {
		$this->vars = $vars;
	}
	
	public function setConstrains($constrains) {
		$cp = $constrains;
		//todo check this part below have prioritize constrains..
		if(isset($constrains['required'])) {
			$v = $constrains['required'];
			unset($constrains['required']);
			$constrains = array_merge(array('required'=>$v), $constrains);
		}
		
		foreach($constrains as $property=>$property_constrains)
			foreach($property_constrains as $constrain=>$value)
				if(!isset($value['condition']) || !is_array($value['condition']))
					if($value === '')
						$cp[$property][$constrain] = array('condition' => true);
					else
						$cp[$property][$constrain] = array('condition' => $value);
		$this->constrains = $cp;

		return $this;
	}
	
	public function setMessages($messages) {
		//Model Messages
		foreach($messages as $property=>$constrains)
			foreach($constrains as $constrain=>$message)
				if(isset($this->constrains[$property][$constrain]))
					$this->constrains[$property][$constrain]['message'] = $message;
		
		return $this;
	}

	var $default_messages = array(
		'max'		=>	'Le champ "%s" doit être inféreieur ou égal à %s.',
		'min'		=>	'Le champ "%s" doit être supérieur ou égal à %s.',
		'required'		=>	'Le champ "%s" est obligatoire.',
		'file_required'	=>	'Le fichier "%s" est obligatoire.',
		'length'			=>	'Le champ "%s" doit faire moins de %s caractères.',
		'type'				=>	array(
			'integer'	=>	'Le champ "%s" doit être un nombre.',
			'email'	=>	'Le champ "%s" doit être une adresse email valide.'
		),
		'_default'		=>	'Le champ "%s" est incorrect.',
	);
	
	public function isValid($var, $constrain, $condition) {
		switch($constrain) {
			case 'validation':
				$model = $condition[0];
				$function = $condition[1];
				if(!$model->$function($var))
					return false;
				break;
			case 'required':
				if($condition && is_array($var) && sizeof($var) == 0)
					return false;
				elseif($condition && ($var===null || $var===''))
					return false;
				break;
			case 'file_required':
				if(!is_array($var) || !isset($var['tmp_name']) || empty($var['tmp_name']))
					return false;
				break;
			case 'eq':
				if($var !== $condition)
					return false;
				break;
			case 'max':
				if((int)$var>$condition)
					return false;
				break;
			case 'min':
				if((int)$var<$condition)
					return false;
				break;
			case 'length':
				if(strlen($var)>$condition)
					return false;
				break;
			case 'type':
				switch($condition) {
					case 'text':
						break;
					case 'integer':
						if(!preg_match('/^-?[0-9 ]*$/', $var))
							return false;
						break;
					case 'email':
						if(!preg_match('/^[\w-]+(\.[\w-]+)*@([a-z0-9-]+(\.[a-z0-9-]+)*?\.[a-z]{2,6}|(\d{1,3}\.){3}\d{1,3})(:\d{4})?$/', $var))
							return false;
						break;
					case 'image':
						if(!is_array($var) || !isset($var['tmp_name']) || empty($var['tmp_name']))
							continue;
						list($w, $h, $type) = getimagesize($var['tmp_name']);
						if($type != IMAGETYPE_GIF && $type != IMAGETYPE_JPEG && $type != IMAGETYPE_PNG)
							return false;
						break;
				}
				break;
			case 'in':
				if($var) {
					if(is_array($var)) {
						foreach($var as $k=>$v) {
							if(!in_array($v, $condition))
								return false;
						}
					}
					else{
						if(!in_array($var, $condition))
							return false;
					}
				}
				break;
			case 'regex':
				if(!preg_match($condition, $var))
					return false;
				break;
		}
		
		return true;
	}

	public function validate_property($property, $constrains) {
		if(isset($this->vars[$property]))
			$var = $this->vars[$property];
		else
			$var = '';
		
		//todo redo messages system...
		foreach($constrains as $constrain=>$options) {
			$condition = $options['condition'];
			if(!$this->isValid($var, $constrain, $condition)) {
				if(isset($options['message']))
					$message = $options['message'];
				//~ elseif(isset()) {
					
				//~ }
				//~ Default message
				else {
					if(!is_array($condition) && isset($this->default_messages[$constrain]) && is_array($this->default_messages[$constrain]) && isset($this->default_messages[$constrain][$condition]))
						$message = $this->default_messages[$constrain][$condition];
					elseif(isset($this->default_messages[$constrain]) && !is_array($this->default_messages[$constrain]))
						$message = $this->default_messages[$constrain];
					else
						$message = $this->default_messages['_default'];
					$message = sprintf($message, $property, $condition);
				}
				
				return $message;
			}
		}
		
		//~ return $errors;
	}

	public function validate($vars) {
		$this->vars = $vars;
		$errors = array();
	
		foreach($this->constrains as $property=>$property_constrains)//~ Validate each var
			if($property_error = $this->validate_property($property, $property_constrains))//~ Check if var returns any error
				$errors[] = $property_error;
				
		return $errors;
	}
}