			<div class="block">
			
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					
					<h2><?php echo !$administrator->isNew() ? $administrator:'New' ?></h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
				
					<p class="breadcrumb"><a href="administrators">Administrateurs</a> &raquo; 
					<a href="<?php echo !$administrator->isNew() ? 'administrators/'.$administrator->id.'/edit':'administrators/new' ?>">
					<?php echo !$administrator->isNew() ? $administrator:'New' ?>
					</a></p>
				
					<?php Messenger::getInstance()->showAll() ?>
					
					<?php
					$form->start();
					$form->input('username', array('label'	=>	'Username'));
					$form->password('password', array('label'	=>	'Password', 'nb' => 'Laissez vide pour ne pas modifier le mot de passe'));
					$form->end();
					?>
					
				</div>		<!-- .block_content ends -->
				
				<div class="bendl"></div>
				<div class="bendr"></div>
					
			</div>		<!-- .block ends -->