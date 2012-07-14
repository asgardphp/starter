<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2><?php echo !$professeur->isNew() ? $professeur:'Nouveau' ?></h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
					<p class="breadcrumb"><a href="<?php echo $this->url_for('index') ?>"><?php echo ucfirst(static::$_models) ?></a> &raquo; 
					<a href="<?php echo !$professeur->isNew() ? $this->url_for('edit', array('id'=>$professeur->id)):$this->url_for('new') ?>">
					<?php echo !$professeur->isNew() ? $professeur:'Nouveau' ?>
					</a></p>
				
					<?php Messenger::getInstance()->showAll() ?>
					
					<?php
					$form->start()
																				->def('nom'
										)
																									->def('prenoms'
										)
																									->def('region'
										)
																									->def('adresse'
										)
																									->def('ville'
										)
																									->def('code_postal'
										)
																									->def('telephone'
										)
																									->def('email'
										)
																									->def('site_web'
										)
																									->def('cours_particuliers'
										)
																									->def('type_choeurs'
										)
																									->textarea('informations_complementaires'
										)
																									//~ ->def('slug')
																									
					->end();
					?>
					
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->