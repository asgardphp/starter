<?php
namespace Coxis\Core\Form;

class Widget extends WidgetHelper {
	protected $parent;

	function __construct($params=array()) {
		$this->params = $params;
		parent::__construct();
	}
	
	public function setDad($dad) {
		$this->dad = $dad;
	}
	
	public function label($label=null) {
		if($label)
			echo '<label'.($this->name ? ' for="'.$this->getID():'').'">'.$label.'</label>';
		else
			echo '<label'.($this->name ? ' for="'.$this->getID():'').'">'.ucfirst($this->name).'</label>';
			
		return $this;
	}

	public function radios($options=array(), $choices=null) {
		#todo options
		if($choices !== null)
			$this->params['choices'] = $choices;
		if(!isset($this->params['choices']))
			throw new \Exception('No multiple choices available.');
		return new RadioGroup($this->dad, $this->name, $this->params, $this->value);
	}
	
	public function getradio($value) {
		$radios = $this->radios(array(), array($value=>$value));
		return $radios[$value];
	}
	
	public function checkboxes($options=array(), $choices=null) {
		#todo options
		if($choices !== null)
			$this->params['choices'] = $choices;
		if(!isset($this->params['choices']))
			throw new \Exception('No multiple choices available.');
		return new CheckboxGroup($this->dad, $this->name, $this->params, $this->value);
	}
	
	public function getcheckbox($value) {
		$checkboxes = $this->checkboxes(array(), array($value=>$value));
		return $checkboxes[$value];
	}
	
	public function checkbox($options=array()) {
		$widget = $this->params;
		
		$widget['view']	=	array_merge(isset($widget['view']) ? $widget['view']:array(), $options);
		
		$value = '';
		if($this->value !== null)
			$value = $this->value;
		elseif(isset($widget['view']['value']))
			$value = $widget['view']['value'];
		elseif(isset($widget['default']))
			$value = $widget['default'];
			
		$params = array(
			'id'	=>	$this->getID(),
			'type'	=>	'checkbox',
			'name'	=>	$this->getName(),
			'value'	=>	1,
		);
		if($value == 1)
			$params['checked'] = 'checked';
		if(isset($widget['view']['class']))
			if(is_array($widget['view']['class']))
				$params['class'] = implode(' ', $widget['view']['class']);
			else
				$params['class'] = $widget['view']['class'];
				
		if(isset($options['attrs']))
			$params = array_merge($options['attrs'], $params);
				
		$res = HTMLHelper::tag('input', $params);
		
		echo $res;
	}
	
	protected function _input($type, $options=array()) {
		$widget = $this->params;
		
		$widget['view']	=	array_merge(isset($widget['view']) ? $widget['view']:array(), $options);
		
		$value = '';
		if($this->value !== null)
			$value = $this->value;
		elseif(isset($widget['view']['value']))
			$value = $widget['view']['value'];
		elseif(isset($widget['default']))
			$value = $widget['default'];
	
		$params = array(
			'id'		=>	$this->getID(),
			'type'		=>	$type,
			'name'	=>	$this->getName(),
		);
		if($type != 'file')
			$params['value'] = $value;
			
		if(isset($options['attrs']))
			$params = array_merge($params, $options['attrs']);
		if(isset($widget['view']['class']))
			if(is_array($widget['view']['class']))
				$params['class'] = implode(' ', $widget['view']['class']);
			else
				$params['class'] = $widget['view']['class'];
				
		if(isset($options['attrs']))
			$params = array_merge($options['attrs'], $params);
	
		#todo with Coxis javascript
		if(isset($widget['view']['placeholder']))
			\Coxis\Core\JS::placeholder('#'.$this->getID(), $widget['view']['placeholder']);
		echo \Coxis\Core\Form\HTMLHelper::tag('input', $params);
		
		return $this;
	}
	
	public function select($options=array(), $choices=null) {
		$widget = $this->params;
		$multiple = (isset($options['multiple']) && $options['multiple']);
		
		$widget['view']	=	array_merge(isset($widget['view']) ? $widget['view']:array(), $options);

		if($multiple)
			$value = array();
		else
			$value = '';
		if(isset($widget['view']['value']))
			$value = $widget['view']['value'];
		elseif($this->value !== null)
			$value = $this->value;
		elseif(isset($widget['default']))
			$value = $widget['default'];
			
		if($multiple && !is_array($value))
			if(!$value)
				$value = array();
			else
				$value = array($value);

		if($choices===null) {
			if(isset($widget['choices']))
				$choices = $widget['choices'];
			else
				$choices = array();
		}
			
		if($multiple) {
			$params = array(
				'id'		=>	$this->getID(),
				'name'	=>	$this->getName($this->name).'[]',
				'multiple'	=>	'multiple',
			);
		}
		else {
			$params = array(
				'id'		=>	$this->getID(),
				'name'	=>	$this->getName($this->name),
			);
		}
		if(isset($options['attrs']))
			$params = array_merge($params, $options['attrs']);
		if(isset($widget['view']['class']))
			if(is_array($widget['view']['class']))
				$params['class'] = implode(' ', $widget['view']['class']);
			else
				$params['class'] = $widget['view']['class'];
				
		if(isset($options['attrs']))
			$params = array_merge($options['attrs'], $params);
				
		$res = HTMLHelper::tag('select', $params)."\n";
		if(!$multiple && !isset($choices['']))
			$res .= '<option value="">'.__('Choose').'</option>'."\n";
		foreach($choices as $k=>$v) {
			if(is_array($v)){
				$res .= HTMLHelper::tag('optgroup', array('label'	=>	$k))."\n";
				foreach($v as $k2=>$v2)
				//~ d($k2, $value);
					if($multiple)
						$res .= '<option value="'.\HTML::sanitize($k2).'"'.(in_array($k2, $value) ? ' selected="selected"':'').'>'.$v2.'</option>'."\n";
					else
						$res .= '<option value="'.\HTML::sanitize($k2).'"'.($k2==$value ? ' selected="selected"':'').'>'.$v2.'</option>'."\n";
				$res .= HTMLHelper::endTag('optgroup')."\n";
			}
			else {
				if($multiple)
					$res .= '<option value="'.$k.'"'.(in_array($k, $value) ? ' selected="selected"':'').'>'.$v.'</option>'."\n";
				else
					$res .= '<option value="'.$k.'"'.($k==$value ? ' selected="selected"':'').'>'.$v.'</option>'."\n";
			}
		}
		$res .= HTMLHelper::endTag('select');
		
		echo $res;
		
		return $this;
	}
	
