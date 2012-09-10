<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2><?php echo !$recette->isNew() ? $recette:'Nouveau' ?></h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
					<p class="breadcrumb"><a href="<?php echo $this->url_for('index') ?>"><?php echo ucfirst(static::$_models) ?></a> &raquo; 
					<a href="<?php echo !$recette->isNew() ? $this->url_for('edit', array('id'=>$recette->id)):$this->url_for('new') ?>">
					<?php echo !$recette->isNew() ? $recette:'Nouveau' ?>
					</a></p>
					<?php Flash::showAll() ?>
					
					<?php
					$form->start()
						->def('titre')
						->def('complexite')
						->def('budget')
						->def('temps_de_preparation')
						->def('temps_de_cuisson')
						->def('type_de_plat')
						->textarea('ingredients')
						->textarea('preparation')
						->def('animal')
						->def('saison')
						->def('photo_small')
						->def('photo_big')
					->end();
					?>
					
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->