DROP TABLE IF EXISTS Member;
DROP TABLE IF EXISTS ChallengeQuestion;
DROP TABLE IF EXISTS Recipient;
DROP TABLE IF EXISTS SecretPage;
DROP TABLE IF EXISTS RecipientHistory;

CREATE TABLE Member(
username VARCHAR(25)  NOT NULL PRIMARY KEY,
password VARCHAR(255) NOT NULL,
email VARCHAR(50) NOT NULL
)ENGINE=InnoDB;

CREATE TABLE ChallengeQuestion(
challengeQuestionID INT UNSIGNED NOT NULL AUTO_INCREMENT,
username VARCHAR(25)  NOT NULL,
question VARCHAR(255) NOT NULL,
answer VARCHAR(255) NOT NULL,
PRIMARY KEY (challengeQuestionID),
FOREIGN KEY (username) REFERENCES Member(username)
) ENGINE=InnoDB;

CREATE TABLE Recipient(
recipientID INT UNSIGNED NOT NULL AUTO_INCREMENT,
email VARCHAR(50) NOT NULL,
PRIMARY KEY (recipientID)
)ENGINE=InnoDB;

CREATE TABLE SecretPage(
challengeQuestionID INT UNSIGNED NOT NULL,
recipientID INT UNSIGNED,
username VARCHAR(25)  NOT NULL,
title VARCHAR(50),
url VARCHAR(255) NOT NULL PRIMARY KEY,
message TEXT(500) NOT NULL,
ImagePath TEXT,
FOREIGN KEY (username) REFERENCES Member(username),
FOREIGN KEY (challengeQuestionID) REFERENCES ChallengeQuestion(challengeQuestionID),
FOREIGN KEY (recipientID) REFERENCES Recipient(recipientID)
)ENGINE=InnoDB;


CREATE TABLE RecipientHistory(
url VARCHAR(255) NOT NULL,
recipientID INT UNSIGNED NOT NULL,
hasViewed bool,
PRIMARY KEY (recipientID, url),
FOREIGN KEY (url) REFERENCES SecretPage(url),
FOREIGN KEY (recipientID) REFERENCES Recipient(recipientID)
)ENGINE=InnoDB;


SELECT * FROM Member;

