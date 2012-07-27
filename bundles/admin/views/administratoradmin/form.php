			<div class="block">
			
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					
					<h2><?php echo !$administrator->isNew() ? $administrator:'Nouveau' ?></h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
				
					<p class="breadcrumb"><a href="administrators"><?php echo __('Administrators') ?></a> &raquo; 
					<a href="<?php echo !$administrator->isNew() ? 'administrators/'.$administrator->id.'/edit':'administrators/new' ?>">
					<?php echo !$administrator->isNew() ? $administrator:'Nouveau' ?>
					</a></p>
				
					<?php Flash::showAll() ?>
					
					<?php
					$form->start();
					$form->input('username', array('label'	=>	__('Username')));
					$form->password('password', array('label'	=>	__('Password')));
					$form->end();
					?>
					
				</div>		<!-- .block_content ends -->
				
				<div class="bendl"></div>
				<div class="bendr"></div>
					
			</div>		<!-- .block ends -->