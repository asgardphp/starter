			<div id="main">
				<div id="content">
					<h1>Actualités</h1>
					<hr class="small"/>
					<ul class="post-list">
						<?php foreach($actualites as $actualite): ?>
						<li>
							<div class="image-holder">
								<img src="<?php echo $actualite->getFilePath('image') ? $actualite->getFilePath('image'):'images/img5.jpg' ?>" width="90" height="90" alt="image description" />
							</div>
							<div class="text-info">
								<h2><a href="actualites/<?php echo $actualite->id ?>/<?php echo $actualite->slug ?>"><?php echo $actualite ?></a></h2>
								<em class="date">
									<?php if($actualite->date) echo $actualite->date ?>
									<?php if($actualite->date && $actualite->lieu) echo ', ' ?>
									<?php if($actualite->date) echo $actualite->lieu ?>
									<?php if($actualite->date || $actualite->lieu) echo '.' ?>
								</em>
								<p><?php echo $actualite->introduction ?></p>
								<a href="actualites/<?php echo $actualite->id ?>/<?php echo $actualite->slug ?>" class="more">Lire la suite</a>
							</div>
						</li>
						<?php endforeach ?>
					</ul>
					<?php $paginator->display('actualites') ?>
				</div>
				<?php $this->component('default', 'sidebar') ?>
			</div>