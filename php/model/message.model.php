<?php

/*

CREATE TABLE `message_Message` (
    `messageID` int(12) NOT NULL AUTO_INCREMENT,
    `messageParentID` int(12), -- message_Message.messageID (replies reference parent to form a thread)
    `siteID` int(12) NOT NULL,
    `creator` int(12) NOT NULL,
    `created` datetime NOT NULL,
    `updated` datetime,
    `deleted` int(1) NOT NULL,
    `messageSubject` varchar(255) NOT NULL,
    `messageContent` text NOT NULL,
    `messageStatus` varchar(10) NOT NULL,
    `messageMetaData` text NOT NULL,
    `messageSendDateTime` datetime,
    `messageSendIP` varchar(45),
    PRIMARY KEY (`messageID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

*/

final class Message extends ORM {

	public $messageID;
	public $messageParentID;
	public $siteID;
	public $creator; // sender
	public $created;
	public $updated;
	public $deleted;
	public $messageSubject;
	public $messageContent;
	public $messageStatus; // [draft|sent|deleted]
	public $messageMetaData; // JSON
	public $messageSendDateTime;
	public $messageSendIP;

	public function __construct($messageID = null) {

		$dt = new DateTime();

		$this->messageID = null;
		$this->messageParentID = null;
		$this->siteID = $_SESSION['siteID'];
		$this->creator = $_SESSION['userID'];
		$this->created = $dt->format('Y-m-d H:i:s');
		$this->updated = null;
		$this->deleted = 0;
		$this->messageSubject = '';
		$this->messageContent = '';
		$this->messageStatus = 'draft'; // [draft|sent|deleted]
		$this->messageMetaData = '{}';
		$this->messageSendDateTime = $dt->format('Y-m-d H:i:s');
		$this->messageSendIP = $_SERVER['REMOTE_ADDR'];

		if ($messageID) {

			$nucleus = Nucleus::getInstance();

			$where = array();
			$where[] = 'siteID = :siteID';
			$where[] = 'deleted = 0';
			$where[] = 'messageID = :messageID';

			$query = 'SELECT * FROM message_Message WHERE ' . implode(' AND ', $where) . ' LIMIT 1';

			$statement = $nucleus->database->prepare($query);
			$statement->bindParam(':siteID', $_SESSION['siteID'], PDO::PARAM_INT);
			$statement->bindParam(':messageID', $messageID, PDO::PARAM_INT);
			$statement->execute();

			if ($row = $statement->fetch()) {
				foreach ($row AS $key => $value) { if (property_exists($this, $key)) { $this->$key = $value; } }
			}

		}

	}

	public function getParticipants() {

		$arg = new ParticipantListParameters();
		$arg->messageID = $this->messageID;
		$pl = new ParticipantList($arg);
		return $pl->participants();

	}

	public function markAsDeleted() {

		$dt = new DateTime();
		$this->updated = $dt->format('Y-m-d H:i:s');
		$this->deleted = 1;
		$this->messageStatus = 'deleted';
		$conditions = array('messageID' => $this->messageID);
		self::update($this, $conditions, true, false, 'message_');

	}

}

final class MessageList {

	private $messages;

	public function __construct(MessageListParameters $arg) {

		$this->messages = array();

		$where = array();
		$where[] = 'siteID = :siteID';
		$where[] = 'deleted = 0';

		if (!is_null($arg->messageID)) { $where[] = 'messageID = :messageID'; }
		if (!is_null($arg->creator)) { $where[] = 'creator = :creator'; }
		if (!is_null($arg->messageStatus)) { $where[] = 'messageStatus = :messageStatus'; }
		if (!is_null($arg->messageSendDateTime)) { $where[] = 'messageSendDateTime = :messageSendDateTime'; }
		if (!is_null($arg->messageSendIP)) { $where[] = 'messageSendIP = :messageSendIP'; }

		$orderBy = array();
		foreach ($arg->orderBy AS $field => $sort) { $orderBy[] = $field . ' ' . $sort; }

		switch ($arg->resultSet) {
			case 'robust': $selector = '*'; break;
			default: $selector = 'messageID';
		}

		$query = 'SELECT ' . $selector . ' FROM message_Message WHERE ' . implode(' AND ',$where) . ' ORDER BY ' . implode(', ',$orderBy);
		if (!is_null($arg->limit)) { $query .= ' LIMIT ' . (is_null($arg->offset)?$arg->offset.', ':'') . $arg->limit; }

		$nucleus = Nucleus::getInstance();
		$statement = $nucleus->database->prepare($query);
		$statement->bindParam(':siteID', $_SESSION['siteID'], PDO::PARAM_INT);

		if (!is_null($arg->messageID)) { $statement->bindParam(':messageID', $arg->messageID, PDO::PARAM_INT); }
		if (!is_null($arg->creator)) { $statement->bindParam(':creator', $arg->creator, PDO::PARAM_INT); }
		if (!is_null($arg->messageStatus)) { $statement->bindParam(':messageStatus', $arg->messageStatus, PDO::PARAM_STR); }
		if (!is_null($arg->messageSendDateTime)) { $statement->bindParam(':messageSendDateTime', $arg->messageSendDateTime, PDO::PARAM_STR); }
		if (!is_null($arg->messageSendIP)) { $statement->bindParam(':messageSendIP', $arg->messageSendIP, PDO::PARAM_STR); }

		$statement->execute();

		while ($row = $statement->fetch()) {
			switch ($arg->resultSet) {
				case 'robust': $this->messages[] = $row; break;
				default: $this->messages[] = $row['messageID'];
			}
		}

	}

	public function messages() {

		return $this->messages;

	}

	public function messageCount() {

		return count($this->messages);

	}

}

final class MessageListParameters {

	public $messageID;
	public $creator; // sender
	public $messageStatus; // [draft|sent|deleted]
	public $messageSendDateTime;
	public $messageSendIP;

	public $messageSearchString;

	public $resultSet;
	public $orderBy;
	public $limit;
	public $offset;

	public function __construct() {

		$this->messageID = null;
		$this->creator = null;
		$this->messageStatus = null;
		$this->messageSendDateTime = null;
		$this->messageSendIP = null;

		$this->resultSet = 'id'; // [id|robust]
		$this->orderBy = array('created' => 'DESC');
		$this->limit = null;
		$this->offset = null;

	}

}

?>