			<div id="main">
				<div id="content">
					<ul id="tabs">
						<li><a href="arpa">L'ARPA</a></li>
						<li class="actif"><a href="etudes">Etudes - document Bulletin d'inscription</a></li>
						<li><a href="partenaires">Partenaires</a></li>
					</ul>
				
					<div class="fiche-content">
						<p class="soft">L'ARPA assure une mission de veille et d'infomation. Nous mettons à votre disposition un corpus de documents téléchargeables concernant non seulement les pratiques vocales mais également d'autres pratiques culturelles, des textes législatifs, des études, des états des lieux ...
Vous y trouverez également votre bulletin d'adhésion ou d'inscritption.</p>
						<br/>


						<ul class="post-list" style="font-size:14px; margin-left:20px;">
							<?php foreach($documents as $document): ?>
							<li>
								<strong><?php echo $document ?></strong><br/>
								<span style="display:inline-block; width:520px;">Description de l’étude : <?php echo $document->description ?></span>
								<a href="<?php echo $document->getFilePath('document') ?>"><img src="images/arpa/telechargement.png" alt="Téléchargement" style="float:right; margin-top:30px;"/></a>
								<br/><br/>
							</li>
							<?php endforeach ?>
						</ul>
	
					</div>
				</div>
				<?php $this->component('default', 'sidebar') ?>
			</div>