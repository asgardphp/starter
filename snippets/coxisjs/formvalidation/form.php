#todo
several validations for one field (validates only if all passes)
	should still be possible to have different display according to validation function (ex. lili: show another password/password is empty)

<?php HTML::include_js('js/form-validation.js') ?>
<script>
	$(function() {
		$('.block').corner();
		$('input').corner();
		$('select').corner();
		$('select').corner();

		validation(
			'membre[pseudo]',
			function(el, val) {
				if(val.length == 0) {
					el.next().replaceWith('<div><br class="clear"><div class="error"><?php echo __('Choisissez un pseudo.') ?></div></div>');
					return false;
				}
				else if(val.length > 15) {
					el.next().replaceWith('<div><br class="clear"><div class="error"><?php echo __('Pseudo trop long.') ?></div></div>');
					return false;
				}
				var res = $.ajax({
					url: 'ajax/pseudo',
					data: {pseudo: val}, 
					success: function(data, textStatus) {
						if(data != '')
							el.next().replaceWith(data);
						else
							// el.next().css('display', 'none');
							el.next().replaceWith('<img src="img/check.png" style="float: left; margin: -5px 0 0 10px;">');
					},
 					async:   false
 				});
 				return res.responseText == '';
			},
			null,
			null
			, {keyup: false}
		);

		validation(
			'membre[motdepasse]', 
			function(el, val) {
				return val.length >= 6;
			},
			function(el, val) {
				// console.log(el.next());
				el.next().replaceWith('<div><br class="clear"><div class="error"><?php echo __('Mot de passe trop court') ?></div></div>');
			},
			function(el, val) {
				// el.next().css('display', 'none');
				el.next().replaceWith('<img src="img/check.png" style="float: left; margin: -5px 0 0 10px;">');
			}
		);

		validation(
			'confirm', 
			function(el, val) {
				if($('input[name="membre[motdepasse]"]').val() == "")
					return null;
				return val == $('input[name="membre[motdepasse]"]').val();
			},
			function(el, val) {
				el.next().replaceWith('<div><br class="clear"><div class="error"><?php echo __('Confirmation incorrecte.') ?></div></div>');
			},
			function(el, val) {
				// el.next().css('display', 'none');
				el.next().replaceWith('<img src="img/check.png" style="float: left; margin: -5px 0 0 10px;">');
			}
		);

		validation(
			'membre[email]',
			function(el, val) {
				if(!validateEmail(val)) {
					el.next().replaceWith('<div><br class="clear"><div class="error"><?php echo __('Email invalide.') ?></div></div>');
					return false;
				}
				var res = $.ajax({
					url: 'ajax/email',
					data: {email: val}, 
					success: function(data, textStatus) {
						if(data != '')
							el.next().replaceWith('<div><br class="clear"><div class="error">Email déjà utilisé.</div></div>');
						else
							el.next().replaceWith('<img src="img/check.png" style="float: left; margin: -5px 0 0 10px;">');
					},
 					async:   false
 				});
 				return res.responseText == '';
			},
			null,
			null
			, {keyup: false}
		);

		// validation(
		// 	'membre[email]', 
		// 	function(el, val) {
		// 		return validateEmail(val);
		// 	},
		// 	function(el, val) {
		// 		el.next().replaceWith('<div><br class="clear"><div class="error"><?php echo __('Email invalide.') ?></div></div>');
		// 	},
		// 	function(el, val) {
		// 		// el.next().css('display', 'none');
		// 		el.next().replaceWith('<img src="img/check.png" style="float: left; margin: -5px 0 0 10px;">');
		// 	}
		// );

		validation(
			'cgv', 
			function(el, val) {
				// console.log(el.is(':checked'));
				// console.log($('#form-cgv').is(':checked'));
				// console.log(el.is(':checked'));
				// console.log($('#form-cgv').is(':checked'));
				return el.is(':checked');
			},
			function(el, val) {
				el.next().next().replaceWith('<div class="error" style="display:block; width:400px; margin-bottom:10px"><?php echo __('Vous devez accepter les conditions.') ?></div>');
			},
			function(el, val) {
				el.next().next().css('display', 'none');
				// el.next().replaceWith('<img src="img/check.png" style="float: left; margin: -5px 0 0 10px;">');
			},
			{
				onEvent: function(el, fct) {
					el.prev().click(fct);
				}
			}
		);

		validation(
			'membre[captcha]', 
			function(el, val) {
				var res = $.ajax({
					url: 'ajax/captcha',
					data: {captcha: val},
 					async:   false
 				});
 				return (res.responseText != '0');
			},
			function(el, val) { el.next().replaceWith('<div><br class="clear"><div class="error"><?php echo __('Captcha invalide.') ?></div></div>'); },
			function(el, val) {
				// el.next().css('display', 'none');
				el.next().replaceWith('<img src="img/check.png" style="float: left; margin: -5px 0 0 10px;">');
			},
			{keyup: false}
		);

		$('#content form').submit(function() {
			for(var i=0; i<registry.length; i++)
				if(!registry[i](true)) return false;
		});
	});
</script>