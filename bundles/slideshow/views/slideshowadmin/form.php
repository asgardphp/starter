<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2>Slideshow</h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
					<?php \Coxis\Core\Flash::showAll() ?>
					
					<?php
					$form->open();
					echo
						$form->image1->def(array('label'=>'Image Slideshow 1', 'note'=>'Image real size: '.Config::get('slideshow', 'width').'/'.Config::get('slideshow', 'height').'px')).
						$form->description1->textarea(array('label'=>'Description')).
						$form->image2->def(array('label'=>'Image Slideshow 2', 'note'=>'Image real size: '.Config::get('slideshow', 'width').'/'.Config::get('slideshow', 'height').'px')).
						$form->description2->textarea(array('label'=>'Description')).
						$form->image3->def(array('label'=>'Image Slideshow 3', 'note'=>'Image real size: '.Config::get('slideshow', 'width').'/'.Config::get('slideshow', 'height').'px')).
						$form->description3->textarea(array('label'=>'Description')).
						$form->image4->def(array('label'=>'Image Slideshow 4', 'note'=>'Image real size: '.Config::get('slideshow', 'width').'/'.Config::get('slideshow', 'height').'px')).
						$form->description4->textarea(array('label'=>'Description'));
					$form->close();
					?>
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->