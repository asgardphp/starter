<?php foreach($actualites as $actualite): ?>
<a href="<?php echo $actualite->url() ?>"><?php echo $actualite ?></a><br>
<?php endforeach; ?>