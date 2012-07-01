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
				<div id="content">
					<a href="annuaire" class="back-home"> &lt;retour à l’accueil de l'annuaire </a>
					<h1>Inscription à l'annuaire des chœurs</h1>
					
					<span style="font-size:18px; font-family:Arial; color:#636466; font-style:italic;">Remplissez le formulaire !</span>
					<hr class="small"/>
					
					<br/>
					<form class="cols" method="post" action="annuaire/depot-choeur/submit">
					<?php $form->_csrf_token->hidden() ?>
					<div style="float:left; width:340px; position:relative;">
						<div class="list">
							<span class="title">Informations sur le chœur*</span><br/><br/>
							<label>Nom* :</label>
							<?php $form->nom->input() ?>
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
							<br class="clear"/>
							<br/>
							
							<span class="title">Lieu des répétitions*</span><br/><br/>
							<label>Adresse :</label>
							<?php $form->lieu_repetition_adresse->input() ?>
							<label>Ville :</label>
							<?php $form->lieu_repetition_ville->input() ?>
							<label>Code postal :</label>
							<?php $form->lieu_repetition_code_postal->input() ?>
							<br class="clear"/>
							<br/>
							
							<span class="title">Jours et horaires des répétitions</span><br/><br/>
							<?php $form->repetitions_horaires->textarea(array('attrs'=>array('style' => 'height:100px; width:260px;'))) ?>
						</div>
						<br/>
						
						<span class="title">Style musical* </span>  <span class="pink">(plusieurs réponses possibles)</span>
						<div class="table">
							<?php
							$boxes = $form->style_musical->checkboxes();
							while($box = $boxes->next()) {
								$box->input()->label();
								echo '<br/>';
							}
							?>
						</div>
					</div>
					<div style="float:left; position:relative; width:360px; margin-left:-50px;">
						<div class="list">
							<span class="title">Informations sur le responsable artistique*</span><br/><br/>
							<label>Nom* :</label>
							<?php $form->responsable_nom->input() ?>
							<label>Prénom* :</label>
							<?php $form->responsable_prenom->input() ?>
							<label>Téléphone* :</label>
							<?php $form->responsable_telephone->input() ?>
							<label>E-mail* :</label>
							<?php $form->responsable_email->input() ?>
						</div>
						<br class="clear"/><br/>
						
						<span class="title">Conditions d’admission* </span>  <span class="pink">(plusieurs réponses possibles)</span>
						<div class="table">
							<?php
							$boxes = $form->conditions_admission->checkboxes();
							while($box = $boxes->next()) {
								$box->input()->label();
								echo '<br/>';
							}
							?>
						</div>
						<br class="clear"/>
						<br/>
						
						<span class="title">Type de chœurs* </span>  <span class="pink">(plusieurs réponses possibles)</span>
						<div class="table">
							<?php
							$boxes = $form->type_choeurs->checkboxes();
							while($box = $boxes->next()) {
								$box->input()->label();
								echo '<br/>';
							}
							?>
						</div>
					</div>
					<input type="image" src="images/annuaire/validez.png" alt="Validez l'inscription" style="float: right;margin-top: 20px; margin-right:130px;"/>
					<br class="clear"/>
					</form>
				</div>
				<?php $this->component('general', 'sidebar') ?>
			</div>