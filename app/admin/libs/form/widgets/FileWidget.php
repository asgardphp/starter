<?php
namespace Coxis\App\Admin\Libs\Form\Widgets;

class FileWidget extends \Coxis\Form\Widgets\HTMLWidget {
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

		if($model->isOld() && $model->$name && $model->$name->exists()) {
			$path = $model->$name->get();
			if(!$path || !$model->$name->saved)
				return $str;
			if($model->property($name)->filetype == 'image') {
				$str .= '<p>
					<a target="_blank" href="../'.$path.'" rel="facebox"><img src="'.\URL::to(ImageCache::src($path, 'admin_thumb')).'" alt=""/></a>
				</p>';
			}
			else {
				$str .= '<p>
					<a target="_blank" href="../'.$path.'">'.__('Download').'</a>
				</p>';
			}
			
			if($optional)
				$str .= '<a href="'.$this->field->form->controller->url_for('deleteSingleFile', array('file'=>$name, 'id'=>$model->id)).'">'. __('Delete').'</a><br/><br/>';
		}

		return $str;
	}
}