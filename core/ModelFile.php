<?php
class ModelFile {
	public $model;
	public $file;
	public $params;
	public $name;
	public $tmp_path;
	
	function __construct($model, $file, $name, $tmp_path=null) {
		if(!$model::hasProperty($file))
			throw new \Exception('File '.$file.' does not exist for model '.$model::getModelName());
		$this->model = $model;
		$this->file = $file;
		$this->params = $model::property($file);
		$this->name = $name;
		$this->tmp_path = $tmp_path;
	}
	
	public function exists() {
		if($this->tmp_path)
			$path = $this->tmp_path;
		else {
			if(!$this->get())
				return false;
			$path = 'web/'.$this->get();
		}

		return file_exists($path);
	}
	
	public function dir() {
		$dir = $this->params->dir;
		$dir = trim($dir, '/');
		$dir = 'upload/'.$dir.($dir ? '/':'');
		return $dir;
	}
	
	public function get() {
		$dir = $this->dir();
		$path = $this->name;
		
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
		return isset($this->params->required) && $this->params->required;
	}
	
	public function type() {
		return $this->params->filetype;
	}
	
	public function format() {
		return $this->params->format;
	}
	
	public function save() {
		if(!$this->tmp_path)
			return;
		$to = 'web/'.$this->get();
		
		if($this->type() == 'image') {
			if(!($format = $this->format()))
				$format = IMAGETYPE_JPEG;
			$filename = ImageManager::load($this->tmp_path)->save($to, $format);
		}
		else
			$filename = FileManager::move_uploaded($this->tmp_path, $to);
			
		if($this->multiple())
			array_push($this->name, $filename);
		else
			$this->name = $filename;
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
		return $this->params->multiple;
	}
}