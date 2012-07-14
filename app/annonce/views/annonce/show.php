			<div id="main">
				<div id="content">
					<a href="annonces" class="back-home"> &lt;retour à la liste de recherche </a>
					<h1>Recherche choeurs</h1>
						
					<hr class="small"/>
					<h1 class="pink"><?php echo $annonce ?></h1>
					<div>
						Ville : <?php echo $annonce->ville ?><br/>
						Région/département : <?php echo $annonce->region ?>
					</div>
					
					<hr style="margin:15px 0 15px 0;"/>
					<h1 class="pink">Description de l'annonce</h1>
					<div>
						<?php echo $annonce->raw('contenu') ?>
					</div>
					
					<hr style="margin:15px 0 15px 0;"/>
					<h1 class="pink">Renseignements de contact</h1>
					<div>
						<?php echo $annonce->nom ?><br/>
						<?php echo $annonce->prenom ?><br/>
						<?php echo $annonce->adresse ?><br/>
						<?php echo $annonce->code_postal ?> <?php echo $annonce->ville ?><br/>
						Téléphone : <?php echo $annonce->telephone ?><br/>
						Portable : <?php echo $annonce->portable ?><br/>
						Mail : <?php echo $annonce->email ?><br/>
						Site internet : <?php echo $annonce->site_web ?><br/>
					</div>
					
					<br/><br/>
					<span class="note">Annonce posté le <?php echo date('d', $annonce->created_at) ?> <?php echo strtolower(Tools::$months[date('F', $annonce->created_at)]) ?> <?php echo date('Y', $annonce->created_at) ?></span>
				</div>
				<?php echo $this->component('default', 'sidebar') ?>
			</div>