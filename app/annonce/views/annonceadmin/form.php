<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2><?php echo !$annonce->isNew() ? $annonce:'Nouveau' ?></h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
					<p class="breadcrumb"><a href="<?php echo $this->url_for('index') ?>"><?php echo ucfirst(static::$_models) ?></a> &raquo; 
					<a href="<?php echo !$annonce->isNew() ? $this->url_for('edit', array('id'=>$annonce->id)):$this->url_for('new') ?>">
					<?php echo !$annonce->isNew() ? $annonce:'Nouveau' ?>
					</a></p>
				
					<?php Messenger::getInstance()->showAll() ?>
					
					<?php
					$form->start()
																				->def('intitule'
										)
																									->def('categorie'
										)
																									->def('region'
										)
																									->def('adresse')
																									->def('code_postal')
																									->def('ville')
																									->textarea('contenu'
										)
																									->def('nom'
										)
																									->def('prenom'
										)
																									->def('portable')
																									->def('telephone'
										)
																									->def('email'
										)
																									->def('site_web'
										)
																									//~ ->def('slug')
																									
					->end();
					?>
					
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->