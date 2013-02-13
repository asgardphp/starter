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


//Initialisation du chemin du fichier de log que nous avions mis dans le même
//répertoire que les fichiers du système de paiement
$logfile = "/srv/www/htdocs/monsiteweb/xpay/log.txt";

//Ouverture du fichier de log en append
$fp = fopen($logfile, "a");

//Analyse du code retour

if (( $code == "" ) && ( $error == "" ) )
{
	//Si nous n'obtenons aucun retour de l'API c'est qu'il n'a pas été exécuté (CQFD)
	//Il s'agit la plupart du temps d'un problème dans le chemin vers le binaire response
	//Il peut s'agir d'un problème de droits : vérifiez qu'il ait bien les droits d'exécution
	//Rappellez-vous que ce fichier ne génére aucune sortie HTML
	//Vous ne verrez donc pas de message d'erreur à l'écran

	fwrite($fp, "#======= Le : " . date("d/m/Y H:i:s") . " ========#\n");
	fwrite($fp, "Erreur appel response\n");
	fwrite($fp, "Executable response non trouvé : $path_bin \n");
	fwrite($fp, "-------------------------------------------\n");
}


else if ( $code != 0 ){

	//Erreur,
	//Ici le binaire response a bien été exécuté mais un ou plusieurs paramètres ne sont pas valides
	//En cas de doute, n'hésitez pas à consulter le Dictionnaire des données

	fwrite($fp, "#======= Le : " . date("d/m/Y H:i:s") . " ========#\n");
	fwrite($fp, "Erreur appel API de paiement.\n");
	fwrite($fp, "Message erreur :  $error \n");
	fwrite($fp, "-------------------------------------------\n");
}
else {

	//OK
	//Ici, la transaction s'est bien déroulée, mais cela ne veut pas dire pour autant que
	//le paiement a été accepté !

	//Paiement accepté = '00'
	//Référez-vous au Dictionnaire des données pour les numéros de réponse
	if($bank_response_code == "00"){


		//Caddie
		//Ici nous retrouvons tout notre caddie que nous remmettons dans un tableau
		$arrayCaddie = unserialize(base64_decode($caddie));

		//Date (ymd) / Heure (His) de paiement en français
		$DatePay = substr($payment_date, 6, 2) . "/" . substr($payment_date, 4, 2) . "/"
		. substr($payment_date, 0, 4) ;

		$HeurePay = substr($payment_time, 0, 2) . "h " . substr($payment_time, 2, 2) . ":"
		. substr($payment_time, 4, 2) ;

		//Le reçu de la transaction que nous allons envoyer pour confirmation
		$Sujet = "Confirmation de votre paiement en ligne [MONSITE.COM]";

		$Msg.= "### CECI EST UN MESSAGE AUTOMATIQUE . MERCI DE NE PAS Y RÉPONDRE ###\n\n";
		$Msg.= "Bonjour,\n";
		$Msg.= "Veuillez trouver ci-dessous le reçu de votre paiement en ligne sur MONSITE.COM \n\n";
		$Msg.= "Prenez soin d'imprimer ce message et de le joindre à votre facture.\n";
		$Msg.= "Ces documents vous seront indispensables en cas de réclamation.\n\n";

		$Msg.= "DÉTAIL DE VOTRE COMMANDE \n";
		$Msg.= "------------------------------------------------------------\n\n";
		$Msg.= "NUMÉRO DE COMMANDE             = " . $arrayCaddie[14] . " \n";

		$Msg.= "DATE DE LA TRANSACTION         = $DatePay à $HeurePay \n";
		$Msg.= "ADRESSE WEB DU COMMERCANT      = WWW.MONSITE.COM \n";
		$Msg.= "IDENTIFIANT COMMERCANT         = $merchant_id \n";
		$Msg.= "REFERENCE DE LA TRANSACTION    = $transaction_id \n";
		$Msg.= "MONTANT DE LA TRANSACTION      = " . substr($amount,0,-2) . "," . substr($amount ,-2)
		. " euros \n";
		$Msg.= "NUMERO DE CARTE                = $card_number  \n";
		$Msg.= "AUTORISATION                   = $authorisation_id \n";
		$Msg.= "CERTIFICAT DE LA TRANSACTION   = $payment_certificate \n\n";

		$Msg.= "NOM                            = " . $arrayCaddie[1] . " \n";
		$Msg.= "PRÉNOM                         = " . $arrayCaddie[2] . " \n";
		$Msg.= "SOCIÉTÉ                        = " . $arrayCaddie[3] . " \n";
		$Msg.= "ADRESSE                        = " . $arrayCaddie[4] . " \n";
		$Msg.= "VILLE                          = " . $arrayCaddie[5] . " \n";
		$Msg.= "CODE POSTAL                    = " . $arrayCaddie[6] . " \n";
		$Msg.= "PAYS                           = " . $arrayCaddie[7] . " \n";
		$Msg.= "TÉLÉPHONE                      = " . $arrayCaddie[8] . " \n\n";

		$Msg.= "LOGICIEL                       = " . $arrayCaddie[11] . " \n";
		$Msg.= "VERSION                        = " . $arrayCaddie[12] . " \n";
		$Msg.= "------------------------------------------------------------\n\n";

		$Msg.= "http://www.monsite.com\n\n";

		$Msg.= "Merci de votre confiance \n";


		//Envoi du message au client
		mail($customer_email , $Sujet, $Msg, 'From: shop@monsite.com');

		//On en profite pour s'envoyer également le reçu
		mail('xxxxx@xxxxx.fr' , $Sujet, $Msg, 'From: shop@monsite.com');

		//Mise à jour de la base de données (si vous en utilisez)
		//Ici nous pouvons mettre à jour la base de données
		//puisque la transaction a réussie et le paiement a été accepté
		//Vous connaissez la méthode .. UPDATE... etc. etc.


	}

	//--------------------------------------------------------------------------------


	//La transaction a réussi.
	//Quelque soit le résultat (paiement accepté ou refusé) , nous enregistrerons toutes les données
	//Ceci nous fait une sécurité de plus en cas de panne ou de litige avec le client
	//ou si aucun email n'a été reçu ( ou message envoyé dans le dossier SPAM du logiciel de messagerie)
	//Si votre boutique débite pas mal, ce que je vous souhaite, vous penserez à vider
	//régulièrement votre fichier de logs pour ne pas encombrer votre espace disque.
	fwrite( $fp, "#======================== Le : " . date("d/m/Y H:i:s") . " ====================#\n");
	fwrite( $fp, "merchant_id : $merchant_id\n");
	fwrite( $fp, "merchant_country : $merchant_country\n");
	fwrite( $fp, "amount : $amount\n");
	fwrite( $fp, "transaction_id : $transaction_id\n");
	fwrite( $fp, "transmission_date: $transmission_date\n");
	fwrite( $fp, "payment_means: $payment_means\n");
	fwrite( $fp, "payment_time : $payment_time\n");
	fwrite( $fp, "payment_date : $payment_date\n");
	fwrite( $fp, "response_code : $response_code\n");
	fwrite( $fp, "payment_certificate : $payment_certificate\n");
	fwrite( $fp, "authorisation_id : $authorisation_id\n");
	fwrite( $fp, "currency_code : $currency_code\n");
	fwrite( $fp, "card_number : $card_number\n");
	fwrite( $fp, "cvv_flag: $cvv_flag\n");
	fwrite( $fp, "cvv_response_code: $cvv_response_code\n");
	fwrite( $fp, "bank_response_code: $bank_response_code\n");
	fwrite( $fp, "complementary_code: $complementary_code\n");
	fwrite( $fp, "complementary_info: $complementary_info\n");
	fwrite( $fp, "return_context: $return_context\n");
	
	//ici on dépiote le caddie
	fwrite( $fp, "caddie : \n");
	fwrite( $fp, "----------- \n");

	for($i = 0 ; $i < count($arrayCaddie); $i++){
		fwrite( $fp, $arrayCaddie[$i] . "\n");
	}
	fwrite( $fp, "-------------------------------- \n");

	fwrite( $fp, "receipt_complement: $receipt_complement\n");
	fwrite( $fp, "merchant_language: $merchant_language\n");
	fwrite( $fp, "language: $language\n");
	fwrite( $fp, "customer_id: $customer_id\n");
	fwrite( $fp, "order_id: $order_id\n");
	fwrite( $fp, "customer_email: $customer_email\n");
	fwrite( $fp, "customer_ip_address: $customer_ip_address\n");
	fwrite( $fp, "capture_day: $capture_day\n");
	fwrite( $fp, "capture_mode: $capture_mode\n");
	fwrite( $fp, "data: $data\n");
	fwrite( $fp, "---------------------------------------------------------\n\n");

}

fclose($fp);