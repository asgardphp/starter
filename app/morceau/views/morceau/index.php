<?php foreach($morceaus as $morceau): ?>
<a href="<?php echo $this->url_for('show', array('id'=>$morceau->id)) ?>"><h1><?php echo $morceau ?></h1></a>
<?php endforeach; ?>