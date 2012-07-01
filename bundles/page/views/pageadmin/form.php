<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2><?php echo !$page->isNew() ? $page:'New' ?></h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
					<p class="breadcrumb"><a href="<?php echo $this->url_for('index') ?>"><?php echo ucfirst(static::$_models) ?></a> &raquo; 
					<a href="<?php echo !$page->isNew() ? $this->url_for('edit', array('id'=>$page->id)):$this->url_for('new') ?>">
					<?php echo !$page->isNew() ? $page:'New' ?>
					</a></p>
				
					<?php Messenger::getInstance()->showAll() ?>
					
					<?php
					$form->start()
					
					->h3('Page')
					->def('title', array('label' => 'Titre'));
					if(_ENV_ == 'dev')
						$form->def('name', array('label' => 'Nom'));
					$form->wysiwyg('content', array('label'	=>	'Contenu', 'config'=>URL::to('bundles/pages/ckeditor_config.js')))
					//~ ->def('comment', array('label'	=>	'Comment'))
					->def('meta_title', array('label'	=>	'Meta Title'))
					->def('meta_description', array('label'	=>	'Description'))
					->def('meta_keywords', array('label'	=>	'Keywords'))
					
					->end();
					?>
					
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->