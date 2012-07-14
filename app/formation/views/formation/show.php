			<div id="main">
				<div id="content">
					<a href="formations" class="back-home"> &lt;retour aux formations </a>
					<h1 class="pink"><?php echo $formation ?></h1>
					<hr class="small"/>
					<br/>
					
					<div class="fiche-content">
						<div class="image-holder img-left">
							<img src="<?php echo $formation->getFilePath('image') ? $formation->getFilePath('image'):'images/img5.jpg' ?>" width="90" height="90" alt="image description" />
						</div>
						
						<div class="fiche-right">
							<div class="intro">
								<?php if($formation->date) echo $formation->date ?>
								<?php if($formation->date && $formation->lieu) echo ', <br/>' ?>
								<?php if($formation->date) echo $formation->lieu ?>
								<?php if($formation->date || $formation->lieu) echo '.' ?>
							</div>
							<div>
								<?php echo $formation->raw('contenu') ?>
							</div>
						</div>
					</div>
				</div>
				<?php $this->component('general', 'sidebar') ?>
			</div>