	public function input($options=array()) {
		return $this->_input('text', $options);
	}
	
	public function password($options=array()) {
		return $this->_input('password', $options);
	}
	
	public function file($options=array()) {
		return $this->_input('file', $options);
	}
	
	public function hidden($options=array()) {
		return $this->_input('hidden', $options);
	}
	
	public function textarea($options=array()) {
		$widget = $this->params;
		
		$widget['view']	=	array_merge(isset($widget['view']) ? $widget['view']:array(), $options);
		
		$value = '';
		if($this->value !== null)
			$value = $this->value;
		elseif(isset($widget['view']['value']))
			$value = $widget['view']['value'];
		elseif(isset($widget['default']))
			$value = $widget['default'];
	
		$params = array(
			'id'		=>	$this->getID(),
			'name'	=>	$this->getName(),
		);
		if(isset($options['attrs']))
			$params = array_merge($params, $options['attrs']);
		if(isset($widget['view']['class']))
			if(is_array($widget['view']['class']))
				$params['class'] = implode(' ', $widget['view']['class']);
			else
				$params['class'] = $widget['view']['class'];
				
		if(isset($options['attrs']))
			$params = array_merge($options['attrs'], $params);
	
		#todo with Coxis javascript
		if(isset($widget['view']['placeholder']))
			echo '<script>placeholder("#'.$this->getID().'", "'.$widget['view']['placeholder'].'")</script>';
		echo HTMLHelper::tag('textarea', $params);
		echo \HTML::sanitize($value);
		echo HTMLHelper::endTag('textarea');
		
		return $this;
	}
	
	//todo
	public function wysiwyg($options=array()) {
		$widget = $this->params;
		
		$widget['view']	=	array_merge(isset($widget['view']) ? $widget['view']:array(), $options);
		
		$value = '';
		if($this->value !== null)
			$value = $this->value;
		elseif(isset($widget['view']['value']))
			$value = $widget['view']['value'];
		elseif(isset($widget['default']))
			$value = $widget['default'];
	
		$params = array(
			'id'		=>	$this->getID(),
			'name'	=>	$this->getName(),
		);
		if(isset($options['attrs']))
			$params = array_merge($params, $options['attrs']);
		if(isset($widget['view']['class']))
			if(is_array($widget['view']['class']))
				$params['class'] = implode(' ', $widget['view']['class']);
			else
				$params['class'] = $widget['view']['class'];
				
		if(isset($options['attrs']))
			$params = array_merge($options['attrs'], $params);
	
		echo HTMLHelper::tag('textarea', $params);
		echo \HTML::sanitize($value);
		echo HTMLHelper::endTag('textarea');
		?>
		<script>
		//<![CDATA[
			$(function(){
				var CKEDITOR_BASEPATH = '<?php echo \URL::to('bundles/ckeditor/ckeditor/') ?>';
				CKEDITOR.basePath = '<?php echo\ URL::to('bundles/ckeditor/ckeditor/') ?>';
				var editor = CKEDITOR.instances['<?php echo $this->getID() ?>'];
				if (editor)
					editor.destroy(true);
				CKEDITOR.replace('<?php echo $this->getID() ?>'
					<?php if(isset($options['config'])): ?>
						, {
								customConfig : '<?php echo $options['config'] ?>'
							}
					<?php endif ?>
				);
			});
		//]]>
		</script>
		<?php
		HTML::include_js('bundles/ckeditor/ckeditor/ckeditor.js');
		HTML::include_js('bundles/ckeditor/ckeditor/_samples/sample.js');
		HTML::include_css('bundles/ckeditor/ckeditor/_samples/sample.css');
		
		return $this;
	}

	public function getError() {
		if(isset($this->dad->errors[$this->name]))
			if(is_array($this->dad->errors[$this->name])) {
				$res = '';
				foreach($this->dad->errors[$this->name] as $error)
					$res .= $error."<br/>\n";
				return $res;
			}
			else
				return $this->dad->errors[$this->name];
	}

	public function error() {
		echo $this->getError();
	}
}