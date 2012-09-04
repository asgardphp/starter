<?php foreach($participants as $participant): ?>
<a href="<?php echo $this->url_for('show', array('id'=>$participant->id)) ?>"><h1><?php echo $participant ?></h1></a>
<?php endforeach; ?>