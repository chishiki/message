<?php


final class MessageInbox {

	private $messages;

	public function __construct(MessageInboxParameters $arg) {

		$this->messages = array();

		$wheres = array();
		$wheres[] = 'message_Message.siteID = :siteID';
		$wheres[] = 'message_Message.deleted = 0';
		$wheres[] = 'message_Message.messageParentID IS NULL';
		if (!is_null($arg->userID)) { $wheres[] = 'message_Participant.participantUserID = :userID'; }
		if (!is_null($arg->messageStatus)) { $wheres[] = 'message_Message.messageStatus = :messageStatus'; }
		if (!is_null($arg->readState)) { $wheres[] = 'message_Participant.readState = :readState'; }
		if (!is_null($arg->flag)) { $wheres[] = 'message_Participant.readState = :messageSendDateTime'; }
		$where = implode(' AND ', $wheres);

		$selectorArray = array();
		foreach ($arg->resultSet AS $fieldAlias) { $selectorArray[] = $fieldAlias['field'] . ' AS ' . $fieldAlias['alias']; }
		$selector = implode(', ', $selectorArray);

		$orderByArray = array();
		foreach ($arg->orderBy AS $fieldSort) { $orderByArray[] = $fieldSort['field'] . ' ' . $fieldSort['sort']; }
		$orderBy = implode(', ',$orderByArray);

		$query = 'SELECT ' . $selector . ' FROM message_Participant ';
		$query .= 'LEFT JOIN message_Message ON message_Participant.messageID = message_Message.messageID ';
		$query .= 'WHERE ' . $where . ' ORDER BY ' . $orderBy;
		if (!is_null($arg->limit)) { $query .= ' LIMIT ' . (is_null($arg->offset)?$arg->offset.', ':'') . $arg->limit; }

		// print_r($query);

		$nucleus = Nucleus::getInstance();
		$statement = $nucleus->database->prepare($query);
		$statement->bindParam(':siteID', $_SESSION['siteID'], PDO::PARAM_INT);

		if (!is_null($arg->userID)) { $statement->bindParam(':userID', $arg->userID, PDO::PARAM_INT); }
		if (!is_null($arg->messageStatus)) { $statement->bindParam(':messageStatus', $arg->messageStatus, PDO::PARAM_STR); }
		if (!is_null($arg->readState)) { $statement->bindParam(':readState', $arg->readState, PDO::PARAM_STR); }
		if (!is_null($arg->flag)) { $statement->bindParam(':flag', $arg->flag, PDO::PARAM_STR); }

		$statement->execute();

		while ($row = $statement->fetch()) {
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

		$this->resultSet = array(
			array('field' => 'message_Message.messageID', 'alias' => 'messageID'),
			array('field' => 'message_Message.messageSendDateTime', 'alias' => 'messageSendDateTime'),
			array('field' => 'message_Participant.flag', 'alias' => 'flag'),
			array('field' => 'message_Participant.readState', 'alias' => 'readState'),
			array('field' => 'message_Message.messageSubject', 'alias' => 'messageSubject'),
		);
		$this->orderBy = array(
			array('field' => 'message_Message.messageSendDateTime', 'sort' => 'DESC')
		);
		$this->limit = null;
		$this->offset = null;

	}

}


?>