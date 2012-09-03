<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2><?php echo !$participant->isNew() ? $participant:'Nouveau' ?></h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
					<p class="breadcrumb"><a href="<?php echo $this->url_for('index') ?>"><?php echo ucfirst(static::$_models) ?></a> &raquo; 
					<a href="<?php echo !$participant->isNew() ? $this->url_for('edit', array('id'=>$participant->id)):$this->url_for('new') ?>">
					<?php echo !$participant->isNew() ? $participant:'Nouveau' ?>
					</a></p>
					<?php Flash::showAll() ?>
					
					<?php
					$form->start()
						->def('civilite')
						->def('nom')
						->def('prenom')
						->def('adresse')
						->def('code_postal')
						->def('ville')
						->def('pays')
						->def('email')
						->def('telephone')
						->def('reponse')
						->def('magasin')
					->end();
					?>
					
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->