<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2><?php echo !$actualite->isNew() ? $actualite:'Nouveau' ?></h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
					<p class="breadcrumb"><a href="<?php echo $this->url_for('index') ?>"><?php echo ucfirst(static::$_entities) ?></a> &raquo; 
					<a href="<?php echo !$actualite->isNew() ? $this->url_for('edit', array('id'=>$actualite->id)):$this->url_for('new') ?>">
					<?php echo !$actualite->isNew() ? $actualite:'Nouveau' ?>
					</a></p>
					<?php \Coxis\Core\App::get('flash')->showAll() ?>
					
					<?php
					$form->start()
						->def('commentaires')
						->def('titre')
						->def('date')
						->def('lieu')
						->textarea('introduction')
						->wysiwyg('contenu')
						->def('image')
						//~ ->def('slug')								
					->end();
					?>
					
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->