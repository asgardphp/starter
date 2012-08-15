<html>
<head>
	<title><?php echo $jeu->titre ?></title>
	<meta charset="utf-8">
	<base href="<?php echo URL::base() ?>"/>
	<script src="js/jquery.js"></script>
	<script src="js/jquery.form.js"></script>
	
	<style>
	* {
		padding:0;
		margin:0;
	}
	body {
		background-color:#<?php echo $jeu->couleur_de_fond ?>;
		color:#00662f;
	}
	#container {
		background-image:url(<?php echo $jeu->image_de_fond ?>);
		width:980px;
		height:800px;
		margin:0 auto;
		position:relative;
	}
	#form1 {
		position:absolute;
		top:460px;
		right:360px;
		width:335px;
		height:200px;
	}
	label {
		display:inline-block;
		width:100px;
		margin-right:5px;
		text-align:right;
		font-size:12px;
	}
	input[type="text"] {
		height:20px;
		width:210px;
		margin-bottom:4px;
		outline:none;
		border:0;
	}
	input[type="image"] {
		margin-left:130px;
		margin-top:15px;
	}
	
	#form2 {
		position:absolute;
		top:410px;
		right:30px;
		width:280px;
		height:200px;
	}
	
	#form3 {
		position:absolute;
		top:485px;
		right:30px;
		width:280px;
		height:200px;
	}
	
	#form4 {
		position:absolute;
		top:610px;
		right:30px;
		width:280px;
		height:200px;
	}
	#form4 input[type="text"] {
		width:200px;
	}
	#form4 label {
		width:60px;
	}
	#form4 input[type="image"] {
		margin-left:130px;
		margin-top:5px;
	}
	</style>
	<script>
	$(function() {
		$('form').ajaxForm({
			statusCode: {
				200: function(responseText, statusText, xhr, jQueryform) {
					alert('Merci de votre participation !');
				},
				500: function(jqXHR, textStatus, errorThrown) {
					alert(jqXHR.responseText);
				}
			}
		});
	});
	</script>
</head>
<body>
	<div id="container">
		<?php if($jeu->image_optionnelle): ?>
		<a href="<?php echo $jeu->lien_optionnelle ?>"><img src="<?php echo $jeu->image_optionnelle ?>" style="position:absolute; bottom:0; left:0;"/></a>
		<?php endif ?>
		<a href="<?php echo $jeu->pdf ?>"><img src="<?php echo $jeu->reglement_du_jeu ?>" style="position:absolute; bottom:0; left:0;"/></a>
		
		<form method="post" action="<?php $jeu->adresse ?>">
		<div id="form1">
			<div style="text-align:center; margin-bottom:5px;">
				<strong>Remplissez ce formulaire et répondez à la question ci-contre</strong> <span style="font-size:9px; font-style:italic">(*champs obligatoires)</span>
			</div>
			<label>Nom*</label> <?php $form->nom->input() ?><br/>
			<label>Prénom*</label> <?php $form->prenom->input() ?><br/>
			<label>Adresse*</label> <?php $form->adresse->input() ?><br/>
			<label>Code postal*</label> <?php $form->code_postal->input() ?><br/>
			<label>Ville*</label> <?php $form->ville->input() ?><br/>
			<label>Pays*</label> <?php $form->pays->input() ?><br/>
			<label>E-mail*</label> <?php $form->email->input() ?><br/>
			<label>Téléphone*</label> <?php $form->telephone->input() ?><br/>
			<div style="text-align:center; padding-top:10px;">
				<?php $form->accepter->checkbox() ?> <span style="font-size:12px;">J'ai lu et j'accepte le règlement du jeu</span>
			</div>
			<input type="image" src="<?php echo $jeu->valider ?>"/>
		</div>
		
		<div id="form2">
			<div style="text-align:center;"><strong><?php echo $jeu->question ?>*</strong></div>
			<select style="width:280px; margin-top:5px;" name="participant[reponse]">
				<?php foreach(explode("\n", $jeu->reponses) as $reponse): ?>
				<option><?php echo $reponse ?></option>
				<?php endforeach ?>
			</select>
		</div>
		
		<?php if($jeu->question_magasin): ?>
		<div id="form3">
			<div style="text-align:center;"><strong><?php echo $jeu->question_magasin ?>*</strong></div>
			<select style="width:280px; margin-top:5px;" name="participant[magasin]">
				<?php foreach(explode("\n", $jeu->magasins) as $magasin): ?>
				<option><?php echo $magasin ?></option>
				<?php endforeach ?>
			</select>
		</div>
		<?php endif ?>
		
		<div id="form4">
			<div style="text-align:center; margin-bottom:15px;">
				<strong>Remplissez ce formulaire et répondez à la question ci-contre</strong> <span style="font-size:9px; font-style:italic">(*champs obligatoires)</span><br/>
				<span style="font-size:10px">(Entrez l'email utilisé pour votre première participation ainsi que le code barre présent sur le produit acheté)</span>
			</div>
			<label>Email</label> <?php $form->prev_email->input() ?><br/>
			<label>Code barre</label> <?php $form->code_barre->input() ?><br/>
			<input type="image" src="<?php echo $jeu->valider ?>"/>
		</div>
		</form>
	</div>
</body>
</html>