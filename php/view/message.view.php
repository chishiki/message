<?php

final class MessageView {

	private $loc;
	private $input;
	private $modules;
	private $errors;
	private $messages;

	public function __construct($loc = array(), $input = array(), $modules = array(), $errors = array(), $messages = array()) {

		$this->loc = $loc;
		$this->input = $input;
		$this->modules = $modules;
		$this->errors = $errors;
		$this->messages = $messages;

	}

	public function messageInbox(MessageListParameters $arg) {

	}
	
	public function messageInboxList(MessageListParameters $arg) {

	}
	
	public function messageForm($type, $messageID = null) {

	}

}

?>