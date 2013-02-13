<?php
$form->open();
echo $form->term->label().': '.$form->term->def();
echo '<input type="submit" value="GO">';
$form->close();
?>

<br><br>

<h1>Results</h1>
<?php
foreach($results as $result) {
	echo '<p>
		<a href="'.$result['link'].'">'.$result['title'].'</a><br>
		'.Tools::truncate($result['description'], 100).'
	<p>';
}