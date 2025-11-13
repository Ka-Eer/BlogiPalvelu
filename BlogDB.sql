CREATE DATABASE blogitekstit 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;
use blogitekstit;

CREATE TABLE `blogit` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Pvm` date NOT NULL,
  `Otsikko` varchar(100) NOT NULL,
  `Teksti` text NOT NULL,
  `Kuva` mediumblob DEFAULT NULL,
  `Tykkaykset` int DEFAULT 0,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci