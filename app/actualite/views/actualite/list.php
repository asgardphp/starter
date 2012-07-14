<ul class="news-list">
<?php foreach($actualites as $actualite): ?>
						<li>
							<img src="<?php echo $actualite->getFilePath('image') ? $actualite->getFilePath('image'):'images/img5.jpg' ?>" width="70" height="70" alt="image description" />
							<div class="info-text">
								<h3><?php echo $actualite ?></h3>
								<p><?php echo Tools::truncate(strip_tags($actualite->raw('contenu')), 100, ' (...)') ?></p>
								<a href="actualites/<?php echo $actualite->id ?>/<?php echo $actualite->slug ?>" class="more">Lire la suite</a>
							</div>
						</li>
<?php endforeach ?>
					</ul>
					<?php $paginator->display('actualites/widget') ?>