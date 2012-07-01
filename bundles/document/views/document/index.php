<?php foreach($documents as $document): ?>
<a href="<?php echo $this->url_for('show', array('id'=>$document->id)) ?>"><h1><?php echo $document ?></h1></a>
<?php endforeach; ?>