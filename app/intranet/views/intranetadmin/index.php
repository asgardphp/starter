			<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2><a href="<?php echo $this->url_for('index') ?>"><?php echo ucfirst($this::$_models) ?></a></h2>
					<ul>
						<li><a href="<?php echo $this->url_for('new') ?>"><?php echo __('Add') ?></a></li>
					</ul>
					<?php
					$searchForm->open();
					echo $searchForm->search->def(array(
						'attrs'	=>	array(
							'class'	=>	'text',
							'placeholder'	=>	'Search',
						),
					));
					$searchForm->close();
					?>
				</div>	
				
				<div class="block_content">
				<!-- 	<div class="block small left" style="width:100%">
						<div class="block_head">
							<div class="bheadl"></div>
							<div class="bheadr"></div>
							<h2>Liste</h2>	
						</div>	
						<div class="block_content"> -->
						
							<?php \Coxis\Core\Flash::showAll() ?>
						
							<?php if(sizeof($users) == 0): ?>
							<div style="text-align:center; font-weight:bold"><?php echo __('Aucun élément') ?></div>
							<?php else: ?>
							<form action="" method="post">
								<table cellpadding="0" cellspacing="0" width="100%" class="sortable">
								
									<thead>
										<tr>
											<th width="10"><input type="checkbox" class="check_all" /></th>
											<th><?php echo __('Created at') ?></th>
											<th><?php echo __('Title') ?></th>
											<td>&nbsp;</td>
										</tr>
									</thead>
									
									<tbody>
										<?php
										foreach($users as $user) { ?>								
											<tr>
												<td><input type="checkbox" name="id[]" value="<?php echo $user->id ?>" /></td>
												<td><?php echo $user->created_at ?></td>
												<td><a href="<?php echo $this->url_for('edit', array('id'=>$user->id)) ?>"><?php echo $user ?></a></td>
												<td class="actions">
													<?php \Hook::trigger('coxis_user_actions', $user, null, true) ?>
													<a class="delete" href="<?php echo $this->url_for('delete', array('id'=>$user->id)) ?>"><?php echo __('Delete') ?></a>
												</td>
											</tr>
										<?php } ?>
									</tbody>
									
								</table>
								<div class="tableactions">
									<select name="action">
										<option>Actions</option>
										<?php foreach($globalactions as $action): ?>
										<option value="<?php echo $action['value'] ?>"><?php echo $action['text'] ?></option>
										<?php endforeach ?>
									</select>
									<input type="submit" class="submit tiny" value="<?php echo __('Apply') ?>" />
								</div>		
								
								<?php if(isset($paginator) && $paginator->getPages()>1): ?>
								<div class="pagination right">
									<?php $paginator->show() ?>
								</div>
								<?php endif ?>
								
							</form>
							<?php endif ?>
						</div>		<!-- .block_content ends -->
						<!-- <div class="bendl"></div>
						<div class="bendr"></div>
					</div> -->
					<!--<div class="block small right" style="width:19%">
						<div class="block_head">
							<div class="bheadl"></div>
							<div class="bheadr"></div>
							
							<h2>Filtres</h2>
						</div>	
						<div class="block_content">
							<?php
							?>
							
						</div>		
						
						<div class="bendl"></div>
						<div class="bendr"></div>
					</div>-->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		