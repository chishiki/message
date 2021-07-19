<?php

final class MessageNavbarItemsView {

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

	public function itemsRight() {

		$h = '';

		if (Auth::isLoggedIn()) {

			$arg = new ParticipantListParameters();
			$arg->participantUserID = $_SESSION['userID'];
			$arg->readState = 'unopened';
			$pl = new ParticipantList($arg);
			$unopenedMessageCount = $pl->participantCount();

			$h = '<li id="navbar_message_link" class="nav-item">';
				$h .= '<a class="nav-link" href="/' . Lang::prefix() . 'message/">';
					$h .= Lang::getLang('messageNavLink');
					if ($unopenedMessageCount) {
						$h .= ' <span class="badge badge-pill badge-danger">' . $unopenedMessageCount . '</span>';
					}
				$h .= '</a>';
			$h .= '</li>';

		}

		return $h;


	}

	public function itemsLeft() {

		return '';

	}

}

?>