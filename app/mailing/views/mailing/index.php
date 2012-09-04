<?php foreach($mailings as $mailing): ?>
<a href="<?php echo $this->url_for('show', array('id'=>$mailing->id)) ?>"><h1><?php echo $mailing ?></h1></a>
<?php endforeach; ?>