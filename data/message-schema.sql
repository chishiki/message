
DROP TABLE IF EXISTS `message_Message`;

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



DROP TABLE IF EXISTS `message_Participant`;

CREATE TABLE `message_Participant` (
    `userID` int(12) NOT NULL, -- perihelion_User.userID
    `messageID` int(12) NOT NULL, -- message_Message.messageID
    `siteID` int(12) NOT NULL,
    `creator` int(12) NOT NULL,
    `created` datetime NOT NULL,
    `updated` datetime,
    `deleted` int(1) NOT NULL,
    `role` varchar(10) NOT NULL, -- [sender|recipient]
    `readState` varchar(8) NOT NULL, -- [opened|unopened]
    `flag` varchar(8), -- [null|spam|starred]
    PRIMARY KEY (`userID`, `messageID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
