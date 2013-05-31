<?php
	/**
	@Route('notify')
	*/
	public function notifyAction($request) {
		$p = new Paypal;
		$url = $p->mode == 'test' ? 'www.sandbox.paypal.com':'www.paypal.com';

		$req = 'cmd=_notify-validate';
		foreach ($_POST as $key => $value) {
			$value = urlencode(stripslashes($value));
			$req .= "&$key=$value";
		}
		$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
		$fp = fsockopen ($url, 80, $errno, $errstr, 30);

		$item_name = $_POST['item_name'];
		$item_number = $_POST['item_number'];
		$payment_status = $_POST['payment_status'];
		$payment_amount = $_POST['mc_gross'];
		$payment_currency = $_POST['mc_currency'];
		$txn_id = $_POST['txn_id'];
		$receiver_email = $_POST['receiver_email'];
		$payer_email = $_POST['payer_email'];
		$id = $_POST['custom'];

		if (!$fp) {
		// ERREUR HTTP
		} else {
			fputs ($fp, $header . $req);
			while (!feof($fp)) {
				$res = fgets ($fp, 1024);
				if (strcmp ($res, "VERIFIED") == 0) {
					// transaction valide

					// vérifier que payment_status a la valeur Completed
					if ( $payment_status == "Completed") {
						// vérifier que txn_id n'a pas été précédemment traité: Créez une fonction qui va interroger votre base de données
						// if (VerifIXNID($txn_id) == 0) {
							// vérifier que receiver_email est votre adresse email PayPal principale
							if ( $p->business == $receiver_email) {
								// vérifier que payment_amount et payment_currency sont corrects
								// traiter le paiement
								
								// VALIDATION DU PAIEMENT !!
							}
							else {
								// Mauvaise adresse email paypal
							}
						// }
						// else {
						// 	// ID de transaction déjà utilisé
						// }
					}
					else {
						// Statut de paiement: Echec
					}
				}
				else if (strcmp ($res, "INVALID") == 0) {
					// Transaction invalide                
				}
			}
			fclose ($fp);
		}
	}