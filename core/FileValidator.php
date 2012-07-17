<?php
namespace Coxis\Core;

//todo separate constrains and file definition.. e.g. path should not be in constrains..

class FileValidator {
	public $constrains = array();
	public $files = array();

	public function setFiles($files) {
		$this->files = $files;
	}
	
	public function setConstrains($constrains) {
		$cp = $constrains;
		foreach($constrains as $file=>$file_constrains)
			foreach($file_constrains as $constrain=>$value)
				if(!is_array($value) || !isset($value['condition']) || !is_array($value['condition'])) {
					if($value === '')
						$cp[$file][$constrain] = array('condition' => true);
					else
						$cp[$file][$constrain] = array('condition' => $value);
				}
		$this->constrains = $cp;
		
		return $this;
	}
	
	public function setMessages($messages) {
		#Model Messages
		foreach($messages as $file=>$constrains)
			foreach($constrains as $constrain=>$message)
				if(isset($this->constrains[$file][$constrain]))
					$this->constrains[$file][$constrain]['message'] = $message;
		
		return $this;
	}

	var $default_messages = array(
		'error_upload'	=>	'Une erreur est survenue lors de l\'upload du fichier "%s".',
		'required'		=>	'Le fichier "%s" est obligatoire.',
		'type'				=>	array(
			'image'	=>	'Le fichier "%s" doit être une image (jpg, png ou gif).',
		),
		'_default'		=>	'Le fichier "%s" est invalide.',
	);
	
	public function isValid($path, $constrain, $condition, $file_definition) {
		switch($constrain) {
			case 'required':
				if($condition && (!is_string($path) || empty($path) || !file_exists($path)))
					return false;
				break;
			case 'type':
				switch($condition) {
					case 'image':
						if(!$path || !file_exists($path))
							return true;
						list($w, $h, $type) = getimagesize($path);
						if($type != IMAGETYPE_GIF && $type != IMAGETYPE_JPEG && $type != IMAGETYPE_PNG)
							return false;
						break;
				}
				break;
		}
		
		return true;
	}

	public function validate_file($file, $constrains) {
		if(isset($this->files[$file]))
			$var = $this->files[$file];
		else
			$var = '';
			
		if(is_array($var))
			$vars = $var;
		else
			$vars = array($var);
			
		$errors = array();
		
		//~ d($vars);
	
		foreach($constrains as $constrain=>$options) {
			$condition = $options['condition'];
			foreach($vars as $var)
				if(!$this->isValid($var, $constrain, $condition, $constrains)) {
					if(isset($options['message']))
						$message = $options['message'];
					#Default message
					else {
						if(!is_array($condition) && isset($this->default_messages[$constrain]) && is_array($this->default_messages[$constrain]) && isset($this->default_messages[$constrain][$condition]))
							$message = $this->default_messages[$constrain][$condition];
						elseif(isset($this->default_messages[$constrain]) && !is_array($this->default_messages[$constrain]))
							$message = $this->default_messages[$constrain];
						else
							$message = $this->default_messages['_default'];
						$message = sprintf($message, $file, $condition);
					}
					
					$errors[] = $message;
				}
		}
		
		return $errors;
	}

	public function validate($files) {
		$this->files = $files;
		$errors = array();
	
		foreach($this->constrains as $file=>$file_constrains)#Validate each var
			if($file_errors = $this->validate_file($file, $file_constrains))# Check if var returns any error
				$errors = array_merge($errors, $file_errors);
				
		return $errors;
	}
}