<?php
namespace Coxis\Core\Tools;

class Email {
	var $to;
	var $subject;
	var $from;
	var $text = '';
	var $html = '';

	public function __construct($to, $subject, $from, $text, $html='', $files=array()) {
		$this->to = $to;
		$this->subject = $subject;
		$this->from = $from;
		$this->text = $text;
		$this->html = $html;

		$this->files = $files;
	}

	public static function generate($to, $subject, $from, $text, $html='', $files=array()) {
		$mail = new Email($to, $subject, $from, $text, $html, $files);
		return $mail;
	}
	
	public function send() {
		if($this->html != '' && $this->text == '')
			return $this->sendHTML();
		elseif($this->html == '' && $this->text != '')
			return $this->sendText();
		else
			return $this->sendBoth();
	}
	
	public function sendHTML() {
		$headers = 'From: '.$this->from.''."\r\n";
		
		//~ $semi_rand = md5(time()); 
		//~ $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
		$boundary = md5(uniqid(microtime(), TRUE));
		
		$headers .= 'Mime-Version: 1.0'."\r\n";
		$headers .= 'Content-Type: multipart/mixed;boundary='.$boundary."\r\n";
		$headers .= "\r\n";
		
		$message = '--'.$boundary."\r\n";
		$message .= "Content-Type: text/html; charset=utf-8\r\n\r\n";
		$message .= $this->html."\r\n";
		
		//~ d($this->files);
		
		foreach($this->files as $file) {
			$path = $file['tmp_name'];
			$filename = $file['name'];
			if(!$path)
				continue;
			if(!$filename)
				continue;
			
			$message .= '--'.$boundary."\r\n";
			$fp =    @fopen($path,"rb");
			$data =    @fread($fp,filesize($path));
			@fclose($fp);
			$data = chunk_split(base64_encode($data));
			$message .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n" . 
			"Content-Description: ".$filename."\r\n" .
			"Content-Disposition: attachment;\n" . " filename=\"".$filename."\"; size=".filesize($path).";\r\n" . 
			"Content-Transfer-Encoding: base64\r\n\r\n" . $data . "\r\n\r\n";
		}
		#$message .= '--'.$boundary;
		#$returnpath = "-f" . $sendermail;

		return mail($this->to, $this->subject, $message, $headers);#, $returnpath);
	}
	
	public function sendText() {
		$headers = 'From: '.$this->from.''."\r\n";
		$headers .= "\r\n";

		return mail($this->to, $this->subject, $this->text, $headers);
	}
	
	public function sendBoth() {
		$boundary = md5(uniqid(microtime(), TRUE));

		// Headers
		$headers = 'From: '.$this->from.''."\r\n";
		$headers .= 'Mime-Version: 1.0'."\r\n";
		$headers .= 'Content-Type: multipart/mixed;boundary='.$boundary."\r\n";
		$headers .= "\r\n";
		
		// Message
		$msg = $this->text."\r\n\r\n";// Message HTML
		$msg .= '--'.$boundary."\r\n";
		$msg .= 'Content-type: text/html; charset=utf-8'."\r\n\r\n";
		$msg .= $this->html."\r\n";

		// Fin
		$msg .= '--'.$boundary."\r\n";

		// Function mail()
		mail($this->to, $this->subject, $msg, $headers);
	}
}
