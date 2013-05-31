<?php
#https://developer.paypal.com/webapps/developer/applications/ipn_simulator

class Paypal {
	public $url_return;
	public $url_cancel;
	public $url_notify;
	public $business;
	public $mode = 'test';
	// public $cmd = '_xclick';
	public $cmd = '_donations';

	function __construct() {
		$this->business = 'michel-facilitator@hognerud.net';
		$this->url_return = URL::url_for(array('Soutenir', 'paypal'));
		$this->url_cancel = URL::url_for(array('Soutenir', 'index'));
		$this->url_notify = URL::url_for(array('Soutenir', 'notify'));
	}

	function button($amount, $item_name, $id) {
		?>
		<form action="<?php echo $this->mode=='test' ? 'https://www.sandbox.paypal.com/cgi-bin/webscr':'' ?>" method="post">
		<input type='hidden' value="<?php echo $amount ?>" name="amount" />
		<input name="currency_code" type="hidden" value="EUR" />
		<input name="shipping" type="hidden" value="0.00" />
		<input name="tax" type="hidden" value="0.00" />
		<input name="return" type="hidden" value="<?php echo $this->url_return ?>" />
		<input name="cancel_return" type="hidden" value="<?php echo $this->url_cancel ?>" />
		<input name="notify_url" type="hidden" value="<?php echo $this->url_notify ?>" />
		<input name="cmd" type="hidden" value="<?php echo $this->cmd ?>" />
		<input name="business" type="hidden" value="<?php echo $this->business ?>" />
		<input name="item_name" type="hidden" value="<?php echo $item_name ?>" />
		<input name="no_note" type="hidden" value="1" />
		<input name="lc" type="hidden" value="FR" />
		<input name="bn" type="hidden" value="PP-BuyNowBF" />
		<input name="custom" type="hidden" value="<?php $id ?>" />
		<input alt="Effectuez vos paiements via PayPal : une solution rapide, gratuite et sécurisée" name="submit" src="https://www.paypalobjects.com/fr_FR/FR/i/btn/btn_donateCC_LG.gif" type="image" /><img src="https://www.paypal.com/fr_FR/i/scr/pixel.gif" border="0" alt="" width="1" height="1" />
		</form>
		<?php
	}
}