DROP DATABASE PWBOX;

CREATE DATABASE PWBOX;
USE PWBOX;

DROP TABLE IF EXISTS User;

CREATE TABLE User(
	id INT NOT NULL AUTO_INCREMENT,
	username VARCHAR(255),
    email VARCHAR(255),
    pass VARCHAR(255), -- Seran Hashes MD5
    birthDay INT,
    birthMonth Varchar(255),
    birthYear INT,
    activatedAccount INT,
    created_at DATE,
    updated_at DATE,
	PRIMARY KEY(id)
);

DROP TABLE IF EXISTS Directori;

CREATE TABLE Directori(
	id INT NOT NULL AUTO_INCREMENT,
	nomCarpeta VARCHAR(255),
	isRoot BOOLEAN,
  carpetaParent INT,
  urlPath VARCHAR(255),
  esCarpeta BOOLEAN,
  esShared BOOLEAN,
	PRIMARY KEY(id)
);

DROP TABLE IF EXISTS UserCarpeta;

CREATE TABLE UserCarpeta(
	id_usuari INT,
	id_carpeta INT,
    admin BOOLEAN,
    reader BOOLEAN,
	FOREIGN KEY (id_usuari) REFERENCES User(id),
    FOREIGN KEY (id_carpeta) REFERENCES Directori(id),
	PRIMARY KEY(id_usuari, id_carpeta)
);

CREATE TABLE UserNotification(
	id_notificacio INT NOT NULL AUTO_INCREMENT,
	id_usuari INT,
    title VARCHAR(255),
    message VARCHAR(255),
    time_sent DATE,
    FOREIGN KEY (id_usuari) REFERENCES User(id),
	PRIMARY KEY(id_notificacio)
);

SELECT title, message, time_sent FROM UserNotification WHERE id_usuari = 1;