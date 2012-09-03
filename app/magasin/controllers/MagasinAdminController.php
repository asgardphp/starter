<?php
/**
@Prefix('admin/magasins')
*/
class MagasinAdminController extends \Coxis\Bundles\Admin\Libs\Controller\ModelAdminController {
	static $_model = 'magasin';
	static $_models = 'magasins';
	
	static $_messages = array(
			'modified'			=>	'Magasin modified with success.',
			'created'			=>	'Magasin created with success.',
			'many_deleted'			=>	'Magasins modified with success.',
			'deleted'			=>	'Magasin deleted with success.',
			'unexisting'			=>	'This magasin does not exist.',
		);
	
	public function formConfigure($model) {
		$form = new \Coxis\Bundles\Admin\Libs\Form\AdminModelForm($model, $this);
		
		return $form;
	}

	/**
	@Route('ajax')
	*/
	public function ajaxAction($request) {
		$mags = Magasin::where(array('centrale_id IN ('.implode(', ', $_POST['centrales']).')'))->get();
		$res = array();
		foreach($mags as $mag)
			$res[] = array('id'=>$mag->id, 'nom'=>$mag->nom);
		Coxis::set('layout', false);
		return json_encode($res);
		// d($mags);
	}
}