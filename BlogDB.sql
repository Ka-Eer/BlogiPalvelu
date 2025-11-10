CREATE TABLE `blogit` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Otsikko` varchar(100) NOT NULL,
  `teksti` text NOT NULL,
  `kuva` mediumblob DEFAULT NULL,
  `tykkaykset` int DEFAULT 0,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci