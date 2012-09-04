			<div id="main">
				<div id="content">
					<a href="actualites" class="back-home"> &lt;retour aux actualités </a>
					<h1 class="pink"><?php echo $actualite ?></h1>
					<hr class="small"/>
					<br/>
					
					<div class="fiche-content">
						<div class="image-holder img-left">
							<img src="<?php echo $actualite->image ? $actualite->image:'images/img5.jpg' ?>" width="90" height="90" alt="image description" />
						</div>
						
						<div class="fiche-right" style="font-style:italic">
							<?php echo $actualite->raw('contenu') ?>
						</div>
					</div>
				</div>
				<?php $this->component('default', 'sidebar') ?>
			</div>