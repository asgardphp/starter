<?php
class ModelFile {
	public $model;
	public $params;
	public $name;
	public $tmp_file = array();
	
	function __construct($model, $file, $name=null, $tmp_file=array()) {
		if(!$model::hasProperty($file))
			throw new \Exception('File '.$file.' does not exist for model '.get_class($model));
		$this->model = $model;
		$this->file = $file;
		$this->params = $model::property($file);
		$this->name = $name;
		$this->tmp_file = $tmp_file;
	}
	
	public function exists() {
		if($this->multiple()) {
			$files = $this->absGet();
			foreach($this->tmp_file as $file)
				$files[] = $file['path'];
			foreach($files as $file)
				if(!file_exists($file))
					return false;
		}
		else {
			if($this->tmp_file)
				$path = $this->tmp_file;
			else {
				if(!$this->get())
					return false;
				$path = 'web/'.$this->get();
			}

			return file_exists($path);
		}
	}
	
	public function dir($absolute=false) {
		$dir = $this->params->dir;
		$dir = trim($dir, '/');
		if($absolute)
			$dir = 'web/upload/'.$dir.($dir ? '/':'');
		else
			$dir = 'upload/'.$dir.($dir ? '/':'');
		return $dir;
	}
	
	public function get($absolute=false) {
		$dir = $this->dir($absolute);
		$path = $this->name;
		
		if($this->multiple()) {
			if(!$path)
				return array();
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
	
	public function save() {
		if(!$this->tmp_file)
			return;

		if(!$this->multiple())
			$files = array($this->tmp_file);
		else
			$files = $this->tmp_file;

		foreach($files as $file) {
			if(!$file['name'] || !$file['tmp_name'])
				continue;
			$to = $this->dir(true).$file['name'];
			if($this->type() == 'image') {
				if(!($format = $this->format()))
					$format = IMAGETYPE_JPEG;
				$filename = ImageManager::load($file['tmp_name'])->save($to, $format);
			}
			else
				$filename = FileManager::move_uploaded($file['tmp_name'], $to);
			
			if($this->multiple()) {
				if(!is_array($this->name))
					$this->name = array();
				array_push($this->name, $filename);
			}
			else
				$this->name = $filename;
		}
		$this->tmp_file = array();

		return $this;
	}

	public function add($files) {
		if(!$this->multiple())
			return;
		$this->tmp_file = array_merge($this->tmp_file, $files);
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
		$this->name = null;
		
		return $this;
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
	
	public function multiple() {
		return $this->params->multiple;
	}
}