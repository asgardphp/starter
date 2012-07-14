			<div id="main">
				<div id="content">
					<a href="annuaire" class="back-home"> &lt;retour à l’accueil de l'annuaire </a>
					<h1>Recherche du chœurs</h1>
					
					<hr class="small"/>
					<br/>
					
					<form method="post" action="annuaire/recherche-choeurs">
					Nom : 
					<input type="text" style="margin-right:20px" name="nom"/>
					Ville :
					<input type="text" name="ville"/> 
					<ul class="select">
						<li>
							<span>Répertoire du Chœur</span>
							<select name="repertoire">
								<option value="">Choisir</option>
								<option>Baroque</option>
								<option>Chanson/Variété</option>
								<option>Chants du monde</option>
								<option>Classique</option>
								<option>Comédie musique</option>
								<option>Comptines et contes musicaux</option>
								<option>Gospel</option>
								<option>Grégorien</option>
								<option>Jazz</option>
								<option>Médiéval</option>
								<option>Musique contemporaine (après 1945)</option>
								<option>Musique du XXème siècle (avant 1945)</option>
								<option>Musique lithurgique d'aujourd'hui</option>
								<option>Musique traditionnelle et/ou folklorique</option>
								<option>Negro spiritual</option>
								<option>Opéra</option>
								<option>Opéra pour enfants</option>
								<option>Opérette</option>
								<option>Oratorio</option>
								<option>Renaissance</option>
								<option>Romantique</option>
								<option>Tout type de répertoire</option>
							</select>
						</li>
						<li>
							<span>Type de Chœur</span>
							<select name="type">
							<option value="">Choisir</option>
							<option>Choisir</option>
							<option>Chœur d’enfants</option>
							<option>Chœur de jeunes (16-26 ans)</option>
							<option>Chœur d’adultesChanson/Variété</option>
							<option>Chœur associant des enfants et des adultes</option>
							<option>Chœur d’université et enseignement supérieur</option>
							<option>Chœur d’entreprise</option>
							<option>Chœur de maison de retraite</option>
							<option>Chœur liturgique</option>
							<option>Chœur mixte</option>
							<option>Chœur à voix égales de femmes</option>
							<option>Chœur à voix égales d’hommes</option>
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
						<?php foreach($choeurs as $choeur): ?>
						<li>
							<a href="annuaire/choeurs/<?php echo $choeur->id ?>/<?php echo $choeur->slug ?>"><img src="images/recherche/voir.png" alt=""/></a>
							<strong><?php echo $choeur ?></strong><br/>
							<span><?php echo $choeur->adresse ?><br/>
							<?php echo $choeur->code_postal ?> <?php echo $choeur->ville ?><br/>
							Email : <?php echo $choeur->email ?><br/>
							Site internet : <?php echo $choeur->site_web ?><br/>
							Type de chœurs : <?php echo implode(', ', $choeur->type_choeurs) ?>
							</span>
						</li>
						<?php endforeach ?>
					</ul>
					<?php $paginator->display('annuaire/recherche-choeurs') ?>
				</div>
				<?php $this->component('default', 'sidebar') ?>
			</div>