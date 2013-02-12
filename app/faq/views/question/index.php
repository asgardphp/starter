<?php foreach($questions as $question): ?>
<h1><?php echo $question ?></h1>
<?php echo $question->raw('answer') ?>
<?php endforeach; ?>