<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2><?php echo !$mailing->isNew() ? $mailing:'New' ?></h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
					<p class="breadcrumb"><a href="<?php echo $this->url_for('index') ?>"><?php echo ucfirst(static::$_models) ?></a> &raquo; 
					<a href="<?php echo !$mailing->isNew() ? $this->url_for('edit', array('id'=>$mailing->id)):$this->url_for('new') ?>">
					<?php echo !$mailing->isNew() ? $mailing:'New' ?>
					</a></p>
				
					<?php Flash::showAll() ?>
					
					<?php
					$form->open();
					echo $form->title->def(array('label'=>'Title'));
					echo $form->content->wysiwyg(array('label'=>'Content', 'config'=>URL::to('newsletter/ckeditor_config.js')));
					echo $form->plaintext->textarea(array(
						'label'=>'Plain Text', 
						'note'=>'When the mail client is not able to show the HTML version, it will fallback to the plain text version. Hence you are advised to fill in both.')
					);
					?>
										
					<hr />
					<p>
						<label for="cours-titre">Test Mail</label>
						<br>
						<input type="text" name="testmail"  class="text">
						<input name="test" type="submit" class="submit long" value="Send a test" /> 
					</p>
					<hr/>
					<p>
						<input name="sauver" type="submit" class="submit long" value="Save" />
						<input name="send" type="submit" class="submit long" value="Save &amp; Back">
						<input name="tous" type="submit" class="submit long" value="Send to all" onclick="return confirm('You are about to send it to all subscribers, are you sure?	')"/> 
					</p>

					<?php $form->close(false) ?>
					
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->