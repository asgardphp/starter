<?php
class Logger implements \Psr\Log\LoggerInterface {
	public function emergency($message, array $context = array()) {
	}

	public function alert($message, array $context = array()) {
	}

	public function critical($message, array $context = array()) {
	}

	public function error($message, array $context = array()) {
	}

	public function warning($message, array $context = array()) {
	}

	public function notice($message, array $context = array()) {
	}

	public function info($message, array $context = array()) {
	}

	public function debug($message, array $context = array()) {
	}

	public function log($level, $message, array $context = array()) {
		switch($level) {
			case \Psr\Log\LogLevel::EMERGENCY:
				$this->emergency($message, $context);
				break;
			case \Psr\Log\LogLevel::ALERT:
				$this->alert($message, $context);
				break;
			case \Psr\Log\LogLevel::CRITICAL:
				$this->critical($message, $context);
				break;
			case \Psr\Log\LogLevel::ERROR:
				$this->error($message, $context);
				break;
			case \Psr\Log\LogLevel::WARNING:
				$this->warning($message, $context);
				break;
			case \Psr\Log\LogLevel::NOTICE:
				$this->notice($message, $context);
				break;
			case \Psr\Log\LogLevel::INFO:
				$this->info($message, $context);
				break;
			case \Psr\Log\LogLevel::DEBUG:
				$this->debug($message, $context);
				break;
		}
	}
}