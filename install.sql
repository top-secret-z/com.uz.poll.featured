DROP TABLE IF EXISTS wcf1_poll_featured;
CREATE TABLE wcf1_poll_featured (
    featuredID            INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    actualPollID        INT(10),
    autoAdd                TINYINT(1) NOT NULL DEFAULT 1,
    frequency            SMALLINT(5) NOT NULL DEFAULT 30,
    isRandom            TINYINT(1) NOT NULL DEFAULT 1,
    nextChange            INT(10) NOT NULL DEFAULT 0,
    pollIDs                TEXT NOT NULL,
    width                SMALLINT(5) NOT NULL DEFAULT 90
};

ALTER TABLE wcf1_poll_featured ADD FOREIGN KEY (actualPollID) REFERENCES wcf1_poll (pollID) ON DELETE SET NULL;
