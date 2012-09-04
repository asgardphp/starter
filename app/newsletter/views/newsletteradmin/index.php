<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2>Newsletter</h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
				
					<?php Flash::showAll() ?>
					
					<?php
					$form->start();
					$form->input('sujet');
					$form->wysiwyg('contenu');
					$form->end();
					?>
					
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->