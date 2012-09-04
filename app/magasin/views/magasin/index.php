<?php foreach($magasins as $magasin): ?>
<a href="<?php echo $this->url_for('show', array('id'=>$magasin->id)) ?>"><h1><?php echo $magasin ?></h1></a>
<?php endforeach; ?>