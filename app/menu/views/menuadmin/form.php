<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2><?php echo !$original->isNew() ? $original:__('New') ?></h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
					<p class="breadcrumb"><a href="<?php echo $this->url_for('index') ?>"><?php echo ucfirst(static::$_models) ?></a> &raquo; 
					<a href="<?php echo !$menu->isNew() ? $this->url_for('edit', array('id'=>$menu->id)):$this->url_for('new') ?>">
					<?php echo !$original->isNew() ? $original:__('New') ?>
					</a></p>
					<?php \Coxis\Core\Flash::showAll() ?>
					
					<?php
					if(_ENV_ == 'dev'):

					$form->open();
					echo $form->name->def();
					?>
					<?php
					$form->close();

					endif
					?>

					<?php if($menu->isOld()):
					if(_ENV_ == 'dev'):
					?>
					<hr>
					<?php endif ?>
					<h3>Items</h3>
					<script src="../menu/jquery.nestable.js"></script>
					<script>
					window.menu_id = <?php echo $menu->id ?>;
					$(function() {
						$('.item select[name="type"]').live('change', function(event) {
							var e = $(this).find(':selected');
							var type = e.val();
							var custom_id = e.attr('data-custom_id');
							var item_id = e.attr('data-item_id');
							var item_type = e.attr('data-item_type');
							if(custom_id)
								e.closest('form').find('*[name="custom_id"]').val(custom_id);
							if(item_id)
								e.closest('form').find('*[name="item_id"]').val(item_id);
							if(item_type)
								e.closest('form').find('*[name="item_type"]').val(item_type);

							if(type == 'fixed')
								$(this).closest('form').find('*[name="fixed_url"]').css('display', 'inline-block');
							else
								$(this).closest('form').find('*[name="fixed_url"]').css('display', 'none');
						});
						$('.item select[name="type"]').change();

						$('#items').nestable({
							'update': function(item) {
								var e = item;
								var id = e.find('form').attr('data-id');

								var after_id = e.prev().find('form').attr('data-id');
								if(typeof after_id == "undefined")
									after_id = 0;

								var parent = e.parent().parent();
								if(parent.attr('id') == 'items')
									var parent_id = 0;
								else
									var parent_id = parent.find('form').first().attr('data-id');
								$.post('menus/'+id+'/save', {'parent': parent_id, 'after': after_id});
							}
						});

						$('.item form *').mousedown(function(event) {
							event.stopPropagation();
						});

						$('.item input, .item select').live('change', function(event) {
							var e = $(this).parent();
							e.find('input[name="save"]').css('color', 'red');
						});

						$('input[name="save"]').live('click', function() {
							var e = $(this).parent();
							var id = e.attr('data-id');
							var formdata = e.serializeArray();
							formdata.push({ name: "menu", value: window.menu_id });
							if(id) {
								$.post('menus/'+id+'/save', formdata, function() {
									e.find('input[name="save"]').css('color', 'black');
								});
							}
							else {
								$.post('menus/new', formdata, function(data) {
									e.find('input[name="save"]').css('color', 'black');
									e.attr('data-id', data);
								});
							}
						});

						$('input[name="delete"]').live('click', function() {
							var e = $(this).parent();
							var id = e.attr('data-id');
							$.get('menus/'+id+'/delete', function() {
								e.parent().parent().remove();
							});
						});

						<?php
						ob_start();
						showItem(new MenuItem, $adminMenu);
						$list = ob_get_clean();
						$list = addcslashes($list, "'");
						$list = str_replace("\r\n", "\\\r\n", $list);
						?>
						var newItem = '<?php echo $list ?>';
						$('#add').click(function() {
							var _newItem = $(newItem);
							$.get('menus/'+window.menu_id+'/newItem', function(id) {
								_newItem.find('form').attr('data-id', id);
								_newItem.find('form *').mousedown(function(event) {
									event.stopPropagation();
								})
								$('#items > ol').append(_newItem);
							});
						});
					});
					</script>
					<style>
					.dd-dragel { position: absolute; pointer-events: none; z-index: 9999; }
					.dd-dragel > .dd-item .dd-handle { margin-top: 0; }
					ol.dd-list {
						list-style-type: none;
					}
					#items button {
						display:none;
					}
					#items {
						position:relative;
					}
					.block .block_content ol.dd-list, .dd-list li, .dd-list div {
						padding:0;
						margin:0;
					}
					.dd-list div {
						display:inline-block;
					}
					.dd-placeholder {
						display: block;
						width:100%;
						position: relative;
						height: 20px;
						margin-bottom:10px !important;
						border:1px dashed grey;
					}
					.block .block_content ol.dd-list {
						position:relative;
						margin-left:30px;
					}
					.block .block_content ol.dd-list li {
						background: none !important;
					}
					ol.dd-list .item {
						-webkit-border-radius: 3px;
						cursor:move;
						background-color:#eee;
						border:1px solid #bbb;
						padding:5px;
						display:block;
						margin-bottom:10px;
					}
					ol.dd-list label {
						cursor:move;
						display:inline-block;
					}
					ol.dd-list input, ol.dd-list select {
						padding:3px;
						-webkit-border-radius: 3px;
						margin-right:20px;
						vertical-align: bottom;
					}

					ol.dd-list input[name="save"], ol.dd-list input[name="delete"] {
						float:right;
					}
					</style>
					
					<div id="items" class="dd">
						<ol class="dd-list">
							<?php foreach($menu->items()->where(array('parent_id'=>0))->get() as $item):
							showItem($item, $adminMenu);
							endforeach;
							?>
							
						</ol>
					</div>

					<input type="button" id="add" value="Add"  class="submit long">
					<?php endif ?>
					
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->