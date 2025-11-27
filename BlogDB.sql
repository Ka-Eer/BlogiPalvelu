CREATE DATABASE blogipalvelu_db 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;
use blogipalvelu_db;


-- taulut


-- blogit-taulu
CREATE TABLE `blogit` (
  `blog_ID` int NOT NULL AUTO_INCREMENT,
  `Pvm` date NOT NULL,
  `Klo` time NOT NULL,
  `Otsikko` varchar(100) NOT NULL,
  `Teksti` text NOT NULL,
  `Kuva` mediumblob DEFAULT NULL,
  -- Poistettu Tykkaykset-sarake, tykkäykset lasketaan likes-taulusta
  PRIMARY KEY (`blog_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- tagit-taulu blogien tageille
CREATE TABLE `tagit` (
  `tag_ID` int NOT NULL,
  `tag_Nimi` varchar(50) NOT NULL,
  PRIMARY KEY (`tag_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Esitäytetyt tagit
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



-- Käyttäjät-taulu
CREATE TABLE `users` (
  `user_ID` int NOT NULL AUTO_INCREMENT,
  `kayttajaNimi` varchar(50) NOT NULL UNIQUE,
  `salasana` varchar(255) NOT NULL,
  PRIMARY KEY (`user_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




-- liitostaulut


-- Liitostaulu blogien ja tagien monesta moneen -suhteelle
CREATE TABLE `blog_tag` (
  `blog_ID` int NOT NULL,
  `tag_ID` int NOT NULL,
  PRIMARY KEY (`blog_ID`, `tag_ID`),
  FOREIGN KEY (`blog_ID`) REFERENCES blogit(`blog_ID`) ON DELETE CASCADE,
  FOREIGN KEY (`tag_ID`) REFERENCES tagit(`tag_ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Liitostaulu tykkäyksille (käyttäjä voi tykätä blogista kerran)
CREATE TABLE `likes` (
  `blog_ID` int NOT NULL,
  `user_ID` int NOT NULL,
  PRIMARY KEY (`blog_ID`, `user_ID`),
  FOREIGN KEY (`blog_ID`) REFERENCES blogit(`blog_ID`) ON DELETE CASCADE,
  FOREIGN KEY (`user_ID`) REFERENCES users(`user_ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


