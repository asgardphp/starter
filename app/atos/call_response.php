<?php

//Récupération de la variable DATA en provenance du serveur du fournisseur.
//La réponse de la transaction est contenue dans cette variable dans son intégralité
//Evidemment toutes ces données sont chiffrées : c'est le binaire response qui fera tout le travail.
$message = "message=" . escapeshellcmd($_POST['DATA']);


// Initialisation du chemin du fichier pathfile
$pathfile = "pathfile=/srv/www/htdocs/monsiteweb/xpay/pathfile";

//Initialisation du chemin de l'executable response
$path_bin = "/srv/www/htdocs/empty/response_2.6.9_3.4.2";

//Appel du binaire response
$result = exec("$path_bin $pathfile $message");


//On separe les differents champs et on les met dans une variable tableau
//Puis on récupère toutes les valeurs.
//En vérité nous n'avons besoin ici que de quelques variables
//mais ceci vous permet d'identifier les différentes valeurs du tableau

$tableau = explode ("!", $result);

$code                = $tableau[1];
$error               = $tableau[2];
$merchant_id         = $tableau[3];
$merchant_country    = $tableau[4];
$amount              = $tableau[5];
$transaction_id      = $tableau[6];
$payment_means       = $tableau[7];
$transmission_date   = $tableau[8];
$payment_time        = $tableau[9];
$payment_date        = $tableau[10];
$response_code       = $tableau[11];
$payment_certificate = $tableau[12];
$authorisation_id    = $tableau[13];
$currency_code       = $tableau[14];
$card_number         = $tableau[15];
$cvv_flag            = $tableau[16];
$cvv_response_code   = $tableau[17];
$bank_response_code  = $tableau[18];
$complementary_code  = $tableau[19];
$complementary_info  = $tableau[20];
$return_context      = $tableau[21];
$caddie              = $tableau[22];
$receipt_complement  = $tableau[23];
$merchant_language   = $tableau[24];
$language            = $tableau[25];
$customer_id         = $tableau[26];
$order_id            = $tableau[27];
$customer_email      = $tableau[28];
$customer_ip_address = $tableau[29];
$capture_day         = $tableau[30];
$capture_mode        = $tableau[31];
$data                = $tableau[32];


//Analyse du code retour

if (( $code == "" ) && ( $error == "" ) ){

	//Si nous n'obtenons aucun retour de l'API c'est qu'il n'a pas été exécuté (CQFD)
	//Il s'agit la plupart du temps d'un problème dans le chemin vers le binaire response
	//Il peut s'agir d'un problème de droits : vérifiez qu'il ait bien les droits d'exécution

	print ("<center><h1>Erreur appel response</h1></center>");
	print ("<p>Executable response non trouve : $path_bin </p>");
}

else if ( $code != 0 ){

	//Erreur,
	//Ici le binaire response a bien été exécuté mais un ou plusieurs paramètres ne sont pas valides
	//En cas de doute, n'hésitez pas à consulter le Dictionnaire des données

	print ("<center><h1>Erreur appel API de paiement.</h1></center>");
	print ("<p>Message erreur : $error </p>");


}

else {

	//OK
	//Ici, la transaction s'est bien déroulée, mais cela ne veut pas dire pour autant que
	//le paiement a été accepté !

	//Paiement accepté = '00'
	//Référez-vous au Dictionnaire des données pour les numéros de réponse

	switch($bank_response_code){

		case "00" :
			print("<p>Votre paiement a été accepté par votre établissement bancaire</p>");
			break;

		case "05" :
			print("<p>Votre paiement a été refusé par votre établissement bancaire</p>");
			break;

		case "33" :
			print("<p>La date de validité de votre carte bancaire est dépassée</p>");
			break;
		
		default : print("<p>La transaction n'a pu aboutir suite à un problème technique</p>");

	}
	
	// if($bank_response_code == "00"){

	// 	print("<center><h1>Merci</h1></center>");
	// 	print("<p>Votre paiement a été accepté par notre établissement bancaire</p>");
	// 	print("<p>Un message électronique vous a été envoyé <br />");
	// 	print("il contient le reçu de la transaction et le détail de votre commande</p>");
	// 	print("<p>Merci de votre confiance</p>");
	// }

	// //Paiement refusé
	// else{

	// 	print("<center><h1>Votre paiement a été refusé par notre établissement bancaire</h1></center>");

	// }

	//Ici nous affichons un message dans le cas où l'internaute aurait cliqué
	//sur le bouton ANNULATION
	if($response_code == "17"){

		print("<center><h1>Transaction annulée par l'utilisateur</h1></center>");

	}
}