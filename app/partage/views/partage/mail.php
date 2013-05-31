<?php if($envoye): ?>
<br>Email envoyé !
<?php else: ?>
<?php \Flash::showAll() ?>
<br>
<?php $form->open() ?>
	<label>Adresse courrier de l'émetteur</label><br>
	<?php echo $form->from->def() ?>
	<br>
	<label>Adresse courrier du destinataire</label><br>
	<?php echo $form->to->def() ?>
	<br>
	<label>Votre message</label><br>
	<?php echo $form->message->textarea() ?>
	<input type="submit" value="Envoyer">
<?php $form->close() ?>
<?php endif ?>