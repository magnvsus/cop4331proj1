-- Use the team's official database
CREATE DATABASE IF NOT EXISTS COP4331;
USE COP4331;

-- Michael's Users Table (Left untouched so the Login API doesn't break)
CREATE TABLE Users (
    ID INT NOT NULL AUTO_INCREMENT,
    FirstName VARCHAR(50) NOT NULL DEFAULT '',
    LastName VARCHAR(50) NOT NULL DEFAULT '',
    Login VARCHAR(50) NOT NULL DEFAULT '',
    Password VARCHAR(50) NOT NULL DEFAULT '',
    PRIMARY KEY (ID)
) ENGINE = InnoDB;

-- My Relational Contacts Table
CREATE TABLE Contacts (
    ContactID INT NOT NULL AUTO_INCREMENT,
    UserID INT NOT NULL,
    FirstName VARCHAR(50) NOT NULL DEFAULT '',
    LastName VARCHAR(50) NOT NULL DEFAULT '',
    Phone VARCHAR(50) NOT NULL DEFAULT '',
    Email VARCHAR(50) NOT NULL DEFAULT '',
    DateCreated DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (ContactID),
    FOREIGN KEY (UserID) REFERENCES Users(ID) ON DELETE CASCADE
) ENGINE = InnoDB;
