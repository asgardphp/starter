			<div id="main">
				<div id="content">
					<a href="annuaire" class="back-home"> &lt;retour à l’accueil de l'annuaire </a>
					<h1>Rechercher un professeur</h1>
					
					<hr class="small"/>
					<br/>
					
					<form method="post" action="annuaire/recherche-professeurs">
					Nom : <input type="text" style="margin-right:20px" name="nom"/> Ville : <input type="text" name="ville"/> 
					<ul class="select">
						<li>
							<span>Styles musicaux</span>
							<select name="repertoire">
							<option value="">Choisir</option>
							<option>Baroque</option>
							<option>Chanson/Variété</option>
							<option>Chants du monde</option>
							<option>Classique</option>
							<option>Comédie musique</option>
							<option>Contemporain</option>
							<option>Gospel</option>
							<option>Jazz</option>
							<option>Métal</option>
							<option>Moyen âge</option>
							<option>Pop</option>
							<option>Renaissance</option>
							<option>Rock</option>
							<option>Romantique</option>
							<option>R’n’B</option>
							<option>Variété</option>
							<option>Tout style</option>
							</select>
						</li>
						<li>
							<span>Région/département</span>
							<select name="region">
							<option value="">Choisir</option>
							<?php foreach(Arpa::$regions as $region): ?>
							<option value="<?php echo $region ?>"><?php echo $region ?></option>
							<?php endforeach ?>
							</select>
						</li>
					</ul>
					<input type="image" src="images/recherche/recherche.png" alt="Lancer la recherche" style="margin-bottom:5px;"/><br/>
					<span style="font-size:14px;">Résultats <?php echo $paginator->getFirstNbr() ?> à <?php echo $paginator->getLastNbr() ?> sur <?php echo $paginator->total ?></span>
					</form>
					<br/><br/>
					
					<ul class="recherche-list">
						<?php foreach($professeurs as $professeur): ?>
						<li>
							<a href="annuaire/professeurs/<?php echo $professeur->id ?>/<?php echo $professeur->slug ?>"><img src="images/recherche/voir.png" alt=""/></a>
							<strong><?php echo $professeur ?></strong><br/>
							<span><?php echo $professeur->adresse ?><br/>
							<?php echo $professeur->code_postal ?> <?php echo $professeur->ville ?><br/>
							Email : <?php echo $professeur->email ?><br/>
							Site internet : <?php echo $professeur->site_web ?><br/>
							Type de chœurs : <?php echo implode(', ', $professeur->type_choeurs) ?></span>
						</li>
						<?php endforeach ?>
					</ul>
					<?php $paginator->display('annuaire/recherche-professeurs') ?>
				</div>
				<?php $this->component('default', 'sidebar') ?>
			</div>