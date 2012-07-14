<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2><?php echo !$choeur->isNew() ? $choeur:'Nouveau' ?></h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
					<p class="breadcrumb"><a href="<?php echo $this->url_for('index') ?>"><?php echo ucfirst(static::$_models) ?></a> &raquo; 
					<a href="<?php echo !$choeur->isNew() ? $this->url_for('edit', array('id'=>$choeur->id)):$this->url_for('new') ?>">
					<?php echo !$choeur->isNew() ? $choeur:'Nouveau' ?>
					</a></p>
				
					<?php Messenger::getInstance()->showAll() ?>
					
					<?php
					$form->start()
																				->def('nom'
										)
																									->def('region'
										)
																									->def('adresse'
										)
																									->def('ville'
										)
																									->def('code_postal'
										)
																									->def('telephone')
																									->def('mobile')
																									->def('email'
										)
																									->def('site_web'
										)
																									->def('lieu_repetition_adresse'
										)
																									->def('lieu_repetition_ville'
										)
																									->def('lieu_repetition_code_postal'
										)
																									->textarea('repetitions_horaires'
										)
																									->def('style_musical'
										)
																									->def('responsable_nom'
										)
																									->def('responsable_prenom'
										)
																									->def('responsable_telephone'
										)
																									->def('responsable_email'
										)
																									->def('conditions_admission'
										)
																									->def('type_choeurs'
										)
																									//~ ->def('slug')
																									
					->end();
					?>
					
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->