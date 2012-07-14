			<div id="main">
				<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
				<script src="script.js"></script>
				<script>
				$(function() {
					$('#click').click(function() {
						dialog('Blabla');
					});
				});
				</script>
				<div id="content">
					<a href="annuaire" class="back-home"> &lt;retour à l’accueil de l'annuaire </a>
					<h1>Annuaire de recherche professeurs</h1>
					
					<hr class="small"/>
					
					<a href="annuaire/recherche-professeurs?new" class="button">Nouvelle<br/>recherche</a><a href="annuaire/recherche-professeurs" class="button">Retour<br/>à la liste</a>
					<br/>
					
					<h1 class="pink"><?php echo $professeur ?></h1>
					
					<div class="lineheight">
					<div style="float:left; width:340px; position:relative;">
						<span class="title">Informations sur le professeur</span><br/>
						<?php echo $professeur->adresse ?><br/>
						<?php echo $professeur->code_postal ?> <?php echo $professeur->ville ?><br/>
						Téléphone : <?php echo $professeur->telephone ?><br/>
						Mobile : <?php /*echo $professeur->mobile*/ ?><br/>
						Email : <?php echo $professeur->email ?><br/>
						Site web : <?php echo $professeur->site_web ?><br/>
					</div>
					<div style="float:left; position:relative; width:360px; margin-left:-50px;">
						<span class="title">Informations sur le chef de chœur</span><br/>
						2 bis allées forain francois verdier<br/>
						31000 Toulouse<br/>
						Téléphone : 05 53 68 63 04<br/>
						Mobile : 06 65 09 16 81<br/>
						Email : ghislain-llorca@hotmail.fr<br/>
					</div>
					<br class="clear"/>
					<br/>
						
					<span class="title">Styles musicaux</span><br/>
					<?php echo implode(', ', $professeur->type_choeurs) ?><br/>
					<br/>
					
					<span class="title">Cours particuliers</span><br/>
					<?php echo $professeur->cours_particuliers ?><br/>
					<br/>
					
					<span class="title">Informations répétitions</span><br/>
					<div style="width:400px;">
					<?php echo nl2br($professeur->informations_complementaires) ?>
					</div>
					<br/>
					
					</div>
					<br class="clear"/>
					<br/>
					<hr/>
					<br/>
					<?php if($prec): ?>
					<a style="" href="#">&lt;fiche précédente</a>
					<?php endif ?>
					<?php if($suiv): ?>
					<a style="float:right;" href="#">fiche suivante&gt;</a>
					<?php endif ?>
					<br/>
					<br/>
					<br/>
					<br/>
					<br/>
					
					<div style="color:#4d4d4f; font-size:9px;">
						Conformément à la loi 78-17 du 6 janvier 1978 relevant de la commission informatique et liberté, toute personne justifiant de son identité a un droit d’accès et de rectification aux informations nominatives la concernant dans les fichiers informatisés de L'ARPA Midi-Pyrénées. Ce droit s’exerce auprès de 
						L'ARPA : contact@arpamip.org<br/>
						<br/>
						Les informations sont traitées et réactualisées en permanence par les membres de l'ARPA Midi-Pyrénées sur la base du formulaire rempli par les inscrits. Si une information est erronée ou bien manquante, veuillez vous adresser directement à l'ARPA Midi-Pyrénées.
					</div>
					<br/>
					<br/>
				</div>
				<?php $this->component('default', 'sidebar') ?>
			</div>