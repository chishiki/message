<?php


final class MessageInbox {

	private $messages;

	public function __construct(MessageInboxParameters $arg) {

		$this->messages = array();

		$where = array();
		$where[] = 'message_Message.siteID = :siteID';
		$where[] = 'message_Message.deleted = null';
		$where[] = 'message_Message.messageParentID = 0';

		if (!is_null($arg->userID)) { $where[] = '(message_Message.creator = :userID OR message_Participant.userID = :userID)'; }
		if (!is_null($arg->messageStatus)) { $where[] = 'message_Message.messageStatus = :messageStatus'; }
		if (!is_null($arg->readState)) { $where[] = 'message_Participant.readState = :readState'; }
		if (!is_null($arg->flag)) { $where[] = 'message_Participant.readState = :messageSendDateTime'; }

		$orderBy = array();
		foreach ($arg->orderBy AS $field => $sort) { $orderBy[] = $field . ' ' . $sort; }

		/*
		switch ($arg->resultSet) {
			case 'robust': $selector = '*'; break;
			default: $selector = 'messageID';
		}
		*/

		$query = 'SELECT * FROM message_Participant LEFT JOIN message_Message ON message_Participant.messageID = message_Message.messageID ';
		$query .= 'WHERE ' . implode(' AND ',$where) . ' ORDER BY ' . implode(', ',$orderBy);
		if (!is_null($arg->limit)) { $query .= ' LIMIT ' . (is_null($arg->offset)?$arg->offset.', ':'') . $arg->limit; }

		$nucleus = Nucleus::getInstance();
		$statement = $nucleus->database->prepare($query);
		$statement->bindParam(':siteID', $_SESSION['siteID'], PDO::PARAM_INT);

		if (!is_null($arg->userID)) { $statement->bindParam(':userID', $arg->userID, PDO::PARAM_INT); }
		if (!is_null($arg->messageStatus)) { $statement->bindParam(':messageStatus', $arg->messageStatus, PDO::PARAM_STR); }
		if (!is_null($arg->readState)) { $statement->bindParam(':readState', $arg->readState, PDO::PARAM_STR); }
		if (!is_null($arg->flag)) { $statement->bindParam(':flag', $arg->flag, PDO::PARAM_STR); }

		$statement->execute();

		while ($row = $statement->fetch()) {
			/*
			switch ($arg->resultSet) {
				case 'robust': $this->messages[] = $row; break;
				default: $this->messages[] = $row['messageID'];
			}
			*/
			$this->messages[] = $row;
		}

	}

	public function messages() {

		return $this->messages;

	}

	public function messageCount() {

		return count($this->messages);

	}

}

final class MessageInboxParameters {

	public $userID; // inbox owner
	public $messageStatus; // [draft|sent|deleted]
	public $readState;
	public $flag;

	public $messageSearchString;

	public $resultSet;
	public $orderBy;
	public $limit;
	public $offset;

	public function __construct() {

		$this->userID = null;
		$this->messageStatus = null;
		$this->readState = null;
		$this->flag = null;
		$this->messageSearchString = null;

		$this->resultSet = 'id'; // [id|robust]
		$this->orderBy = array('message_Message.messageSendDateTime' => 'DESC');
		$this->limit = null;
		$this->offset = null;

	}

}


?>