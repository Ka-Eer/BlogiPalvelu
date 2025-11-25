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
  `tag_ID` int NOT NULL,
  `tag_Nimi` varchar(50) NOT NULL,
  
  PRIMARY KEY (`tag_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO tagit (tag_ID, tag_Nimi) VALUES 
(1, 'Pelit'),
(2, 'Matkustaminen'),
(3, 'Teknologia & internet'),
(4, 'Oppiminen & Itsekehitys'),
(5, 'Ruoka & Juoma'),
(6, 'Hyvinvointi & Elämäntyyli'),
(7, 'Luovuus & Kulttuuri'),
(8, 'Työ & Ura'),
(9, 'Koti & Arki'),
(10, 'Tee se itse & Projektit'),
(11, 'Ympäristö & Luonto'),
(12, 'Talous & Raha');
