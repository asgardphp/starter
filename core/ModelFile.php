<?php
class ModelFile {
	public $model;
	public $file;
	public $params;
	
	function __construct($model, $file) {
		if(!isset($model::$files[$file]))
			throw new \Exception('File '.$file.' does not exist for model '.$model->getModelName());
		$this->model = $model;
		$this->file = $file;
		$this->params = $model::$files[$file];
	}
	
	public function exists() {
		$filename_property = 'filename_'.$this->file;
		return $this->model->$filename_property && file_exists('web/'.$this->get());
	}
	
	public function raw() {
		$filename_property = 'filename_'.$this->file;
		return $this->model->$filename_property;
	}
	
	public function dir() {
		$dir = $this->params['dir'];
		$dir = trim($dir, '/');
		$dir = 'upload/'.$dir.($dir ? '/':'');
		return $dir;
	}
	
	public function get() {
		$dir = $this->dir();
		$path = $this->raw();
		
		if($this->multiple()) {
			$result = array();
			foreach($path as $filename)
				$result[] = $dir.$filename;
			return $result;
		}
		else {
			if($path)
				return $dir.$path;
			else
				return null;	
		}
	}
	
	public function __toString() {
		return (string)$this->get();
	}
	
	public function params() {
		return $this->params;
	}
	
	public function required() {
		return isset($this->params['required']) && $this->params['required'];
	}
	
	public function type() {
		if(isset($this->params['type']))
			return $this->params['type'];
		else
			return null;
	}
	
	public function format() {
		if(isset($this->params['format']))
			return $this->params['format'];
		else
			return null;
	}
	
	public function add($from, $to) {
		$file_property = 'filename_'.$this->file;
		
		if($this->type() == 'image') {
			if(!($format = $this->format()))
				$format = IMAGETYPE_JPEG;
			$filename = ImageManager::load($from)->save($to, $format);
			
			if($this->multiple())
				array_push($this->model->$file_property, $filename);
			else
				$this->model->$file_property = $filename;
		}
		else {
			$filename = FileManager::move_uploaded($from, $to);
			
			if($this->multiple())
				array_push($this->model->$file_property, $filename);
			else
				$this->model->$file_property = $filename;
			
		}
	}
	
	public function set($path) {
		$filename_property = 'filename_'.$this->file;
		$this->model->$filename_property = $path;
		return $this;
	}
	
	public function delete() {
		$path = $this->get();
		if($this->multiple()) {
			foreach($path as $file) {
				FileManager::unlink(_WEB_DIR_.'/'.$file);
				ImageCache::clearFile($path);
			}
		}
		else {
			if($path) {
				FileManager::unlink(_WEB_DIR_.'/'.$path);
				ImageCache::clearFile($path);
			}
		}
		$file_property = 'filename_'.$this->file;
		$this->$file_property = '';
		
		return $this;
	}
	
	public function multiple() {
		return (isset($this->params['multiple']) && $this->params['multiple']);
	}
}