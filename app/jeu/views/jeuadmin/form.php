<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2><?php echo !$jeu->isNew() ? $jeu:'Nouveau' ?></h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
					<p class="breadcrumb"><a href="<?php echo $this->url_for('index') ?>"><?php echo ucfirst(static::$_models) ?></a> &raquo; 
					<a href="<?php echo !$jeu->isNew() ? $this->url_for('edit', array('id'=>$jeu->id)):$this->url_for('new') ?>">
					<?php echo !$jeu->isNew() ? $jeu:'Nouveau' ?>
					</a></p>
					<?php Flash::showAll() ?>
					
					<?php
					$form->start()
						->def('titre')
						->def('couleur_de_fond')
						->def('adresse')
						->def('date_debut')
						->def('date_fin')
						->def('question')
						->textarea('reponses')
						->def('bonne_reponse')
						->textarea('codes_barres')
						->def('lien_optionnelle', array('label'=>'Lien optionnel'))
						->def('question_magasin')
						->textarea('magasins')
						->def('image_de_fond')
						->def('valider')
						->def('reglement_du_jeu')
						->def('pdf')
						->def('image_optionnelle')
					->end();
					?>
					
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->