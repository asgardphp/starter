			<div id="main">
				<script>
				$(function() {
					$('#content form').ajaxForm({
						statusCode: {
							200: function(responseText, statusText, xhr, jQueryform) {
								dialog('Votre inscription a bien été prise en compte.<br/><br/>Elle sera traitée dans les plus brefs délais par l\'ARPA pour sa mise en ligne.');
							},
							500: function(jqXHR, textStatus, errorThrown) {
								alert(jqXHR.responseText);
							}
						}
					});
				});
				</script>
				<style>
				form .table label {
					display:inline-block;
					width:140px;
				}
				</style>
				<div id="content">
					<a href="annuaire" class="back-home"> &lt;retour à l’accueil de l'annuaire </a>
					<h1>Inscription à l'annuaire des Professeurs</h1>
					
					<span style="font-size:18px; font-family:Arial; color:#636466; font-style:italic;">Remplissez le formulaire !</span>
					<hr class="small"/>
					
					<br/>
					<form class="cols" method="post" action="annuaire/depot-professeur/submit">
					<?php $form->_csrf_token->hidden() ?>
					<div style="float:left; width:340px; position:relative;">
						<div class="list">
							<span class="title">Informations générales</span><br/><br/>
							<label>Nom* :</label>
							<?php $form->nom->input() ?>
							<label>Prénoms* :</label>
							<?php $form->prenoms->input() ?>
							<label>Région/dept.* :</label>
							<?php $form->region->select(array(), Arpa::$regions) ?>
							<label>Adresse* :</label>
							<?php $form->adresse->input() ?>
							<label>Ville* :</label>
							<?php $form->ville->input() ?>
							<label>Code postal* :</label>
							<?php $form->code_postal->input() ?>
							<label>Téléphone* :</label>
							<?php $form->telephone->input() ?>
							<label>E-mail* :</label>
							<?php $form->email->input() ?>
							<label>Site-web :</label>
							<?php $form->site_web->input() ?>
						</div>
						<br class="clear"/>
						<br/>
						
						<span class="title">Cours particuliers* :</span> 
						<?php
						$radios = $form->cours_particuliers->radio();
						while($radio = $radios->next()) {
							$radio->input();
							$radio->label();
						}
						?>
						<br/><br/>
						
						<span class="title">Style musical* </span>  <span class="pink">(plusieurs réponses possibles)</span>
						<div class="table">
							<?php
							$boxes = $form->type_choeurs->checkboxes();
							$i=0;
							while($box = $boxes->next()) {
								$box->input()->label();
								if($i++%2 == 1)
									echo '<br/>';
							}
							?>
						</div>
					</div>
					<div style="float:left; width:300px; position:relative; margin-top:300px;">
						<span class="title">Informations complémentaires</span><br/><br/>
							<?php $form->informations_complementaires->textarea(array('attrs'=>array('style' => 'height:200px; width:300px;'))) ?>
						<span style="font-size:11px; font-style:italic; font-family:Arial; color:#c2c3c5; display:block; text-align:right;">600 caractères max.</span>
					</div>
					<input type="image" src="images/annuaire/validez.png" alt="Validez l'inscription" style="float: right;margin-top: 20px;"/>
					<br class="clear"/>
					</form>
				</div>
				<?php $this->component('general', 'sidebar') ?>
			</div>