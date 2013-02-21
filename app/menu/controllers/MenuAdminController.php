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
			$pages[$page->__toString()] = array(
				'type'	=>	'item',
				'model'	=>	'page',
				'item'	=>	$page->id
			);
		}

		$this->adminMenu = array(
			'Pages'	=>	array(
				'childs' => $pages,
			),
		);

		function showMenuItem($name, $menuitem, $item) {
			?>
			<option
				value="<?php echo $menuitem['type'] ?>"
				<?php echo isset($menuitem['custom_id']) ? 'data-custom_id="'.$menuitem['custom_id'].'"':'' ?>
				<?php echo isset($menuitem['item']) ? 'data-item="'.$menuitem['item'].'"':'' ?>
				<?php if(
					$item->type == $menuitem['type']
					&& (isset($menuitem['custom_id']) && $item->custom_id == $menuitem['custom_id'] || isset($menuitem['item']) && $item->item_id == $menuitem['item'])
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
			<li>
				<div class="item">
					<form data-id="<?php echo $item->id ?>">
					<input type="hidden" name="custom_id" value="<?php echo $item->custom_id ?>">
					<input type="hidden" name="item" value="<?php echo $item->item ?>">
					<input type="text" name="title" value="<?php echo $item->title ?>" placeholder="Title">
					<select name="type">
						<option value="fixed" <?php echo $item->type=='fixed' ? 'selected="selected"':'' ?>>Fixed url</option>
						<option value="none" <?php echo $item->type=='none' ? 'selected="selected"':'' ?>>None</option>
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
					<input type="text" name="fixed_url" value="<?php echo $item->fixed_url ?>" placeholder="Fixed url">

					<input type="button" name="delete" value="Delete">
					<input type="button" name="save" value="Save">
					</form>
				</div>
				<?php
				$childs = $item->childs;
				if(count($childs) == 0)
					echo '<ul class="empty"></ul>';
				else {
					echo '<ul>';
					foreach($childs as $child)
						showItem($child, $adminMenu);
					echo '</ul>';
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