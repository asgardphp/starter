<?php
class Collection implements Iterator
{
    private $elements = array();
    private $model = null;

    public function __construct($model, $array) {
        if (is_array($array)) {
            $this->elements = $array;
        }
		$this->model = $model;
    }

    public function rewind() {
        reset($this->elements);
    }

    public function current() {
        $element = current($this->elements);
        return $element;
    }

    public function key() {
        $key = key($this->elements);
        return $key;
    }

    public function next() {
        $next = next($this->elements);
        return $next;
    }

    public function valid() {
        $key = key($this->elements);
        $var = ($key !== NULL && $key !== FALSE);
        return $var;
    }
	
	//todo persist

	public function add($element) {
		$this->elements[] = $element;
		
		return $this;
	}

	public function delete($value) {
		foreach($this->elements as $k=>$element)
			if($element == $value) {
				unset($this->elements[$k]);
				return 1;
			}
			
		return 0;
	}

	public function addByID($id) {
		//todo
		return $this;
	}

	public function deleteByID($id) {
		foreach($this->elements as $k=>$element)
			if($element->id == $id) {
				unset($this->elements[$k]);
				return 1;
			}
			
		return 0;
	}
	
	public function toArray() {
		return $this->elements;
	}
}