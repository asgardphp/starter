<?php
class AdminModelForm extends ModelForm {
	static $SEND = 0;
	static $SAVE = 1;
	static $BOTH = 2;
	
	public function def($widget, $options=array()) {
		$properties = $this->model->getProperty($widget);
		
		$modelName = $this->model->getModelName();
		
		if(isset($properties['in']))
			$this->select($widget, $options);
		elseif($properties['type'] === 'boolean')
			$this->checkbox($widget, $options);
		elseif($properties['type'] === 'date')
			$this->input($widget, array_merge($options, array('class'=>'text date_picker')));
		else {
			if($this->model->hasFile($widget)) {
				$file = $this->model->getFile($widget);
				$this->file($widget, $options);
			}
			else {
				$relationships = $modelName::$relationships;
				if(isset($relationships[$widget]))
					$this->relation($widget, $options);
				else
					$this->input($widget, $options);
			}
		}
			
		return $this;
	}
	
	public function relation($relation, $options=array()) {
		if(!isset($options['label']))
			$options['label'] = ucfirst(str_replace('_', ' ', $relation));
	
		$modelName = $this->model->getModelName();
	
		$relationship = access($modelName::$relationships, $relation);
		$relation_model = $relationship['model'];
		$widget = $relation.'_id';
		
		if($relationship['type'] == 'belongsTo' || $relationship['type'] == 'hasOne') {
			
			echo '<p>';
			$label = isset($options['label']) ? $options['label']:ucfirst($widget);
			//~ d($widget);
			if(access($this->model->relationships(), $widget, 'required'))
				$label .= '*';
				
			$choices = array();
			$all = $relation_model::find();
			foreach($all as $one)
				$choices[$one->id] = $one->__toString();
			
			$this->$widget->label($label);
			echo '<br>';
			$this->$widget->select(
				array(
					'class' => 'styled',
				),
				$choices
			);
			echo '</p>';
		}
		elseif($relationship['type'] == 'hasMany') {
			echo '<p>';
			$label = isset($options['label']) ? $options['label']:ucfirst($relation);
			if(access($this->model->relationships(), $widget, 'required'))
				$label .= '*';
				
			$choices = array();
			$all = $relation_model::find();
			foreach($all as $one)
				$choices[$one->id] = $one->__toString();
			
			$this->$widget->label($label);
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
		elseif($relationship['type'] == 'HMABT') {
			echo '<p>';
			$label = isset($options['label']) ? $options['label']:ucfirst($widget);
			if(access($this->model->relationships(), $widget, 'required'))
				$label .= '*';
				
			$choices = array();
			$all = $relation_model::find();
			foreach($all as $one)
				$choices[$one->id] = $one->__toString();
			
			$this->$widget->label($label);
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
		
		if(access($this->model->getProperty($widget), 'required'))
			$label .= '*';
			
		return $label;
	}
	
	public function select($widget, $options=array(), $choices=array()) {
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
			throw new Exception($widget.' should be a file.');
		
		$specific_file = $this->model->getFile($widget);
		$path = $this->model->getFilePath($widget);
		$optional = !(isset($specific_file['required']) && $specific_file['required']);
				
		if(isset($specific_file['multiple']) && $specific_file['multiple']) {
			if(!$this->model->isNew()):
				$uid = Tools::randstr(10);
				HTML::code_js("
					$(function(){
						multiple_upload('$uid', '".url_for('coxis_'.$this->model->getModelName().'_files_add', array('id' => $this->model->id, 'file' => $widget))."');
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
								<img src="<?php echo URL::to('imagecache/admin_thumb/'.$one_path) ?>" alt=""/>
								<ul>
									<li class="view"><a href="<?php echo URL::to($one_path) ?>" rel="facebox">Voir</a></li>
									<li class="delete"><a href="<?php echo url_for('coxis_'.$this->model->getModelName().'_files_delete', array('id' => $this->model->id, 'pos' => $i, 'file' => $widget)) ?>">Suppr.</a></li>
								</ul>
							</li>
							<?php
							$i++;
							endforeach;
							?>
							</li>
							
						</ul>
						
						<p id="<?php echo $uid ?>">
							<label>Uploader :</label><br />
							<input type="file" id="<?php echo $uid ?>-filesupload" class="filesupload" /><br/>
							<span class="uploadmsg">Taille maximale 3Mb</span>
							<div id="<?php echo $uid ?>-custom-queue"></div>
						</p>
						
					</div>		<!-- .block_content ends -->
					
					<div class="bendl"></div>
					<div class="bendr"></div>
					
				</div>		<!-- .leftcol ends -->
			<?php endif;
		}
		else {
			AdminForm::file($this->$widget, $options);
							
			if(!$this->model->isNew() && $path && file_exists(_WEB_DIR_.'/'.$path)) {
				if($specific_file['type'] == 'image') {
					echo '<p>
						<a href="../'.$path.'" rel="facebox"><img src="../'.ImageCache::src($path, 'admin_thumb').'" alt=""/></a>
					</p>';
				}
				else {
					echo '<p>
						<a href="../'.$path.'">Download file</a>
					</p>';
				}
				
				if($optional && !$this->model->isNew()):
					//~ d(CoxisAdmin::url_for_model($this->model->getModelName(), 'deleteFile', array('file'=>$file, 'id'=>$this->model->id)));
					?>
					<a href="<?php echo CoxisAdmin::url_for_model($this->model->getModelName(), 'deleteFile', array('file'=>$widget, 'id'=>$this->model->id), false) ?>">Delete</a><br/><br/>
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
							($submits!=static::$SAVE ? '<input name="send" type="submit" class="submit long" value="Sauvegarder & Retour" /> ':'').
							($submits!=static::$SEND ? '<input name="stay" type="submit" class="submit long" value="Sauvegarder & Rester" /> ':'').
						'</p>';
		parent::end();
		
		return $this;
	}
}