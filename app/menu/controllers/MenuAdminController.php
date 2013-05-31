<?php
/**
@Prefix('admin/menus')
*/
class MenuAdminController extends \Coxis\Admin\Libs\Controller\ModelAdminController {
	static $_model = 'menu';
	static $_models = 'menus';

	function __construct() {
		$this->_messages = array(
					'modified'			=>	__('Menu modified with success.'),
					'created'			=>	__('Menu created with success.'),
					'many_deleted'			=>	__('Menus modified with success.'),
					'deleted'			=>	__('Menu deleted with success.'),
					'unexisting'			=>	__('This menu does not exist.'),
				);
		parent::__construct();
	}
	
	public function formConfigure($model) {
		$form = new \Coxis\Admin\Libs\Form\AdminModelForm($model, $this);
		
		return $form;
	}

	/**
	@Route(':id/edit')
	*/
	public function editAction($request) {
		parent::editAction($request);

		$pages = array('Index'=>array(
			'type'	=>	'custom',
			'custom_id'	=>	'pages',
		));
		foreach(Page::all() as $page) {
			$pages[$page->__toString().' ('.$page->name.')'] = array(
				'type'	=>	'item',
				'model'	=>	'page',
				'item_id'	=>	$page->id,
				'item_type'	=>	'page',
			);
		}
		$actualites = array();
		foreach(Actualite::all() as $actualite) {
			$actualites[$actualite->__toString()] = array(
				'type'	=>	'item',
				'model'	=>	'page',
				'item_id'	=>	$actualite->id,
				'item_type'	=>	'actualite',
			);
		}

		$this->adminMenu = array(
			'Pages'	=>	array(
				'childs' => $pages,
			),
			'Actualités'	=>	array(
				'childs' => $actualites,
			),
		);

		function showMenuItem($name, $menuitem, $item) {
			?>
			<option
				value="<?php echo $menuitem['type'] ?>"
				<?php echo isset($menuitem['custom_id']) ? 'data-custom_id="'.$menuitem['custom_id'].'"':'' ?>
				<?php echo isset($menuitem['item_id']) ? 'data-item_id="'.$menuitem['item_id'].'"':'' ?>
				<?php echo isset($menuitem['item_type']) ? 'data-item_type="'.$menuitem['item_type'].'"':'' ?>
				<?php if(
					$item->type == $menuitem['type']
					&& (isset($menuitem['custom_id']) && $item->custom_id == $menuitem['custom_id'] || isset($menuitem['item_id']) && $item->item_id == $menuitem['item_id'])
					): ?>
				selected="selected"
				<?php endif ?>
				>
				<?php echo $name ?>
			</option>
			<?php
		}

		function showItem($item, $adminMenu) {
			?>
			<li class="dd-item">
				<div class="item dd-handle">
					<form data-id="<?php echo $item->id ?>">
					<input type="hidden" name="custom_id" value="<?php echo $item->custom_id ?>">
					<input type="hidden" name="item_id" value="<?php echo $item->item_id ?>">
					<input type="hidden" name="item_type" value="<?php echo $item->item_type ?>">
					<input type="text" name="title" value="<?php echo $item->title ?>" placeholder="<?php echo __('Title') ?>">
					<select name="type">
						<option value="fixed" <?php echo $item->type=='fixed' ? 'selected="selected"':'' ?>><?php echo __('Fixed url') ?></option>
						<option value="none" <?php echo $item->type=='none' ? 'selected="selected"':'' ?>><?php echo __('None') ?></option>
						<?php
						foreach($adminMenu as $name=>$menuitem) {
							if(isset($menuitem['childs'])) {
								?>
								<optgroup label="<?php echo $name ?>">
									<?php
									foreach($menuitem['childs'] as $subname=>$submenuitem):
									showMenuItem($subname, $submenuitem, $item);
									endforeach
									?>
								</optgroup>
								<?php
							}
							else
								showMenuItem($name, $menuitem, $item);
						}
						?>
					</select>
					<input type="text" name="fixed_url" value="<?php echo $item->fixed_url ?>" placeholder="<?php echo __('Fixed url') ?>">

					<input type="button" name="delete" value="<?php echo __('Delete') ?>">
					<input type="button" name="save" value="<?php echo __('Save') ?>">
					</form>
				</div>
				<?php
				$childs = $item->childs;
				if(count($childs)) {
					echo '<ol class="dd-list">';
					foreach($childs as $child)
						showItem($child, $adminMenu);
					echo '</ol>';
				}
				?>
			</li>
			<?php
		}
	}

	/**
	@Route(':id/newItem')
	*/
	public function newItemAction($request) {
		\Memory::set('layout', false);
		$menu = new Menu($request['id']);
		return $menu->items()->create()->id;
	}

	/**
	@Route(':id/save')
	*/
	public function saveItemAction($request) {
		\Memory::set('layout', false);
		$item = MenuItem::load($request['id']);
		$item->save(POST::all());
		$after_id = POST::get('after');
		$item->moveAfter($after_id);
	}

	/**
	@Route(':id/delete')
	*/
	public function deleteItemAction($request) {
		\Memory::set('layout', false);
		$item = new MenuItem($request['id']);
		$item->destroy();
	}
}