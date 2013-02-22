<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2><?php echo !$question->isNew() ? $question:__('New') ?></h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
					<p class="breadcrumb"><a href="<?php echo $this->url_for('index') ?>"><?php echo ucfirst(static::$_models) ?></a> &raquo; 
					<a href="<?php echo !$question->isNew() ? $this->url_for('edit', array('id'=>$question->id)):$this->url_for('new') ?>">
					<?php echo !$question->isNew() ? $question:__('New') ?>
					</a></p>
					<?php \Coxis\Core\Flash::showAll() ?>
					
					<?php
					$form->open();
					echo $form->question->def();
					echo $form->answer->wysiwyg();
					$form->close();
					?>
					
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->