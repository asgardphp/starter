<?php foreach($centrales as $centrale): ?>
<a href="<?php echo $this->url_for('show', array('id'=>$centrale->id)) ?>"><h1><?php echo $centrale ?></h1></a>
<?php endforeach; ?>