<?php
namespace Coxis\Bundles\Admin\Libs\Form;

class AdminModelForm extends \Coxis\Core\Form\ModelForm {
	public static $SEND = 0;
	public static $SAVE = 1;
	public static $BOTH = 2;
	public $controller = null;
	
	function __construct($model, $controller) {
		parent::__construct($model);
		$this->controller = $controller;
	}
	
	public function def($widget, $options=array()) {
		$model = $this->model;
		
		if($this->model->hasProperty($widget)) {
			$properties = $this->model->property($widget);
			if($properties->in)
				$this->select($widget, $options);
			elseif($properties->type === 'boolean')
				$this->checkbox($widget, $options);
			elseif($properties->type === 'date')
				$this->input($widget, array_merge($options, array('class'=>'text date_picker')));
			elseif($this->model->hasFile($widget)) {
				// $file = $this->model->$widget->params();
				$this->file($widget, $options);
			}
			else
				$this->input($widget, $options);
		}
		elseif(isset($model::$relations[$widget])) {
			$this->relation($widget, $options);
		}
		else {
			$this->input($widget, $options);
		}
			
		return $this;
	}
	
	public function relation($relation, $options=array()) {
		if(!isset($options['label']))
			$options['label'] = ucfirst(str_replace('_', ' ', $relation));
	
		$modelName = get_class($this->model);
	
		$relation = get($modelName::$relations, $relation);
		$relation_model = $relation['model'];
		$widget = $relation;
				
		if(isset($options['choices']))
			$choices = $options['choices'];
		elseif(isset($this->$widget->params['choices']))
			$choices = $this->$widget->params['choices'];
		else {
			$choices = array();
			$all = $relation_model::all();
			foreach($all as $one)
				$choices[$one->id] = $one->__toString();
		}
		
		if($relation['type'] == 'belongsTo' || $relation['type'] == 'hasOne') {
			echo '<p>';
			$label = isset($options['label']) ? $options['label']:ucfirst($widget);
				
			$model = $this->model;
			if(get($model::$relations, $widget, 'required'))
				$label .= '*';
		
			$this->$widget->label($label);
			if($this->$widget->getError())
				echo '<span style="color:#f00">'.$this->$widget->getError().'</span>';
			echo '<br>';
			$this->$widget->select(
				array(
					'class' => 'styled',
				),
				$choices
			);
			echo '</p>';
		}
		elseif($relation['type'] == 'hasMany') {
			echo '<p>';
			$label = isset($options['label']) ? $options['label']:ucfirst($relation);
			if(get($this->model->getDefinition()->relations(), $widget, 'required'))
				$label .= '*';
			
			$this->$widget->label($label);
			if($this->$widget->getError())
				echo '<span style="color:#f00">'.$this->$widget->getError().'</span>';
			echo '<br>';
			$this->$widget->select(
				array(
					'class' => 'styled',
					'multiple'	=>	true,
					'attrs'	=>	array(
						'style'	=>	'height:100px',
					),
				),
				$choices
			);
			echo '</p>';
		}
		elseif($relation['type'] == 'HMABT') {
			echo '<p>';
			$label = isset($options['label']) ? $options['label']:ucfirst($widget);
			if(get($modelName::$relations, $widget, 'required'))
				$label .= '*';
			
			$this->$widget->label($label);
			if($this->$widget->getError())
				echo '<span style="color:#f00">'.$this->$widget->getError().'</span>';
			echo '<br>';
			$this->$widget->select(
				array(
					'class' => 'styled',
					'multiple'	=>	true,
					'attrs'	=>	array(
						'style'	=>	'height:100px',
					),
				),
				$choices
			);
			echo '</p>';
		}
		
		return $this;
	}
	
	public function prepareLabel($widget, $options) {
		if(!isset($options['label']))
			$label = ucfirst(str_replace('_', ' ', $widget));
		else
			$label = $options['label'];
		
		try {
			if($this->model->property($widget)->required)
				$label .= '*';
		} catch(\ErrorException $e) {} #if widget does not belong to the model
			
		return $label;
	}
	
	public function select($widget, $options=array(), $choices=null) {
		$label = $this->prepareLabel($widget, $options);
			
		AdminForm::select($this->$widget, $label, $options, $choices);
		
		return $this;
	}
	
	public function input($widget, $options=array()) {
		$label = $this->prepareLabel($widget, $options);
			
		AdminForm::input($this->$widget, $label, $options);
		
		return $this;
	}
	
