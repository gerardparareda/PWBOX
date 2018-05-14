CREATE DATABASE PWBOX;
USE PWBOX;

DROP TABLE User;

CREATE TABLE User(
	id INT NOT NULL AUTO_INCREMENT,
	username VARCHAR(255),
    email VARCHAR(255),
    pass VARCHAR(255), -- Seran Hashes MD5
    birthDay INT,
    birthMonth Varchar(255),
    birthYear INT,
    created_at DATE,
    updated_at DATE,
	PRIMARY KEY(id)
);

INSERT INTO User(name) VALUES('hola');

SELECT * FROM User;