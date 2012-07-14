			<div id="main">
				<div id="content">
					<a href="#" class="back-home"> &lt;retour à l’accueil formations </a>
					<h1>Les formations</h1>
					<div class="content-block">
						<img class="alignright" src="images/img1.jpg" width="265" height="103" alt="image description" />
						<div class="text-box">
							<p>Fort d'un réseau national d'intervenants de très grande qualité, l'ARPA vous propose des formations pertinentes et innovantes pour tous les publics. Si vous utilisez la voix dans votre pratique professionnelle ou en amateur, ces formations vous concernent.</p>
						</div>
					</div>
					<ul class="post-list">
					<?php foreach($formations as $formation): ?>
						<li>
							<div class="image-holder">
								<img src="<?php echo $formation->getFilePath('image') ? $formation->getFilePath('image'):'images/img5.jpg' ?>" width="90" height="90" alt="image description" />
							</div>
							<div class="text-info">
								<h2><a href="formations/<?php echo $formation->id ?>/<?php echo $formation->slug ?>"><?php echo $formation ?></a></h2>
								<em class="date">
									<?php if($formation->date) echo $formation->date ?>
									<?php if($formation->date && $formation->lieu) echo ', ' ?>
									<?php if($formation->date) echo $formation->lieu ?>
									<?php if($formation->date || $formation->lieu) echo '.' ?>
								</em>
								<p><?php echo $formation->introduction ?></p>
								<a href="formations/<?php echo $formation->id ?>/<?php echo $formation->slug ?>" class="more">Lire la suite</a>
							</div>
						</li>
					<?php endforeach ?>
					</ul>
					<?php $paginator->display('formations/liste') ?>
				</div>
				<?php $this->component('default', 'sidebar') ?>
			</div>