<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2><?php echo !$actualite->isNew() ? $actualite:__('New') ?></h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
					<p class="breadcrumb"><a href="<?php echo $this->url_for('index') ?>"><?php echo ucfirst($this::$_models) ?></a> &raquo; 
					<a href="<?php echo !$actualite->isNew() ? $this->url_for('edit', array('id'=>$actualite->id)):$this->url_for('new') ?>">
					<?php echo !$actualite->isNew() ? $actualite:__('New') ?>
					</a></p>
					<?php \Coxis\Core\Flash::showAll() ?>
					
					<?php
					$form->open();
					echo
					$form->title->def().
					$form->published->def().
					$form->content->def().
					$form->image->def().
					$form->meta_title->def().
					$form->meta_description->def().
					$form->meta_keywords->def();
					$form->close();
					?>
					
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->