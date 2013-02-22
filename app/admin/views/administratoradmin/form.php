			<div class="block">
			
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					
					<h2><?php echo !$administrator->isNew() ? $administrator:__('New') ?></h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
				
					<p class="breadcrumb"><a href="administrators"><?php echo __('Administrators') ?></a> &raquo; 
					<a href="<?php echo !$administrator->isNew() ? 'administrators/'.$administrator->id.'/edit':'administrators/new' ?>">
					<?php echo !$administrator->isNew() ? $administrator:__('New') ?>
					</a></p>
				
					<?php Flash::showSuccess() ?>
					<?php $form->showErrors() ?>
					
					<?php
					$form->open();
					echo
						$form->username->def(array('label'	=>	__('Username'))).
						$form->password->password(array('label'	=>	__('password')));
					$form->close();
					?>
					
				</div>		<!-- .block_content ends -->
				
				<div class="bendl"></div>
				<div class="bendr"></div>
					
			</div>		<!-- .block ends -->