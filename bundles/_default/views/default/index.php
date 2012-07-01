			<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>	
			<script>
			pos = 1;
			function move(newpos) {
				if(newpos < 1)
					newpos = 3;
				if(newpos > 3)
					newpos = 1;
				
				var margin = (newpos-1)*764;
				$('#slideshow_container').animate({'margin-left': -margin}, 'slow');
				
				pos = newpos;
				
				clearInterval(d);
				d = setInterval(function(){move(pos+1)},5000);
			}
			$(function() {
				$('#slideshow-left').click(function() {
					move(pos-1);
				});
				$('#slideshow-right').click(function() {
					move(pos+1);
				});
				d = setInterval(function(){move(pos+1)}, 5000);
			})
			</script>
			<div id="main">
				<div id="content">
					<div id="slideshow">
						<div id="hidden">
							<ul id="slideshow_container">
								<li>
									<!--<span>Sur le site de l'Atelier Régional des Pratiques musicales Amateurs :<br/>ARPA Midi-Pyrénées. <a href="#">Lire la suite</a></span>-->
									<a href="arpa"><img src="images/accueil/slide-bienvenue.jpg" alt=""/></a>
								</li>
								<li>
									<!--<span>Acteurs des pratiques vocales, retrouvez ici nos propositions de formations,<br/>de colloques, de rencontres. <a href="#">Lire la suite</a></span>-->
									<a href="formations"><img src="images/accueil/slide-formation.jpg" alt=""/></a>
								</li>
								<li>
									<!--<span>Vous êtes un choeur, un ensemble vocal, un professeur de chant ou de chant choral :<br/>
									inscrivez-vous, c'est votre annuaire !!! <a href="#">Lire la suite</a></span>-->
									<a href="annuaire"><img src="images/accueil/slide-annuaire.jpg" alt=""/></a>
								</li>
							</ul>
						</div>
						<img src="images/accueil/left.png" alt="" id="slideshow-left"/>
						<img src="images/accueil/right.png" alt="" id="slideshow-right"/>
					</div>
				</div>
				<div id="sidebar">
					<div id="actualites-holder">
					<?php $this->component('actualite', 'list') ?>
					</div>
				</div>
			</div>
			<div id="accueil-blocks">
				<div class="block">
					<img src="images/accueil/arpa.png" alt="ARPA"/><br/>
					<span class="text">Pôle régional dans le domaine de la voix, l'ARPA vous accompagne dans vos projets depuis plus de 20 ans en Midi-Pyrénées.</span><br/>
					<a href="arpa">Lire la suite</a>
				</div>
				<div class="block">
					<img src="images/accueil/formations.png" alt="Formations"/><br/>
					<span class="text">La formation est commue un outil de développement de l'art vocal dans toutes les esthétiques : un réseau de formateur unique en région.</span><br/>
					<a href="formations">Lire la suite</a>
				</div>
				<div class="block">
					<img src="images/accueil/annonces.png" alt="Petites annonces"/><br/>
					<span class="text">Vous souhaitez annoncer un concert, un stage, un recrutement, un projet : cette page est la votre.</span><br/>
					<a href="annonces">Lire la suite</a>
				</div>
				<div class="block">
					<img src="images/accueil/annuaire.png" alt="Annuaire régional"/><br/>
					<span class="text">L'annuaire : voici un outil de ressource indispensable !!! Vous ne pouvez pas être absent de cette page.</span><br/>
					<a href="annuaire">Lire la suite</a>
				</div>
			</div>