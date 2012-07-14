			<div id="main">
				<script>
				$(function() {
					$('#content form').ajaxForm({
						statusCode: {
							200: function(responseText, statusText, xhr, jQueryform) {
								dialog('Annonce en validation<br/><br/>Votre annonce a bien été prise en compte et est en cours de validation.');
							},
							500: function(jqXHR, textStatus, errorThrown) {
								alert(jqXHR.responseText);
							}
						}
					});
				});
				</script>
				<div id="content">
					<a href="annonces" class="back-home"> &lt;retour aux petites annonces</a>
					<h1>Déposer une petite annonce</h1>
					
					<hr class="small"/>
					
					<style>
					#content label {
						display:inline-block;
						width:110px;
					}
					#content input, #content select {
						width:150px;
					}
					#content form {
						line-height:20px;
					}
					</style>
					<form action="annonces/depot/submit" method="post">
					<label><strong>Catégorie* :</strong></label> 
					<?php $form->categorie->select() ?>
					<br/>
					<label><strong>Intitulé* :</strong></label> 
					<?php $form->intitule->input() ?>
					<br/>
					<label><strong>Région/dép.* :</strong></label> 
					<?php $form->region->select() ?>
					<br/>
					<label><strong>Ville* :</strong></label> 
					<?php $form->ville->input() ?>
					<br/>
					<label style="width:auto;"><strong>Contenu de l'annonce* :</strong></label> <br/>
					<?php $form->contenu->textarea(array('attrs'=>array('style'=>'height:150px; width:400px;'))) ?>
					<span style="font-size:11px; font-style:italic; font-family:Arial; color:#c2c3c5; display:block; text-align:right; width:400px;">600 caractères max.</span>
					
					<strong>Renseignement/inscription auprès de :</strong><br/>
					<label>Nom/Structure* :</label> <?php $form->nom->input() ?><br/>
					<label>Prénom* :</label> <?php $form->prenom->input() ?><br/>
					<label>Téléphone* :</label> <?php $form->telephone->input() ?><br/>
					<label>E-mail* :</label> <?php $form->email->input() ?><br/>
					<label>Site web* :</label> <?php $form->site_web->input() ?><br/>
					
					<input type="image" src="images/annonces/valider.png" style="margin:20px 0 0 120px;"/>
					</form>
				</div>
				<?php echo $this->component('general', 'sidebar') ?>
			</div>