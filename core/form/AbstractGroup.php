<?php
namespace Coxis\Core\Form;

abstract class AbstractGroup implements \ArrayAccess, \Iterator {
	protected $groupName = null;
	protected $dad;
	public $data = array();
	public $files = array();
	protected $widgets = array();
	
	public function getWidgets() {
		return $this->widgets;
	}
	
	public function getParents() {
		$parents = array();
		
		if($this->groupName !== null)
			$parents[] = $this->groupName;
			
		if($this->dad)
			$parents = array_merge($this->dad->getParents(), $parents);
			
		return $parents;
	}
	
	public function getName() {
		return $this->groupName;
	}
	
	public function isSent() {
		//todo handle get form
		$method = isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']):'get';
		if($method != 'post')
			return false;
		else
			if($this->dad)
				return $this->dad->isSent();
			else
				if($this->groupName)
					return isset($_POST[$this->groupName]);
				else
					return true;
	}

	public function parseWidgets($widgets, $name) {
			if(is_array($widgets)) {
				return new Group($widgets, $this, $name, 
					(isset($this->data[$name]) ? $this->data[$name]:array()), 
					(isset($this->files[$name]) ? $this->files[$name]:array())
				);
				//~ if(isset($this->data[$name]))
					//~ return new Group($widgets, $this, $name, $this->data[$name], $this->files[$name]);
				//~ else
					//~ return new Group($widgets, $this, $name);
			}
			elseif(is_object($widgets) && is_subclass_of($widgets, 'Coxis\Core\Form\WidgetHelper')) {
				if(in_array($name, array('groupName', 'dad', 'data', 'widgets', 'params', 'files'), true))
					throw new \Exception('Can\'t use keyword "'.$name.'" for form widget');
				$widget = $widgets;
				$widget->setName($name);
				$widget->setDad($this);
				
				if($widget->params['type'] == 'file')
					if(isset($this->files[$name]))
						$widget->setValue($this->files[$name]);
					else
						$widget->setValue(array());
				elseif(isset($this->data[$name]))
					$widget->setValue($this->data[$name]);
				else
					if($this->isSent()) {
						if(isset($widget->params['multiple']) && $widget->params['multiple'])
							$widget->value = array();
						else
							$widget->value = '';
					}
					
				return $widget;
			}
			elseif(is_object($widgets) && (is_subclass_of($widgets, 'Form') || is_a($widgets, 'Form'))) {
				$form = $widgets;
				$form->setName($name);
				$form->setDad($this);
				//~ if(isset($this->data[$name]))
					//~ $form->setData($this->data[$name], (isset($this->files[$name]) ? $this->files[$name]:array()));
				$form->setData(
					(isset($this->data[$name]) ? $this->data[$name]:array()),
					(isset($this->files[$name]) ? $this->files[$name]:array())
				);
					
				return $form;
			}
	}
	
	public function addWidgets($widgets, $name=null) {	
		foreach($widgets as $name=>$sub_widgets) {
			$this->widgets[$name] = $this->parseWidgets($sub_widgets, $name);
		}
			
		//todo
		//~ reset data (widgets values) after adding new widgets
			
		return $this;
	}
	
	public function addWidget($widget, $name=null) {
		if(in_array($name, array('groupName', 'dad', 'data', 'widgets', 'params')))
			throw new \Exception('Can\'t use keyword '.$name.' for form widget');
		$this->widgets[$name] = $this->parseWidgets($widget, $name);
		
		return $this;
	}
	
	public function setDad($dad) {
		$this->dad = $dad;
		$this->remove('_csrf_token');
	}
	
	public function setWidgets($widgets) {
		$this->widgets = array();
		$this->addWidgets($widgets, $this);
	}
	
	public function setName($name) {
		$this->groupName = $name;
	}
	
	public function setData($data, $files) {
		$this->data = $data;
		$this->files = $files;
		
		$this->updateChilds();
	}
	
	public function hasFile() {
		foreach($this->widgets as $name=>$widget) {
			if(is_subclass_of($widget, 'Coxis\Core\Form\AbstractGroup')) {
				if($widget->hasFile())
					return true;
			}
			elseif($widget->params['type'] == 'file')
				return true;
		}
		
		return false;
	}
	
