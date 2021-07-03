<?php

/*

CREATE TABLE `message_Participant` (
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

final class Participant extends ORM {

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

			$query = 'SELECT * FROM message_Participant WHERE ' . implode(' AND ', $where) . ' LIMIT 1';

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

final class ParticipantList {

	private $participants;

	public function __construct(ParticipantListParameters $arg) {

		$this->participants = array();

		$wheres = array();
		$wheres[] = 'siteID = :siteID';
		$wheres[] = 'deleted = 0';
		if ($arg->messageID) { $wheres[] = 'message_Participant.messageID = :messageID'; }
		if ($arg->participantUserID) { $wheres[] = 'message_Participant.participantUserID = :participantUserID'; }
		if ($arg->readState) { $wheres[] = 'message_Participant.readState = :readState'; }
		if ($arg->flag) { $wheres[] = 'message_Participant.flag = :flag'; }
		$where = implode(' AND ',$wheres);

		$selectorArray = array();
		foreach ($arg->resultSet AS $fieldAlias) { $selectorArray[] = $fieldAlias['field'] . ' AS ' . $fieldAlias['alias']; }
		$selector = implode(', ', $selectorArray);

		$orderByArray = array();
		foreach ($arg->orderBy AS $fieldSort) { $orderByArray[] = $fieldSort['field'] . ' ' . $fieldSort['sort']; }
		$orderBy = implode(', ',$orderByArray);

		$query = 'SELECT ' . $selector . ' FROM message_Participant ';
		$query .= 'LEFT JOIN perihelion_User ON message_Participant.participantUserID = perihelion_User.userID ';
		$query .= 'WHERE ' . $where . ' ORDER BY ' . $orderBy;
		if ($arg->limit) { $query .= ' LIMIT ' . ($arg->offset?$arg->offset.', ':'') . $arg->limit; }

		$nucleus = Nucleus::getInstance();
		$statement = $nucleus->database->prepare($query);
		$statement->bindParam(':siteID', $_SESSION['siteID'], PDO::PARAM_INT);

		if ($arg->messageID) { $statement->bindParam(':messageID', $arg->messageID, PDO::PARAM_INT); }
		if ($arg->participantUserID) { $statement->bindParam(':participantUserID', $arg->participantUserID, PDO::PARAM_INT); }
		if ($arg->readState) { $statement->bindParam(':readState', $arg->readState, PDO::PARAM_STR); }
		if ($arg->flag) { $statement->bindParam(':flag', $arg->flag, PDO::PARAM_STR); }

		$statement->execute();

		while ($row = $statement->fetch()) {
			$this->participants[] = $row;
		}

	}

	public function participants() {

		return $this->participants;

	}

	public function participantCount() {

		return count($this->participants);

	}

}

final class ParticipantListParameters {

	public $messageID;
	public $participantUserID;
	public $readState;
	public $flag;

	public $otherParticipantsOnly;

	public $resultSet;
	public $orderBy;
	public $limit;
	public $offset;

	public function __construct() {

		$this->messageID = null;
		$this->participantUserID = null;
		$this->readState = null;

		$this->flag = null;

		$this->resultSet = array(
			array('field' => 'message_Participant.participantUserID', 'alias' => 'participantUserID'),
			array('field' => 'perihelion_User.userDisplayName', 'alias' => 'userDisplayName'),
			array('field' => 'message_Participant.role', 'alias' => 'role'),
			array('field' => 'message_Participant.readState', 'alias' => 'readState'),
			array('field' => 'message_Participant.flag', 'alias' => 'flag')
		);
		$this->orderBy = array(
			array('field' => 'perihelion_User.userDisplayName', 'sort' => 'ASC')
		);

		$this->limit = null;
		$this->offset = null;

	}

}

?>