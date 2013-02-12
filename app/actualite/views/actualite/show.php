<h1><?php echo $actualite ?></h1>
<p>
	<?php if($actualite->image->exists()): ?>
	<img src="<?php echo $actualite->image ?>" style="float:left; max-height:100px; margin-right:10px">
	<?php endif ?>
	<?php echo $actualite->raw('content') ?>
</p>