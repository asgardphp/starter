<?php
\Flash::showAll();
$form->open();
echo $form->username->label().$form->username->def().'<br>';
echo $form->password->label().$form->password->password().'<br>';
echo $form->remember->label().$form->remember->def().'<br>';
echo '<input type="submit" value="Go">';
$form->close();