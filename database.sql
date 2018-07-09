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
    birthMonth VARCHAR(255),
    birthYear INT,
    activateHash VARCHAR(255),
    activatedAccount INT,
    created_at DATE,
    updated_at DATE,
	PRIMARY KEY(id)
);

DROP TABLE IF EXISTS Directori;
-- SELECT * FROM Directori;

CREATE TABLE Directori(
	id INT NOT NULL AUTO_INCREMENT,
	nomCarpeta VARCHAR(255),
	isRoot BOOLEAN,
	carpetaParent INT,
	urlPath VARCHAR(255),
	esCarpeta BOOLEAN,
	esShared BOOLEAN,
    id_propietari INT,
	PRIMARY KEY(id)
);

DROP TABLE IF EXISTS UserCarpeta;

-- SELECT * FROM UserCarpeta;

CREATE TABLE UserCarpeta(
	id_usuari INT,
	id_carpeta INT,
    admin BOOLEAN,
    reader BOOLEAN,
	FOREIGN KEY (id_usuari) REFERENCES User(id),
    FOREIGN KEY (id_carpeta) REFERENCES Directori(id) ON DELETE CASCADE,
	PRIMARY KEY(id_usuari, id_carpeta)
);

DROP TABLE IF EXISTS SharedUserCarpeta;

CREATE TABLE SharedUserCarpeta(
	id_usuari INT,
	id_carpeta INT,
    admin BOOLEAN,
    reader BOOLEAN,
	FOREIGN KEY (id_usuari) REFERENCES User(id),
    FOREIGN KEY (id_carpeta) REFERENCES Directori(id),
	PRIMARY KEY(id_usuari, id_carpeta)
);


DROP TABLE IF EXISTS UserNotification;

CREATE TABLE UserNotification(
	id_notificacio INT NOT NULL AUTO_INCREMENT,
	id_usuari INT,
    title VARCHAR(255),
    message VARCHAR(255),
    time_sent DATE,
    FOREIGN KEY (id_usuari) REFERENCES User(id),
	PRIMARY KEY(id_notificacio)
);

SELECT * FROM User;
SELECT * FROM Directori;
SELECT * FROM SharedUserCarpeta;
DELETE FROM SharedUserCarpeta WHERE id_c = 10;

use PWBOX;





DELETE suc, d FROM SharedUserCarpeta as suc, Directori AS d WHERE suc.id_carpeta = d.id AND d.id_propietari = 1;


SET SQL_SAFE_UPDATES = 0;

