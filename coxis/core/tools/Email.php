<?php
namespace Coxis\Core\Tools;

class Email {
	protected $to;
	protected $from;
	protected $subject;
	protected $text = '';
	protected $html = '';
	protected $files = array();
 
	function __construct($to, $from, $subject, $text='') {
 		$this->to = $to;
 		$this->from = $from;
		$this->subject = $subject;
 		$this->text = $text;
 	}
 
	public static function create($to, $from, $subject, $text='') {
		$mail = new static($to, $from, $subject, $text);
 		return $mail;
 	}

	public function text($text) {
		$this->text = $text;
		return $this;
 	}
 
	public function html($html) {
		$this->html = $html;
		return $this;
 	}

	public function addFile($file, $filename=null) {
		if($filename)
			$this->files[$filename] = $file;
		else
			$this->files[] = $file;
		return $this;
	}
	
	public function send() {
		$boundary = md5(uniqid(microtime(), TRUE));

		// Headers
		$headers = 'From: '.$this->from.''."\r\n";
		$headers .= 'Mime-Version: 1.0'."\r\n";
		$headers .= 'Content-Type: multipart/mixed;boundary='.$boundary."\r\n";
		$headers .= "\r\n";
		
		$msg = '';

		#text
		if($this->text) {
			$msg .= '--'.$boundary."\r\n";
			$msg .= 'Content-type: text/plain; charset=utf-8'."\r\n";
			$msg = $this->text."\r\n";
		}

		#html
		if($this->html) {
			$msg .= '--'.$boundary."\r\n";
			$msg .= 'Content-type: text/html; charset=utf-8'."\r\n\r\n";
			$msg .= $this->html."\r\n";
		}

		#files
		if($this->files) {
			foreach($this->files as $filename=>$path) {
				if(is_int($filename))
					$filename = basename($path);

				$msg .= '--'.$boundary."\r\n";
				$fp = fopen($path,"rb");
				$data = fread($fp,filesize($path));
				fclose($fp);
				$data = chunk_split(base64_encode($data));
				$msg .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n" . 
				"Content-Description: ".$filename."\r\n" .
				"Content-Disposition: attachment;\n" . " filename=\"".$filename."\"; size=".filesize($path).";\r\n" . 
				"Content-Transfer-Encoding: base64\r\n\r\n" . $data . "\r\n\r\n";
			}
		}

		return mail($this->to, $this->subject, $msg, $headers);
	}
}
