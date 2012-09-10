<?php foreach($recettes as $recette): ?>
<a href="<?php echo $this->url_for('show', array('id'=>$recette->id)) ?>"><h1><?php echo $recette ?></h1></a>
<?php endforeach; ?>