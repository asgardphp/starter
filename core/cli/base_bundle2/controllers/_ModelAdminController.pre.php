<%
/**
@Prefix('admin/<?php echo $bundle['model']['meta']['plural'] ?>')
*/
class <?php echo ucfirst($bundle['model']['meta']['name']) ?>AdminController extends \Coxis\Bundles\Admin\Libs\Controller\ModelAdminController {
	static $_model = '<?php echo ucfirst($bundle['model']['meta']['name']) ?>';
	static $_models = '<?php echo $bundle['model']['meta']['plural'] ?>';

	public static function _autoload() {
		static::$_messages = array(
		<?php foreach($bundle['coxis_admin']['messages'] as $k=>$v): ?>
			'<?php echo $k ?>'			=>	__('<?php echo $v ?>'),
		<?php endforeach ?>
		);
	}
	
	public function formConfigure($model) {
		$form = new \Coxis\Bundles\Admin\Libs\Form\AdminModelForm($model, $this);
		
		return $form;
	}
}