	protected function updateChilds() {
		foreach($this->widgets as $name=>$widget)
			if(is_subclass_of($widget, 'Coxis\Core\Form\AbstractGroup'))
				$widget->setData(
					(isset($this->data[$name]) ? $this->data[$name]:array()),
					(isset($this->files[$name]) ? $this->files[$name]:array())
				);
				//~ if(isset($this->data[$name]))
					//~ $widget->setData($this->data[$name], (isset($this->files[$name]) ? $this->files[$name]:array()));
				//~ else
					//~ $widget->setData(array());
			else {
				//~ if(isset($this->data[$name]))
					//~ $widget->value = $this->data[$name];
				if($widget->params['type'] == 'file') {
					if(isset($this->files[$name]))
						$widget->value = $this->files[$name];
					else
						$widget->value = null;
				}
				elseif(isset($this->data[$name]))
					$widget->value = $this->data[$name];
				else {
					if($this->isSent()) {
						if(isset($widget->params['multiple']) && $widget->params['multiple'])
							$widget->value = array();
						else
							$widget->value = '';
					}
					else
						$widget->value = null;
				}
			}
	}
	
	public function errors() {
		$errors = array();
	
		foreach($this->widgets as $name=>$widget)
			if(is_subclass_of($widget, 'Coxis\Core\Form\AbstractGroup')) {
				$errors[$name] = $widget->errors();
				if(sizeof($errors[$name]) == 0)
					unset($errors[$name]);
			}
		
		$errors = array_merge($this->my_errors(), $errors);
		
		return $errors;
	}
	
	public function my_errors() {
		$validator = new Validator();
		$constrains = array();
		$messages = array();
		
		foreach($this->widgets as $name=>$widget)
			if(!is_subclass_of($widget, 'Coxis\Core\Form\AbstractGroup')) {
				if(isset($widget->params['rules']))
					$constrains[$name] = $widget->params['rules'];
				if(isset($widget->params['messages']))
					$messages[$name] = $widget->params['messages'];
				if(isset($widget->params['choices']))
					$constrains[$name]['in']	=	array_keys($widget->params['choices']);
				//todo all constrains type.. or a better system?
			}
			
		//~ d($this->widgets);

		$validator->setConstrains($constrains);
		$validator->setMessages($messages);

		return $validator->validate($this->data, $this->files);
	}
	
	public function save() {
		if($errors = $this->errors()) {
			$e = new FormException();
			$e->errors = $errors;
			throw $e;
		}
	
		return $this->_save();
	}
	
	public function _save($group=null) {
		if(!$group)
			$group = $this;
			
		if(is_subclass_of($group, 'Coxis\Core\Form\AbstractGroup'))
			foreach($group->widgets as $name=>$widget) {
				if(is_subclass_of($widget, 'Coxis\Core\Form\AbstractGroup')) {
					$widget->_save($widget);
				}
			}
	}
	
	public function isValid() {
		return !$this->errors();
	}
	
	public function remove($name) {
		unset($this->widgets[$name]);
		unset($this->params[$name]);
	}
	
	public function __unset($name) {
		$this->remove($name);
	}

	public function get($name) {
		return $this->widgets[$name];
	}
	
	public function __get($name) {
		return $this->get($name);
	}
	
	public function __set($k, $v) {
		$this->widgets[$k] = $this->parseWidgets($v, $k);
		
		return $this;
	}
	
	/* IMPLEMENTS */
	
    public function offsetSet($offset, $value) {
		if(is_null($offset))
			$this->widgets[] = $this->parseWidgets($value, sizeof($this->widgets));
		else
			$this->widgets[$offset] = $this->parseWidgets($value, $offset);
    }
	
    public function offsetExists($offset) {
		return isset($this->widgets[$offset]);
    }
	
    public function offsetUnset($offset) {
		unset($this->widgets[$offset]);
    }
	
    public function offsetGet($offset) {
		return isset($this->widgets[$offset]) ? $this->widgets[$offset] : null;
    }
	
    public function rewind() {
		reset($this->widgets);
    }
  
    public function current() {
		return current($this->widgets);
    }
  
    public function key()  {
		return key($this->widgets);
    }
  
    public function next()  {
		return next($this->widgets);
    }
	
    public function valid() {
		$key = key($this->widgets);
		$var = ($key !== NULL && $key !== FALSE);
		return $var;
    }
}