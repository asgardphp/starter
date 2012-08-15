<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2><?php echo $jeu ?></h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
					<form method="post">
					<p>
						<?php if($jeu->magasins): ?>
						<select name="magasin" class="styled">
							<?php foreach(explode("\r\n", $jeu->magasins) as $magasin): ?>
								<option value="">Tous</option>
								<option><?php echo $magasin ?></option>
							<?php endforeach ?>
						</select><br/>
						<?php else: ?>
						<input type="hidden" name="magasin" value=""/>
						<?php endif ?>
						<input type="hidden" name="jeu" value="<?php echo $jeu->id ?>"/>
						<input type="submit" name="type" value="Participants" class="submit long"/>
						<input type="submit" name="type" value="Gagants" class="submit long"/>
					</form>
					<?php
					
					?>
					
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->