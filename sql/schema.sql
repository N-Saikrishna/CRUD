CREATE DATABASE IF NOT EXISTS COP4331
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE COP4331;

CREATE TABLE IF NOT EXISTS Users (
  ID          INT          NOT NULL AUTO_INCREMENT,
  FirstName   VARCHAR(50)  NOT NULL DEFAULT '',
  LastName    VARCHAR(50)  NOT NULL DEFAULT '',
  Login       VARCHAR(50)  NOT NULL,
  Password    VARCHAR(255) NOT NULL,
  DateCreated DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (ID),
  UNIQUE KEY uq_users_login (Login)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS Contacts (
  ID          INT          NOT NULL AUTO_INCREMENT,
  UserID      INT          NOT NULL,
  FirstName   VARCHAR(50)  NOT NULL DEFAULT '',
  LastName    VARCHAR(50)  NOT NULL DEFAULT '',
  Phone       VARCHAR(25)  NOT NULL DEFAULT '',
  Email       VARCHAR(100) NOT NULL DEFAULT '',
  DateCreated DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (ID),
  KEY idx_contacts_user (UserID),
  KEY idx_contacts_user_name (UserID, LastName, FirstName),
  CONSTRAINT fk_contacts_user
    FOREIGN KEY (UserID) REFERENCES Users(ID) ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS Sessions (
  Token     CHAR(64) NOT NULL,
  UserID    INT      NOT NULL,
  ExpiresAt DATETIME NOT NULL,
  PRIMARY KEY (Token),
  KEY idx_sessions_user (UserID),
  CONSTRAINT fk_sessions_user
    FOREIGN KEY (UserID) REFERENCES Users(ID) ON DELETE CASCADE
) ENGINE = InnoDB;
