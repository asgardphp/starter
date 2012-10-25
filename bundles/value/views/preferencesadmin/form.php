			<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>	
					<h2>Preferences</h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
					<p class="breadcrumb"><a href="preferences">Preferences</a></p>
				
					<?php \Flash::showAll() ?>
					
					<?php
					$form->start();
						
					$form->values['email']->def('value', array('label'=>'Email'));
					
					$form->values['name']->def('value', array('label'=>'Nom*'));
					
					$form->values['head_script']->def('value', array('label'=>'Script'));
					
					$form->end();
					?>
					
					
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->