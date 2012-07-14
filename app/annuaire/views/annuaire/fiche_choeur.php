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
					<a href="#" class="back-home"> &lt;retour à l’accueil de l'annuaire </a>
					<h1>Annuaire de recherche chœurs</h1>
					
					<hr class="small"/>
					
					<a href="annuaire/recherche-choeurs?new" class="button">Nouvelle<br/>recherche</a><a href="annuaire/recherche-choeurs" class="button">Retour<br/>à la liste</a>
					<br/>
					
					<h1 class="pink"><?php echo $choeur ?></h1>
					
					<div class="lineheight">
					<div style="float:left; width:340px; position:relative;">
						<span class="title">Informations sur le chœur</span><br/>
						<?php echo $choeur->adresse ?><br/>
						<?php echo $choeur->code_postal ?> <?php echo $choeur->ville ?><br/>
						Téléphone : <?php echo $choeur->telephone ?><br/>
						Mobile : <?php echo $choeur->mobile ?><br/>
						Email : <?php echo $choeur->email ?><br/>
						Site web : <?php echo $choeur->site_web ?><br/>
						<br/>
						
						<span class="title">Type de choeur</span><br/>
						<?php echo implode(', ', $choeur->type_choeurs) ?><br/>
						<br/>
						
						<span class="title">Répertoire</span><br/>
						<?php echo implode(', ', $choeur->style_musical) ?><br/>
						<br/>
						
						<span class="title">Lieu des répétitions</span><br/>
						<?php echo $choeur->lieu_repetition_adresse ?><br/>
						<?php echo $choeur->lieu_repetition_code_postal ?> <?php echo $choeur->lieu_repetition_ville ?><br/>
						<br/>
						
						<span class="title">Jours et heures des répétitions</span><br/>
						<?php echo nl2br($choeur->repetitions_horaires) ?>
					</div>
					<div style="float:left; position:relative; width:360px; margin-left:-50px;">
						<span class="title">Informations sur le chef de chœur</span><br/>
						<?php echo $choeur->responsable_adresse ?><br/>
						<?php echo $choeur->responsable_code_postal ?> <?php echo $choeur->responsable_ville ?><br/>
						Téléphone : <?php echo $choeur->responsable_telephone ?><br/>
						Mobile : <?php echo $choeur->responsable_mobile ?><br/>
						Email : <?php echo $choeur->responsable_email ?><br/>
						<br/>
						
						<span class="title">Conditions d'admission</span><br/>
						<?php echo implode(', ', $choeur->conditions_admission) ?>
					</div>
					</div>
					<br class="clear"/>
					<br/>
					<hr/>
					<br/>
					<?php if($prec): ?>
					<a style="" href="<?php echo $prec ?>">&lt;fiche précédente</a>
					<?php endif ?>
					<?php if($suiv): ?>
					<a style="float:right;" href="<?php echo $suiv ?>">fiche suivante&gt;</a>
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
				<?php $this->component('general', 'sidebar') ?>
			</div>