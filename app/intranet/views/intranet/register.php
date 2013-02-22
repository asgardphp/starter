<?php
\Flash::showAll();
$form->open();
echo $form->username->label().$form->username->def().'<br>';
echo $form->password->label().$form->password->password().'<br>';
echo $form->email->label().$form->email->def().'<br>';
echo '<input type="submit" value="'.__('Register').'">';
$form->close();