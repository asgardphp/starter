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
						->def('question_magasin');
						// ->textarea('magasins')
						// ->textarea('magasins')
						?>
						<p>
						<label>Centrales</label><br/>
						<select name="jeu[centrale]" class="styled" multiple="multiple" style="height:100px">
						<?php
						foreach(Centrale::all() as $centrale):
						?>
						<option value="<?php echo $centrale->id ?>"><?php echo $centrale ?></option>
						<?php
						endforeach;
						?>
						</select>
						<script>
						$('select[name="jeu[centrale]"]').change(function() {
							// console.log('aaa');
							var objData = {centrales: $('select[name="jeu[centrale]"]').val()};
							$.ajax({
								url: "magasins/ajax",
								dataType: "json",
								data: objData,
								type: 'POST',
								success: function (data) {
									// console.log(data);
									$('#mags').html('');
									for(var i=0; i<data.length; i++)
										$('#mags').append('<option value="'+data[i]['id']+'">'+data[i]['nom']+'</option>');
								}
							});
							// $('select[name="magasin[centrale]"]').val();
							// $('select[name="magasin[magasins]"]').append('<option value=""></option>');
						});
						</script>
						</p>
						<p>
						<label>Magasins</label><br/>
						<select id="mags" name="jeu[magasins][]" class="styled" multiple="multiple" style="height:100px">
						<?php
						// d($jeu->magasins);
						foreach($jeu->magasins as $magasin):
						?>
						<option value="<?php echo $magasin->id ?>" selected="selected"><?php echo $magasin ?></option>
						<?php
						endforeach;
						?>
						</select>
						<?php
						$form->def('image_de_fond')
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