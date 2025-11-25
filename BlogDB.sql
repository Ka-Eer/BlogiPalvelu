CREATE DATABASE blogitekstit 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;
use blogitekstit;

CREATE TABLE `blogit` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Pvm` date NOT NULL,
  `Klo` time NOT NULL,
  `Otsikko` varchar(100) NOT NULL,
  `Teksti` text NOT NULL,
  `Kuva` mediumblob DEFAULT NULL,
  `Tykkaykset` int DEFAULT 0,
  /* BlogTag tagit true/false */
  `BT1` boolean NOT NULL DEFAULT 0,
  `BT2` boolean NOT NULL DEFAULT 0,
  `BT3` boolean NOT NULL DEFAULT 0,
  `BT4` boolean NOT NULL DEFAULT 0,
  `BT5` boolean NOT NULL DEFAULT 0,
  `BT6` boolean NOT NULL DEFAULT 0,
  `BT7` boolean NOT NULL DEFAULT 0,
  `BT8` boolean NOT NULL DEFAULT 0,
  `BT9` boolean NOT NULL DEFAULT 0,
  `BT10` boolean NOT NULL DEFAULT 0,
  `BT11` boolean NOT NULL DEFAULT 0,
  `BT12` boolean NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `users` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `kayttajaNimi` varchar(50) NOT NULL UNIQUE,
  `salasana` varchar(255) NOT NULL,
  check(CHAR_LENGTH(salasana) BETWEEN 8 and 255),
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tagit` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Pelit` boolean NOT NULL DEFAULT 0,
  `Matkustaminen` boolean NOT NULL DEFAULT 0,
  `Teknologia` boolean NOT NULL DEFAULT 0,
  `Oppiminen` boolean NOT NULL DEFAULT 0,
  `Ruoka` boolean NOT NULL DEFAULT 0,
  `Hyvinvointi` boolean NOT NULL DEFAULT 0,
  `Luovuus` boolean NOT NULL DEFAULT 0,
  `Työ` boolean NOT NULL DEFAULT 0,
  `Koti` boolean NOT NULL DEFAULT 0,
  `Projektit` boolean NOT NULL DEFAULT 0,
  `Ympäristö` boolean NOT NULL DEFAULT 0,
  `Talous` boolean NOT NULL DEFAULT 0,
  
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;