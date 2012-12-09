<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2>Slideshow</h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
					<?php Flash::showAll() ?>
					
					<?php
					$form->start()
						->def('image1', array('label'=>'Image Slideshow 1', 'note'=>'Image real size: '.Config::get('slideshow', 'width').'/'.Config::get('slideshow', 'height').'px'))
						->textarea('description1', array('label'=>'Description'))
						->def('image2', array('label'=>'Image Slideshow 2', 'note'=>'Image real size: '.Config::get('slideshow', 'width').'/'.Config::get('slideshow', 'height').'px'))
						->textarea('description2', array('label'=>'Description'))
						->def('image3', array('label'=>'Image Slideshow 3', 'note'=>'Image real size: '.Config::get('slideshow', 'width').'/'.Config::get('slideshow', 'height').'px'))
						->textarea('description3', array('label'=>'Description'))
						->def('image4', array('label'=>'Image Slideshow 4', 'note'=>'Image real size: '.Config::get('slideshow', 'width').'/'.Config::get('slideshow', 'height').'px'))
						->textarea('description4', array('label'=>'Description'))
					->end();
					?>
					<!-- <p><input name="stay" type="submit" class="submit long" value="Save"> </p> -->
					
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->