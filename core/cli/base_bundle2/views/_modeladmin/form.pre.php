<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2><% echo !$<?php echo $bundle['model']['meta']['name'] ?>->isNew() ? $<?php echo $bundle['model']['meta']['name'] ?>:'Nouveau' %></h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
					<p class="breadcrumb"><a href="<% echo $this->url_for('index') %>"><% echo ucfirst(static::$_models) %></a> &raquo; 
					<a href="<% echo !$<?php echo $bundle['model']['meta']['name'] ?>->isNew() ? $this->url_for('edit', array('id'=>$<?php echo $bundle['model']['meta']['name'] ?>->id)):$this->url_for('new') %>">
					<% echo !$<?php echo $bundle['model']['meta']['name'] ?>->isNew() ? $<?php echo $bundle['model']['meta']['name'] ?>:'Nouveau' %>
					</a></p>
					<% Flash::showAll() %>
					
					<%
					$form->start()<?php
					$model = $bundle['model']['meta']['name'];
					foreach($bundle['coxis_admin']['form']['display'] as $field):
						if($model::hasProperty($field) && $model::property($field)->editable === false) continue;
						if(isset($bundle['coxis_admin']['form']['fields'][$field])):
							$params = $bundle['coxis_admin']['form']['fields'][$field];
							$type = isset($params['type']) ? $params['type']:'def';
							unset($params['type']);
						?>

						-><?php echo $type ?>('<?php echo $field ?>'<?php 
							if($params):
								 ?>, array(
								<?php foreach($params as $k=>$v): ?>
									'<?php echo $k ?>'	=>	<?php echo outputPHP($v) ?>,
								<?php endforeach ?>
								)
							<?php endif ?>)<?php 
						endif;
					endforeach ?>

					->end();
					%>
					
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->