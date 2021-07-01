<?php

final class MessageController {

	private $loc;
	private $input;
	private $modules;
	private $errors;
	private $messages;

	public function __construct($loc, $input, $modules) {

		$this->loc = $loc;
		$this->input = $input;
		$this->modules = $modules;
		$this->errors = array();
		$this->messages =  array();

	}

	public function setState() {

		if ($this->loc[0] == 'message') {

			if (!Auth::isLoggedIn()) {

				$loginURL = '/' . Lang::prefix() . 'login/';
				header("Location: $loginURL");

			}

			if ($this->loc[1] == 'draft' && !empty($this->input)) {
				print_r($this->input);
				die();
			}

		}

		if (isset($controller)) {
			$controller->setState();
			$this->errors = $controller->getErrors();
			$this->messages = $controller->getMessages();
		}

	}

	public function getErrors() {
		return $this->errors;
	}

	public function getMessages() {
		return $this->messages;
	}

}

?>