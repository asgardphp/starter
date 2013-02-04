<style>
label {
	display:inline-block;
	width:200px;
	vertical-align:top;
}
input[type="text"] {
	width:200px;
}
textarea {
	width:200px;
	height:100px;
}
.errormsg {
	color:red;
}
.success {
	color:green;
}
</style>

<?php $form->open() ?>

<?php \Flash::showAll() ?>

<?php echo $form->name->labelTag() ?> <?php echo $form->name->def() ?><br>
<?php echo $form->email->labelTag() ?> <?php echo $form->email->def() ?><br>
<?php echo $form->message->labelTag() ?> <?php echo $form->message->textarea() ?><br>
<?php echo $form->captcha->labelTag() ?> <?php echo $form->captcha->def() ?><br>
<br>
<input type="submit" value="Envoyer">

<?php $form->close() ?>