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

		$input = $this->input;

		if ($this->loc[0] == 'message') {

			if (!Auth::isLoggedIn()) {

				$loginURL = '/' . Lang::prefix() . 'login/';
				header("Location: $loginURL");

			}

			if ($this->loc[1] == 'draft' && isset($input['message-draft-submit'])) {

				if (empty($input['userID'])) { $this->errors[] = array('userID' => 'userMustBeSelected'); }

				if (empty($this->errors)) {

					// create message
					$message = new Message();
					$message->messageSubject = $input['messageSubject'];
					$message->messageContent = $input['messageContent'];
					$message->messageStatus = 'sent';
					$messageID = Message::insert($message, true, 'message_');

					// add sender participant
					$sender = new Participant($_SESSION['userID'], $messageID);
					$sender->readState = 'opened';
					Participant::insert($sender, false, 'message_');

					// add other participant(s)
					if ($input['userID'] != $_SESSION['userID']) { // not required is sending to self
						$sender = new Participant($input['userID'], $messageID);
						Participant::insert($sender, false, 'message_');
					}

					$successURL = '/' . Lang::prefix() . 'message/';
					header("Location: $successURL");

				}

			}

			if ($this->loc[1] == 'read' && is_numeric($this->loc[2])) {

				// mark all messages in thread as read

				$messageID = $this->loc[2];

				$arg = new ParticipantListParameters();
				$arg->participantUserID = $_SESSION['userID'];
				$arg->messageID = $messageID;
				$arg->readState = 'unopened';
				$pl = new ParticipantList($arg);
				$listOfUnopenedThreads = $pl->participants();

				if (!empty($listOfUnopenedThreads) && empty($this->errors)) {

					foreach ($listOfUnopenedThreads AS $participantData) {

						$participant = new Participant($participantData['participantUserID'], $participantData['messageID']);
						$participant->readState = 'opened';
						$cond = array('participantUserID' => $participantData['participantUserID'], 'messageID' => $participantData['messageID']);
						Participant::update($participant, $cond, true, false, 'message_');

					}

				}

			}

			if ($this->loc[1] == 'read' && is_numeric($this->loc[2]) && isset($input['message-reply-submit'])) {

				$messageParentID = $this->loc[2];
				if (empty($this->errors)) {

					// add message to thread
					$message = new Message();
					$message->messageParentID = $messageParentID;
					$message->messageContent = $input['messageContent'];
					$message->messageStatus = 'sent';
					Message::insert($message, false, 'message_');

					// mark thread as "unopened" for all other participants
					$arg = new ParticipantListParameters();
					$arg->messageID = $messageParentID;
					$arg->readState = 'opened';
					$pl = new ParticipantList($arg);
					$participants = $pl->participants();

					foreach ($participants AS $participantData) {
						if ($participantData['participantUserID'] != $_SESSION['userID']) {
							$participant = new Participant($participantData['participantUserID'], $message->messageParentID);
							$participant->readState = 'unopened';
							$cond = array('participantUserID' => $participantData['participantUserID'], 'messageID' => $message->messageParentID);
							Participant::update($participant, $cond, true, false, 'message_');
						}
					}

				}

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