	public function password($widget, $options=array()) {
		$label = $this->prepareLabel($widget, $options);
			
		AdminForm::input($this->$widget, $label, $options);
		
		return $this;
	}
	
	public function textarea($widget, $options=array()) {
		$label = $this->prepareLabel($widget, $options);
			
		AdminForm::textarea($this->$widget, $label, $options);
		
		return $this;
	}
	
	public function checkbox($widget, $options=array()) {
		$label = $this->prepareLabel($widget, $options);
		
		AdminForm::checkbox($this->$widget, $label, $options);
		
		return $this;
	}
	
	public function wysiwyg($widget, $options=array()) {
		$label = $this->prepareLabel($widget, $options);
			
		AdminForm::wysiwyg($this->$widget, $label, $options);
		
		return $this;
	}
	
	public function file($widget, $options=array()) {
		$label = $this->prepareLabel($widget, $options);
		
		if($this->$widget->params['type'] != 'file')
			throw new \Exception($widget.' should be a file.');

		$path = $this->model->$widget->get();
		$optional = !$this->model->$widget->required();
				
		if($this->model->property($widget)->multiple) {
			if(!$this->model->isNew()):
				$uid = Tools::randstr(10);
				HTML::code_js("
					$(function(){
						multiple_upload('$uid', '".$this->controller->url_for('addFile', array('id' => $this->model->id, 'file' => $widget), false)."');
					});");
				?>
				<div class="block">
				
					<div class="block_head">
						<div class="bheadl"></div>
						<div class="bheadr"></div>
						
						<h2><?php echo $label ?></h2>
						<?php
						if(isset($options['nb']))
							echo '<span>'.$options['nb'].'</span>';
						?>
					</div>		<!-- .block_head ends -->
					
					<div class="block_content">
						<script>
						window.parentID = <?php echo $this->model->id ?>;
						</script>
						<ul class="imglist">
							<?php
							$i=1;
							foreach($path as $one_path):
							?>
							<li>
								<img src="<?php echo \URL::to('imagecache/admin_thumb/'.$one_path) ?>" alt=""/>
								<ul>

									<li class="view"><a href="<?php echo \URL::to($one_path) ?>" rel="facebox">Voir</a></li>
									<li class="delete"><a href="<?php echo $this->controller->url_for('deleteFile', array('id' => $this->model->id, 'pos' => $i, 'file' => $widget), false) ?>">Suppr.</a></li>
								</ul>
							</li>
							<?php
							$i++;
							endforeach;
							?>
							</li>
							
						</ul>
						
						<p id="<?php echo $uid ?>">
							<label><?php echo __('Upload:') ?></label><br />
							<input type="file" id="<?php echo $uid ?>-filesupload" class="filesupload" /><br/>
							<span class="uploadmsg"><?php echo __('Maximum size 3Mb') ?></span>
							<div id="<?php echo $uid ?>-custom-queue"></div>
						</p>
						
					</div>		<!-- .block_content ends -->
					
					<div class="bendl"></div>
					<div class="bendr"></div>
					
				</div>		<!-- .leftcol ends -->
			<?php endif;
		}
		else {
			AdminForm::file($this->$widget, $label, $options);
							
			if(!$this->model->isNew() && $this->model->$widget->exists()) {
				if($this->model->$widget->type() == 'image') {
					echo '<p>
						<a href="../'.$path.'" rel="facebox"><img src="../'.ImageCache::src($path, 'admin_thumb').'" alt=""/></a>
					</p>';
				}
				else {
					echo '<p>
						<a href="../'.$path.'">'.__('Download').'</a>
					</p>';
				}
				
				if($optional && !$this->model->isNew()):
					?>
					<a href="<?php echo $this->controller->url_for('deleteSingleFile', array('file'=>$widget, 'id'=>$this->model->id)) ?>"><?php echo __('Delete') ?></a><br/><br/>
					<?php
				endif;
			}
		}	
		
		return $this;
	}

	public function h3($title) {
		AdminForm::h3($title);
		
		return $this;
	}

	public function h4($title) {
		AdminForm::h3($title);
		
		return $this;
	}

	public function end($submits=null) {
		if($submits===null)
			$submits = static::$BOTH;
		echo '<hr />
						<p>'.
							($submits!=static::$SAVE ? '<input name="send" type="submit" class="submit long" value="'.__('Save & Back').'" /> ':'').
							($submits!=static::$SEND ? '<input name="stay" type="submit" class="submit long" value="'.__('Save & Stay').'" /> ':'').
						'</p>';
		parent::end();
		
		return $this;
	}
}