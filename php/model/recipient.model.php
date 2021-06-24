<?php

/*

CREATE TABLE `message_Recipient` (
    `userID` int(12) NOT NULL, -- perihelion_User.userID
    `messageID` int(12) NOT NULL, -- message_Message.messageID
    `siteID` int(12) NOT NULL,
    `creator` int(12) NOT NULL,
    `created` datetime NOT NULL,
    `updated` datetime,
    `deleted` int(1) NOT NULL,
    `readState` varchar(8) NOT NULL, -- [opened|unopened]
    `flag` varchar(8), -- [null|spam|starred]
    PRIMARY KEY (`userID`, `messageID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

*/

final class Recipient extends ORM {

	public $userID;
	public $messageID;
	public $siteID;
	public $creator;
	public $created;
	public $updated;
	public $deleted;
	public $readState;
	public $flag;

	public function __construct($userID = null, $messageID = null) {

		$dt = new DateTime();

		$this->userID = $userID;
		$this->messageID = $messageID;
		$this->siteID = $_SESSION['siteID'];
		$this->creator = $_SESSION['userID'];
		$this->created = $dt->format('Y-m-d H:i:s');
		$this->updated = null;
		$this->deleted = 0;
		$this->readState = 'unopened';
		$this->flag = null;

		if ($userID && $messageID) {

			$nucleus = Nucleus::getInstance();

			$where = array();
			$where[] = 'siteID = :siteID';
			$where[] = 'deleted = 0';
			$where[] = 'userID = :userID';
			$where[] = 'messageID = :messageID';

			$query = 'SELECT * FROM message_Receipient WHERE ' . implode(' AND ', $where) . ' LIMIT 1';

			$statement = $nucleus->database->prepare($query);
			$statement->bindParam(':siteID', $_SESSION['siteID'], PDO::PARAM_INT);
			$statement->bindParam(':userID', $userID, PDO::PARAM_INT);
			$statement->bindParam(':messageID', $messageID, PDO::PARAM_INT);
			$statement->execute();

			if ($row = $statement->fetch()) {
				foreach ($row AS $key => $value) { if (property_exists($this, $key)) { $this->$key = $value; } }
			}

		}

	}

	public function markAsDeleted() {

		$dt = new DateTime();
		$this->updated = $dt->format('Y-m-d H:i:s');
		$this->deleted = 1;
		$conditions = array('userID' => $this->userID, 'messageID' => $this->messageID);
		self::update($this, $conditions, true, false, 'message_');

	}

}

final class RecipientList {

	private $messages;

	public function __construct(ReceipientListParameters $arg) {

		$this->messages = array();

		$where = array();
		$where[] = 'siteID = :siteID';
		$where[] = 'deleted = 0';

		if ($arg->messageID) { $where[] = 'messageID = :messageID'; }
		if ($arg->recipientID) { $where[] = 'recipientID = :recipientID'; }
		if ($arg->readState) { $where[] = 'readState = :readState'; }
		if ($arg->flag) { $where[] = 'flag = :flag'; }

		$orderBy = array();
		foreach ($arg->orderBy AS $field => $sort) { $orderBy[] = $field . ' ' . $sort; }

		switch ($arg->resultSet) {
			case 'robust': $selector = '*'; break;
			default: $selector = 'messageID';
		}

		$query = 'SELECT ' . $selector . ' FROM message_Recipient WHERE ' . implode(' AND ',$where) . ' ORDER BY ' . implode(', ',$orderBy);
		if ($arg->limit) { $query .= ' LIMIT ' . ($arg->offset?$arg->offset.', ':'') . $arg->limit; }

		$nucleus = Nucleus::getInstance();
		$statement = $nucleus->database->prepare($query);
		$statement->bindParam(':siteID', $_SESSION['siteID'], PDO::PARAM_INT);

		if ($arg->messageID) { $statement->bindParam(':messageID', $arg->messageID, PDO::PARAM_INT); }
		if ($arg->recipientID) { $statement->bindParam(':recipientID', $arg->recipientID, PDO::PARAM_INT); }
		if ($arg->readState) { $statement->bindParam(':readState', $arg->readState, PDO::PARAM_STR); }
		if ($arg->flag) { $statement->bindParam(':flag', $arg->flag, PDO::PARAM_STR); }

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

final class RecipientListParameters {

	public $messageID;
	public $recipientID;
	public $readState;
	public $flag;

	public $resultSet;
	public $orderBy;
	public $limit;
	public $offset;

	public function __construct() {

		$this->messageID = null;
		$this->recipientID = null;
		$this->readState = null;
		$this->flag = null;

		$this->resultSet = 'id'; // [id|robust]
		$this->orderBy = array('created' => 'ASC');
		$this->limit = null;
		$this->offset = null;

	}

}

?>