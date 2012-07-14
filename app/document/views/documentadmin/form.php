<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2><?php echo !$document->isNew() ? $document:'Nouveau' ?></h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
					<p class="breadcrumb"><a href="<?php echo $this->url_for('index') ?>"><?php echo ucfirst(static::$_models) ?></a> &raquo; 
					<a href="<?php echo !$document->isNew() ? $this->url_for('edit', array('id'=>$document->id)):$this->url_for('new') ?>">
					<?php echo !$document->isNew() ? $document:'Nouveau' ?>
					</a></p>
				
					<?php Messenger::getInstance()->showAll() ?>
					
					<?php
					$form->start()
																				->def('titre'
										)
																									->textarea('description'
										)
																																													->def('document')
															
					->end();
					?>
					
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->