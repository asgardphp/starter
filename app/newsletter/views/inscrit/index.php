<?php foreach($inscrits as $inscrit): ?>
<a href="<?php echo $this->url_for('show', array('id'=>$inscrit->id)) ?>"><h1><?php echo $inscrit ?></h1></a>
<?php endforeach; ?>