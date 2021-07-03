<?php

final class MessageViewController {

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

	public function getView() {

		if ($this->loc[0] == 'message') {

			$view = new MessageView();

			if ($this->loc[1] == 'draft') {
				return $view->messageDraft();
			}

			if ($this->loc[1] == 'read' && ctype_digit($this->loc[2])) {
				$messageID = $this->loc[2];
				return $view->messageRead($messageID);
			}

			if ($this->loc[1] == 'confirm-delete' && ctype_digit($this->loc[2])) {
				$messageID = $this->loc[2];
				return $view->messageConfirmDelete($messageID);
			}

			$arg = new MessageInboxParameters();
			$arg->userID = $_SESSION['userID'];

			return $view->messageInbox($arg);

		}

		if (isset($v)) {
			return $v->getView();
		} else {
			$url = '/' . Lang::prefix();
			header("Location: $url" );
		}

	}

}

?>