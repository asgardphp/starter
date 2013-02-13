<?php

//Récupération du caddie
$TheCaddie = array();

$TheCaddie[] =  $_SESSION['USER_ID'];
$TheCaddie[] =  $_SESSION['USER_NOM'];
$TheCaddie[] =  $_SESSION['USER_PRENOM'];
$TheCaddie[] =  $_SESSION['USER_COMPANY'];
$TheCaddie[] =  $_SESSION['USER_ADRESSE'];
$TheCaddie[] =  $_SESSION['USER_VILLE'];
$TheCaddie[] =  $_SESSION['USER_ZIP'];
$TheCaddie[] =  $_SESSION['USER_PAYS'];
$TheCaddie[] =  $_SESSION['USER_TEL'];
$TheCaddie[] =  $_SESSION['USER_EMAIL'];

$TheCaddie[] =  $_SESSION['CADDIE_ID'];
$TheCaddie[] =  $_SESSION['CADDIE_LOG_NOM'];
$TheCaddie[] =  $_SESSION['CADDIE_LOG_VERSION'];
$TheCaddie[] =  $_SESSION['CADDIE_AMOUNT'];

//Numéro de commande
$NumCmd      = "CMD-" . date("YmdHis");
$TheCaddie[] =  $NumCmd ;

$xCaddie = base64_encode(serialize($TheCaddie));

//Identifiant du commerçant (TEST)
$parm = "merchant_id=013044876511111";

//Chemins binaire + pathfile
$parm .= " pathfile=/srv/www/htdocs/monsiteweb/xpay/pathfile";
$path_bin = "/srv/www/htdocs/empty/request_2.6.9_3.4.2";

//Langages
$parm .= " merchant_country=fr";
$parm .= " language=fr";

//Montant du caddie
$parm .= " amount=" .$_SESSION['CADDIE_AMOUNT'];

//Euro
$parm .= " currency_code=978";

//Numéro de transaction
$parm .= " transaction_id=" . date ("His");

//Complément du reçu
$Produit = "<tr><td>CHEZN1BUS&nbsp;:&nbsp;&nbsp;LOGICIEL&nbsp;PLANNINGFACILE</td></tr>";
$parm .= " receipt_complement=" . $Produit;

//Email du client
$parm .= " customer_email=" . $_SESSION['USER_EMAIL'];

//IP client
$parm .= " customer_ip_address=" . $IP;

//caddie
$parm .= " caddie=" . $xCaddie ;

//url en cas d'annulation
$SUPERID = session_id();
$parm .= " cancel_return_url=http://www.monsite.com/response.php?SUPERID=" . $SUPERID;

// url réponse automatique
$parm .= " automatic_response_url=http://www.monsite.com/call_autoresponse.php";

//url de retour du client après le paiement
$parm .= " normal_return_url=http://www.monsite.com/response.php?SUPERID=" . $SUPERID;

//Template
$parm .= " templatefile=le_template_de_mon_site";


//Appel du binaire request
$result  = exec("$path_bin $parm");
$tableau = explode ("!", "$result");

//Récupération des paramètres
$code    = $tableau[1];
$error   = $tableau[2];
$message = $tableau[3];

//Analyse du code retour

if (( $code == "" ) && ( $error == "" ) )
{
	//Erreur chemin ou droits
	print ("<center><h1>Erreur appel request</h1></center>");
	print ("<p>&nbsp;</p>");
	print ("<p>Executable request non trouve : $path_bin</p>");
}


else if ($code != 0){

	//Erreur paramètres
	print ("<center><h1>Erreur appel API de paiement.</h1></center>");
	print ("<p>&nbsp;</p>");
	print ("<p>Message erreur : $error </p>");
}


else {

	//OK, on affiche les cartes bancaires
	print ("<p>&nbsp;</p>");
	print ("$message");
	print ("<p>&nbsp;</p>");
	print("<p align='center'><strong>Montant à payer : </strong> ".
	substr($_SESSION['CADDIE_AMOUNT'],0,-2).",".substr($_SESSION['CADDIE_AMOUNT'] ,-2) . " Euros  - ");
	print("<strong>Num&eacute;ro de commande : </strong> " . $NumCmd  ."</p>");
	print("<p align='center'><a href='javascript:history.go(-1)'>RETOUR</a></p>");

	//Vérifier le contenu du parm caddie que l'on va envoyer (Test ONLY !!!)

	/*
	$arrayCaddie = unserialize(base64_decode($xCaddie));
	for($i = 0 ; $i < count($arrayCaddie); $i++){
	echo $arrayCaddie[$i] . "<br />";
	}
	*/

}