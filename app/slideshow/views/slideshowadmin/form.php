<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2>Slideshow</h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
					<?php \App\Core\Flash::showAll() ?>
					
					<script>
					function add() {
						var newslide = $('<?php echo $form->images->renderjQuery("'+$('.slide').length+'") ?>');
						$('#slides').append(newslide);
						newslide.find("input[type=file]").filestyle({ 
						    image: "../admin/img/upload.gif",
						    imageheight : 30,
						    imagewidth : 80,
						    width : 250
						});
					}
					</script>
					<?php
					$form->open();
					?>
					<div id="slides">
						<?php
						foreach($form->images as $k=>$img)
							echo $form->images->def($img);
						?>
					</div>
					<p>
						<input type="button" name="send" value="<?php echo __('Add') ?>" id="" class="submit long" onclick="add()">
					</p>
					<?php
					$form->close();
					?>
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->