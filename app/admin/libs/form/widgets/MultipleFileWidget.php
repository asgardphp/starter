<?php
namespace Coxis\App\Admin\Libs\Form\Widgets;

class MultipleFileWidget extends \Coxis\Form\Widgets\HTMLWidget {
	public function render($options=array()) {
		$options = $this->options+$options;
		
		$attrs = array();
		if(isset($options['attrs']))
			$attrs = $options['attrs'];

		$str = HTMLHelper::tag('input', array(
			'type'	=>	'file',
			'name'	=>	$this->name,
			'id'	=>	isset($options['id']) ? $options['id']:null,
		)+$attrs);
		$model = $this->field->form->getModel();
		$name = $this->field->name;		
		$optional = !$model->property($name)->required;
		$path = $model->$name->get();

		if($model->isNew())
			return null;
		$uid = Tools::randstr(10);
		HTML::code_js("
			$(function(){
				multiple_upload('$uid', '".$this->field->form->controller->url_for('addFile', array('id' => $model->id, 'file' => $name), false)."');
			});");
		ob_start();
		?>
		<div class="block">
		
			<div class="block_head">
				<div class="bheadl"></div>
				<div class="bheadr"></div>
				
				<h2><?php echo $name ?></h2>
				<?php
				if(isset($options['nb']))
					echo '<span>'.$options['nb'].'</span>';
				?>
			</div>		<!-- .block_head ends -->
			
			<div class="block_content">
				<script>
				window.parentID = <?php echo $model->id ?>;
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
							<li class="delete"><a href="<?php echo $this->field->form->controller->url_for('deleteFile', array('id' => $model->id, 'pos' => $i, 'file' => $name), false) ?>">Suppr.</a></li>
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

		<?php
		return ob_get_clean();
		/*if($model->isOld() && $model->$name && $model->$name->exists()) {
			$path = $model->$name->get();
			if(!$path)
				return $str;
			if($model->property($name)->filetype == 'image') {
				$str .= '<p>
					<a href="../'.$path.'" rel="facebox"><img src="'.\URL::to(ImageCache::src($path, 'admin_thumb')).'" alt=""/></a>
				</p>';
			}
			else {
				$str .= '<p>
					<a href="../'.$path.'">'.__('Download').'</a>
				</p>';
			}
			
			if($optional)
				$str .= '<a href="'.$this->field->form->controller->url_for('deleteSingleFile', array('file'=>$name, 'id'=>$model->id)).'">'. __('Delete').'</a><br/><br/>';
		}*/

		// return $str;
	}
}
