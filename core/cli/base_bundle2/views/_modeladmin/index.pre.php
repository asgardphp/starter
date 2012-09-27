			<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2><a href="<% echo $this->url_for('index') %>"><% echo ucfirst(static::$_models) %></a></h2>
					<ul>
						<li><a href="<% echo $this->url_for('new') %>">Ajouter</a></li>
					</ul>
					<%
					$searchForm->start('', 'get')
					->search->input(array(
						'class'	=>	'text',
						'placeholder'	=>	'Search',
					));
					$searchForm->end();
					%>
				</div>	
				
				<div class="block_content">
				<!-- 	<div class="block small left" style="width:100%">
						<div class="block_head">
							<div class="bheadl"></div>
							<div class="bheadr"></div>
							<h2>Liste</h2>	
						</div>	
						<div class="block_content"> -->
						
							<% \Coxis\Core\Flash::showAll() %>
						
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
										<%
										foreach($<?php echo $bundle['model']['meta']['plural'] ?> as $<?php echo $bundle['model']['meta']['name'] ?>) { %>								
											<tr>
												<td><input type="checkbox" name="id[]" value="<% echo $<?php echo $bundle['model']['meta']['name'] ?>->id %>" /></td>
												<td><% echo $<?php echo $bundle['model']['meta']['name'] ?>->created_at %></td>
												<td><a href="<% echo $this->url_for('edit', array('id'=>$<?php echo $bundle['model']['meta']['name'] ?>->id)) %>"><% echo $<?php echo $bundle['model']['meta']['name'] ?> %></a></td>
												<td class="actions">
													<% Event::trigger_show('coxis_<?php echo ucfirst($bundle['model']['meta']['name']) ?>_actions', $<?php echo $bundle['model']['meta']['name'] ?>) %>
													<a class="delete" href="<% echo $this->url_for('delete', array('id'=>$<?php echo $bundle['model']['meta']['name'] ?>->id)) %>">Supprimer</a>
												</td>
											</tr>
										<% } %>
									</tbody>
									
								</table>
								<div class="tableactions">
									<select name="action">
										<option>Actions</option>
										<option value="delete">Supprimer</option>
									</select>
									<input type="submit" class="submit tiny" value="Appliquer" />
								</div>		
								
								<%
								if(isset($paginator))
								if($paginator->getPages()>1) {
								%>
								<div class="pagination right">
									<% $paginator->show() %>
								</div>
								<%
								}
								%>
								
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
							<%
							%>
							
						</div>		
						
						<div class="bendl"></div>
						<div class="bendr"></div>
					</div>-->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		