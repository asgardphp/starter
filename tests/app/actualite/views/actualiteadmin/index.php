			<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2><a href="<?php echo $this->url_for('index') ?>"><?php echo ucfirst(static::$_entities) ?></a></h2>
					<ul>
						<li><a href="<?php echo $this->url_for('new') ?>">Ajouter</a></li>
					</ul>
					<?php
					$searchForm->start('', 'get')
					->search->input(array(
						'class'	=>	'text',
						'placeholder'	=>	'Search',
					));
					$searchForm->end();
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
						
							<?php \Coxis\Core\Coxis\Core\App::get('flash')->showAll() ?>
						
							<form action="" method="post">
								<table cellpadding="0" cellspacing="0" width="100%" class="sortable">
								
									<thead>
										<tr>
											<th width="10"><input type="checkbox" class="check_all" /></th>
											<th>Créée le</th>
											<th>Titre</th>
											<td>&nbsp;</td>
										</tr>
									</thead>
									
									<tbody>
										<?php foreach($actualites as $actualite): ?>								
											<tr>
												<td><input type="checkbox" name="id[]" value="<?php echo $actualite->id ?>" /></td>
												<td><?php echo $actualite->created_at ?></td>
												<td><a href="<?php echo $this->url_for('edit', array('id'=>$actualite->id)) ?>"><?php echo $actualite ?></a></td>
												<td class="actions">
													<?php \Hook::trigger('coxis_actualite_actions', $actualite) ?>
													<a class="delete" href="<?php echo $this->url_for('delete', array('id'=>$actualite->id)) ?>">Supprimer</a>
												</td>
											</tr>
										<?php endforeach ?>
									</tbody>
									
								</table>
								<div class="tableactions">
									<select name="action">
										<option>Actions</option>
										<option value="delete">Supprimer</option>
									</select>
									<input type="submit" class="submit tiny" value="Appliquer" />
								</div>		
								
								<?php
								if(isset($paginator))
								if($paginator->getPages()>1) {
								?>
								<div class="pagination right">
									<?php $paginator->show() ?>
								</div>
								<?php
								}
								?>
								
							</form>
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