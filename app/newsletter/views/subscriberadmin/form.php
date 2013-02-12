<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2><?php echo !$subscriber->isNew() ? $subscriber:'New' ?></h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
					<p class="breadcrumb"><a href="<?php echo $this->url_for('index') ?>"><?php echo ucfirst(static::$_models) ?></a> &raquo; 
					<a href="<?php echo !$subscriber->isNew() ? $this->url_for('edit', array('id'=>$subscriber->id)):$this->url_for('new') ?>">
					<?php echo !$subscriber->isNew() ? $subscriber:'New' ?>
					</a></p>
				
					<?php \Flash::showAll() ?>
					
					<?php
					$form->open();
					echo $form->email->def();
					$form->close();
					?>
					
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->