				<script>
				$(function() {
					$('.newsletter-form').ajaxForm({
						statusCode: {
							200: function(responseText, statusText, xhr, jQueryform) {
								$('.form-frame .replace').html('Merci de votre inscription.');
							},
							500: function(jqXHR, textStatus, errorThrown) {
								$('.error-message').text(jqXHR.responseText);
							}
						}
					});
				});
				</script>
<div class="form-holder">
						<div class="form-frame">
							<form action="newsletter/submit" method="post" class="newsletter-form">
								<fieldset>
									<h2>Newsletter</h2>
									<div class="replace">
									<div class="row">
										<label for="mail">Votre mail :</label>
										<div class="input-holder">
											<?php $form->email->input(array('attrs'=>array('id'=>'mail','class'=>'text'))) ?>
										</div>
									</div>
									<span class="error-message"></span>
									<div class="submit-row">
										<input class="submit" type="submit" value="Je m’abonne" />
									</div>
									</div>
								</fieldset>
							</form>
						</div>
					</div>