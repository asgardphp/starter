			<div id="main">
				<script>
				$(function() {
					$('#content form').ajaxForm({
						statusCode: {
							200: function(responseText, statusText, xhr, jQueryform) {
								dialog('Votre message nous a bien été transmis.<br/>Nous vous donnerons une réponse dans les plus brefs délais.');
							},
							500: function(jqXHR, textStatus, errorThrown) {
								alert(jqXHR.responseText);
							}
						}
					});
				});
				</script>
				<div id="content">
					<h1>Formulaire de contact</h1>
					
					<hr class="small"/>
					
					<style>
					#content label {
						display:inline-block;
						width:80px;
					}
					#content input[type="text"], #content select {
						width:150px;
					}
					#content form {
						line-height:20px;
					}
					</style>
					<div style="float:left; width:280px;">
					<?php $form->start('contact', 'post') ?>
					Vous souhaitez* :<br/>
					<?php
					$boxes = $form->souhaitez->checkboxes();
					while($box = $boxes->next()) {
						$box->input();
						echo ' '.$box->text().'<br/>';
					}
					?>
					<label>Nom* :</label> <?php $form->nom->input() ?><br/>
					<label>Prénom* :</label> <?php $form->prenom->input() ?><br/>
					<label>Téléphone* :</label> <?php $form->telephone->input() ?><br/>
					<label>E-mail* :</label> <?php $form->email->input() ?><br/>
					<label style="width:auto;">Votre question / demande :</label> <br/>
					<?php $form->question->textarea(array('attrs'=>array('style'=>'height:150px; width:230px;'))) ?>
					<span style="font-size:11px; font-style:italic; font-family:Arial; color:#c2c3c5; display:block; text-align:right; width:230px;">600 caractères max.</span>
					
					<input type="image" src="images/contact/envoyer.png" style="margin:20px 0 0 120px;"/>
					
					<?php $form->end() ?>
					</div>
					
					<div style="float:left; width:300px; line-height:25px;">
						<strong>ARPA Midi-Pyrénées</strong><br/>
						1, Allée Abel Boyer<br/>
						31770 Colomiers<br/>
						Téléphone : 05 61 55 44 60<br/>
						E-mail : contact@arpamip.org
						<hr class="small"/>
						<iframe width="360" height="165" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=Atelier+R%C3%A9gional+des+Pratiques+Musicales+Amateur&amp;aq=&amp;sll=43.611673,1.345847&amp;sspn=0.008436,0.021136&amp;ie=UTF8&amp;hq=Atelier+R%C3%A9gional+des+Pratiques+Musicales+Amateur&amp;hnear=&amp;t=m&amp;cid=15215375135387016808&amp;ll=43.611222,1.345997&amp;spn=0.010254,0.030813&amp;z=14&amp;iwloc=A&amp;output=embed"></iframe><br /><small><a href="https://maps.google.com/maps?f=q&amp;source=embed&amp;hl=en&amp;geocode=&amp;q=Atelier+R%C3%A9gional+des+Pratiques+Musicales+Amateur&amp;aq=&amp;sll=43.611673,1.345847&amp;sspn=0.008436,0.021136&amp;ie=UTF8&amp;hq=Atelier+R%C3%A9gional+des+Pratiques+Musicales+Amateur&amp;hnear=&amp;t=m&amp;cid=15215375135387016808&amp;ll=43.611222,1.345997&amp;spn=0.010254,0.030813&amp;z=14&amp;iwloc=A" style="color:#4d4d4f;text-align:left; font-size:12px;">Agrandir le plan</a></small>
					</div>
				</div>
				<?php $this->component('general', 'sidebar') ?>
			</div>