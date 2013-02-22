<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2><?php echo !$menu->isNew() ? $menu:__('Nouveau') ?></h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
					<p class="breadcrumb"><a href="<?php echo $this->url_for('index') ?>"><?php echo ucfirst(static::$_models) ?></a> &raquo; 
					<a href="<?php echo !$menu->isNew() ? $this->url_for('edit', array('id'=>$menu->id)):$this->url_for('new') ?>">
					<?php echo !$menu->isNew() ? $menu:__('Nouveau') ?>
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
					<script src="js/query-ui-1.8.16.custom.js"></script>
					<script>
					window.menu_id = <?php echo $menu->id ?>;
					$(function() {
						$("#items ul").sortable({
			                connectWith: "#items ul",
			                placeholder: "ui-state-highlight",
			                handle: ".item",
			                change: function(e, ui) {
			                	$('ul').removeClass('empty');
			                	$('ul:empty').addClass('empty');
			                },
			                update: function(e, ui) {
			                	var id = ui.item.find('form').attr('data-id');

			                	var after_id = ui.item.prev().find('form').attr('data-id');
			                	if(typeof after_id == "undefined")
			                		after_id = 0;

			                	var parent = ui.item.parent().parent();
			                	if(parent.attr('id') == 'items')
			                		var parent_id = 0;
			                	else
			                		var parent_id = parent.find('form').first().attr('data-id');
			                	$.post('menus/'+id+'/save', {'parent': parent_id, 'after': after_id});

			                	
			                }
						});

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

						$('.item input, .item select').live('change', function() {
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
								$('#items > ul').append(_newItem);
							});
						});
					});
					</script>
					<style>
					#items ul, #items div {
						padding:0;
						margin:0;
					}
					#items div {
						display:inline-block;
					}
					#items ul {
						padding-top:15px;
						margin-left:15px;
						padding-bottom:5px;
					}
					#items ul.empty {
						padding-top:0;
						height:10px;
					}
					#items li {
						background: none !important;
					}
					#items .item {
						-webkit-border-radius: 3px;
						cursor:move;
						background-color:#eee;
						border:1px solid #bbb;
						padding:5px;
						display:block;
					}
					#items label {
						cursor:move;
						display:inline-block;
					}
					#items input, #items select {
						padding:3px;
						-webkit-border-radius: 3px;
						margin-right:20px;
						vertical-align: bottom;
					}
					.ui-state-highlight {
						height:30px;
						border:1px dashed grey;
						margin-left:15px;
					}

					#items input[name="save"], #items input[name="delete"] {
						float:right;
					}
					</style>
					
					<div id="items">
						<ul>
							<?php foreach($menu->items()->where(array('parent_id'=>0))->get() as $item):
							showItem($item, $adminMenu);
							endforeach;
							?>
							
						</ul>
					</div>

					<input type="button" id="add" value="Add"  class="submit long">
					<?php endif ?>
					
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->