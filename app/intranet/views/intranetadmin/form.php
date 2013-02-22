<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2><?php echo !$user->isNew() ? $user:__('New') ?></h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
					<p class="breadcrumb"><a href="<?php echo $this->url_for('index') ?>"><?php echo ucfirst($this::$_models) ?></a> &raquo; 
					<a href="<?php echo !$user->isNew() ? $this->url_for('edit', array('id'=>$user->id)):$this->url_for('new') ?>">
					<?php echo !$user->isNew() ? $user:__('New') ?>
					</a></p>
					<?php \Coxis\Core\Flash::showAll() ?>
					
					<?php
					$form->open();
					echo
					$form->username->def().
					$form->password->password().
					$form->email->def();
					$form->close();
					?>
					
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->