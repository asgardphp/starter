<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2>Home</h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
					<p class="breadcrumb">Home</a></p>
				
					<?php Flash::showAll() ?>
					
					<?php
					$form->start()
																				->def('title'
										)
																									->wysiwyg('content'
										, array(
															'config'=>URL::to('bundles/service/ckeditor_config.js'),
										)
										)
																														->def('meta_title')
																									->def('meta_description')
																									->def('meta_keywords');
																																													//~ ->def('image')
																									//~ ->def('presentation')
																						$form->pref->textarea('whatwedo', array('label'=>'What we do'));
															
					$form->end(AdminForm::$SAVE);
					?>
					
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->