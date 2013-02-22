<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2><?php echo !$page->isNew() ? $page:__('New') ?></h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
					<p class="breadcrumb"><a href="<?php echo $this->url_for('index') ?>"><?php echo ucfirst(static::$_models) ?></a> &raquo; 
					<a href="<?php echo !$page->isNew() ? $this->url_for('edit', array('id'=>$page->id)):$this->url_for('new') ?>">
					<?php echo !$page->isNew() ? $page:__('New') ?>
					</a></p>
					<?php \Coxis\Core\Flash::showAll() ?>
					
					<?php
					$form->open();
					if(_ENV_ == 'dev')
						echo $form->name->def();
					echo $form->title->def();
					echo $form->published->def();
					echo $form->url->def();
					echo $form->content->wysiwyg(array('config'=>\URL::to('page/ckeditor_config.js')));
					echo $form->meta_title->def();
					echo $form->meta_description->def();
					echo $form->meta_keywords->def();
					$form->close();
					?>
					
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->