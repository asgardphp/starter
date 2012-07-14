			<div id="main">
				<div id="content">
					<ul id="tabs">
						<li><a href="arpa">L'ARPA</a></li>
						<li><a href="etudes">Etudes - document Bulletin d'inscription</a></li>
						<li class="actif"><a href="partenaires">Partenaires</a></li>
					</ul>
				
					<div class="fiche-content">
						<?php echo $page->raw('content') ?>
					</div>
				</div>
				<?php $this->component('general', 'sidebar') ?>
			</div>