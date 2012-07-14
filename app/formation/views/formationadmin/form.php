<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2><?php echo !$formation->isNew() ? $formation:'Nouveau' ?></h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
					<p class="breadcrumb"><a href="<?php echo $this->url_for('index') ?>"><?php echo ucfirst(static::$_models) ?></a> &raquo; 
					<a href="<?php echo !$formation->isNew() ? $this->url_for('edit', array('id'=>$formation->id)):$this->url_for('new') ?>">
					<?php echo !$formation->isNew() ? $formation:'Nouveau' ?>
					</a></p>
				
					<?php Messenger::getInstance()->showAll() ?>
					
					<?php
					$form->start()
																				->def('titre'
										)
																									->def('date'
										)
																									->def('lieu'
										)
																									->textarea('introduction')
																									->wysiwyg('contenu')
																									->def('meta_title')
																									->def('meta_description')
																									->def('meta_keywords')
																									//~ ->def('slug')
																																								->def('image')
															
					->end();
					?>
					
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->