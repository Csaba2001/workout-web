-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 28, 2022 at 10:48 PM
-- Server version: 5.5.68-MariaDB
-- PHP Version: 7.2.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pixel`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `CategoryID` int(11) NOT NULL,
  `CategoryName` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`CategoryID`, `CategoryName`) VALUES
(3, 'Erősítés'),
(1, 'Fogyás'),
(2, 'Szálkásítás');

-- --------------------------------------------------------

--
-- Table structure for table `exercises`
--

CREATE TABLE `exercises` (
  `ExerciseID` int(11) NOT NULL,
  `ExerciseName` varchar(30) NOT NULL,
  `Description` varchar(100) NOT NULL,
  `TrainerID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `exercises`
--

INSERT INTO `exercises` (`ExerciseID`, `ExerciseName`, `Description`, `TrainerID`) VALUES
(54, 'Fekvőtámasz 15x10', '', 83),
(51, 'Fekvőtámasz 20x5', '', 84),
(22, 'Fekvőtámasz 5x25', '', 1),
(27, 'Futás', '30perc futás', 1),
(49, 'Futás', '60perc futás', 1),
(56, 'Futás 120 perc', '', 82),
(52, 'Futás 50 perc', '', 84),
(55, 'Futás 90 perc', '', 82),
(53, 'Guggolás 10x10', '', 83),
(50, 'Guggolás 10x5', '', 84),
(21, 'Guggolás 5x10', 'https://www.youtube.com/watch?v=bmV_9Xwg8_4', 1),
(23, 'Hasizom 5x30', '', 1),
(0, 'Pihenés', 'Pihenés', 0);

-- --------------------------------------------------------

--
-- Table structure for table `persons`
--

CREATE TABLE `persons` (
  `PersonID` int(11) NOT NULL,
  `LastName` varchar(30) NOT NULL,
  `FirstName` varchar(30) NOT NULL,
  `Phone` varchar(40) NOT NULL,
  `Email` varchar(254) NOT NULL,
  `Hash` varchar(60) NOT NULL,
  `VerifyCode` varchar(60) DEFAULT NULL,
  `RegistrationExpires` datetime DEFAULT NULL,
  `NewPassword` varchar(60) DEFAULT NULL,
  `CodePassword` char(40) DEFAULT NULL,
  `NewPasswordExpires` datetime DEFAULT NULL,
  `Verified` enum('pending','verified') NOT NULL DEFAULT 'pending',
  `Rank` enum('user','trainer','admin') NOT NULL DEFAULT 'user',
  `Status` enum('active','banned') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `persons`
--

INSERT INTO `persons` (`PersonID`, `LastName`, `FirstName`, `Phone`, `Email`, `Hash`, `VerifyCode`, `RegistrationExpires`, `NewPassword`, `CodePassword`, `NewPasswordExpires`, `Verified`, `Rank`, `Status`) VALUES
(0, '', '', '', '', '', NULL, '2022-08-11 20:24:42', NULL, NULL, '2022-08-11 20:24:42', 'pending', 'trainer', 'banned'),
(1, 'Fenyvesi', 'Mátyás', '+381637346025', 'matyas@gmail.com', '$2y$10$FFWSgpM1P1mEMvxqC93E.ufEPUhJII/LvN/aiujb8Km6LxLNAMNH2', '123', '2022-05-16 21:20:36', NULL, NULL, NULL, 'verified', 'trainer', 'active'),
(2, 'Kiss', 'Máté', '0650636454', 'mate@gmail.com', '$2y$10$M.Ei6MlwrhN/ULvacXQ/EuBKdvSf2it1zHzBj5Qqyj6HePIKFEDMe', '321', '2022-05-18 19:28:58', NULL, NULL, NULL, 'verified', 'user', 'active'),
(9, 'Nagy', 'Virág', '0652658459', 'virag@gmail.com', '$2y$10$BZTSTQHJvL3ixiG7eKiAou/pJy9fxgSGT15ybr4V19XqQVSTPZDxi', '4ef9e3a44f11d5b56556', '2022-08-08 18:22:08', NULL, NULL, NULL, 'verified', 'user', 'banned'),
(11, 'Koncz', 'Kata', '025645786', 'kata@gmail.com', '$2y$10$di.O3Ej9LwUdDGkFBzDdjOpxMSfJtzQwEuaTs.L.9wAZ7mBEyK.mS', '224eaadb32c4ffee89b9', '2022-08-10 16:58:39', NULL, NULL, NULL, 'verified', 'trainer', 'active'),
(20, 'Szűcs', 'György', '0000000000', 'gyorgy@gmail.com', '$2y$10$K9Zf92PnB4f8WLDhT33I4OoSrnmsbYH8dD4.ganLHG5ccOkBSLLFa', '4bef3e34ac1e844dc15a', '2022-08-12 01:10:12', NULL, NULL, NULL, 'verified', 'trainer', 'active'),
(22, 'Németh', 'Dorina', '11111111111', 'dorina@gmail.com', '$2y$10$mPrzvJkKfT1OAEG2dph7eua9LdC7whbs9ngKay1z4wYe7hKU3k3SK', '47273c586595097cd60c', '2022-08-12 01:25:13', NULL, NULL, NULL, 'verified', 'trainer', 'active'),
(25, 'Boja', 'Bálint', '0637345144', 'balint@gmail.com', '$2y$10$t8VCI1gTyAsedorvvb/Cc.yq2nhVgEbyNStPBqnNc7VGGJqGdDkPi', '40740a4dbbe8c83829d7', '2022-08-18 16:54:11', NULL, NULL, NULL, 'verified', 'user', 'banned'),
(67, 'Vörös', 'Kornél', '0001112223', 'kornel@gmail.com', '$2y$10$vdRMU6PdEAmLxmW0UjE00.h9vOEdVYghXcY5i2W3Ce3JVX5sa2aT6', '332edb2e2adeedd5889b', '2022-08-18 18:45:04', NULL, NULL, NULL, 'verified', 'user', 'active'),
(74, 'Csaba', 'Dobó', '0100000000', 'admin@admin.admin', '$2a$12$H/lEOD6PVVtwBTTALICfSuxW2afgfkP/QT65OjrGfhMM4ZDo8Mhgi', NULL, '2022-08-19 20:09:04', NULL, NULL, NULL, 'verified', 'admin', 'active'),
(76, 'Szücs', 'Dórián', '0666666666', 'dorian@gmail.com', '$2y$10$KIlHRFqCrqgCBSRjzSf3teZhpfKRC6.M4zUunV5zn1RbylXzmslMq', 'acf3d970b8522aaf6a42', '2022-08-21 16:49:21', '$2y$10$Q/4KZQUopNqjk4Qo3R4yrOIZihuKIEs/ExFKL8LmsFcVLtqnWIEeS', '7f4e059ab6ae147ee8e2', '2022-08-21 17:00:31', 'verified', 'user', 'active'),
(79, 'Dobó', 'Csaba', '000000000', 'dobocsaba13@gmail.com', '$2y$10$LkOFbWRX7fsSwHLnqnjXwet3vkB73djESLYbU9LU7oPgGFZ0l4fxa', 'f8aee8767cd7a6ce9960', '2022-08-22 22:28:03', NULL, NULL, NULL, 'verified', 'user', 'active'),
(80, 'Kovács', 'Gábor', '066666666', 'smith@smithworks.dev', '$2y$10$URb9zwr4rvEP.su/BtX0.uLy8mMQhaRLdHWMd2T2ARWQVLWaT2XMm', '55838e713321457adc1e', '2022-08-23 13:25:57', NULL, NULL, NULL, 'verified', 'user', 'active'),
(81, 'boja', 'balint', '123456789', 'bojaboci@gmail.com', '$2y$10$U8xOrtIRP8YVyoiAE2ehwemocrMFBxiYS63rdZH44MSDvUkkroekC', 'a06772b5be2c5e80190e', '2022-08-29 21:35:30', NULL, NULL, NULL, 'verified', 'user', 'active'),
(82, 'Sági', 'Bendegúz', '064545675', 'bendeguz@gmail.com', '$2y$10$7iSnNES1qQAg5mZGbczGd.BNXk2E.mxhp7W3tU5yg1F5N23iVH78.', '4f7fcc450a80e9f9f5a7', '2022-08-29 22:05:13', NULL, NULL, NULL, 'verified', 'trainer', 'active'),
(83, 'Földessy', 'Krisztina', '012345678', 'krisztina@gmail.com', '$2y$10$6LbMpkmERtSiWgjtYfr9Ze.40Rj8kyld9WBITiZzCVIQOUavzZZkq', '5661e4030a011dfd8cd2', '2022-08-29 22:14:57', NULL, NULL, NULL, 'verified', 'trainer', 'active'),
(84, 'Fehérvári', 'Dániel', '087456123', 'daniel@gmail.com', '$2y$10$2bOq0mHklajvRQo/IDTAZOQg.ICaMLk.FT1S2x1OiKcdC5yTPnmsm', 'daac658f32bb404b6869', '2022-08-29 22:26:23', NULL, NULL, NULL, 'verified', 'trainer', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `persons_trainers_rating`
--

CREATE TABLE `persons_trainers_rating` (
  `TrainerID` int(11) NOT NULL,
  `PersonID` int(11) NOT NULL,
  `Rating` enum('1','2','3','4','5') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Triggers `persons_trainers_rating`
--
DELIMITER $$
CREATE TRIGGER `trainerUpdateRated` BEFORE INSERT ON `persons_trainers_rating` FOR EACH ROW UPDATE trainers SET trainers.rated = (SELECT COUNT(new.Rating) FROM persons_trainers_rating WHERE persons_trainers_rating.TrainerID = new.TrainerID) WHERE trainers.TrainerID = new.TrainerID
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trainersUpdateRating` AFTER INSERT ON `persons_trainers_rating` FOR EACH ROW UPDATE trainers SET trainers.rating = (SELECT AVG(persons_trainers_rating.Rating) FROM persons_trainers_rating WHERE persons_trainers_rating.TrainerID = new.TrainerID) WHERE trainers.TrainerID = new.TrainerID
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `persons_trainings`
--

CREATE TABLE `persons_trainings` (
  `ID` int(11) NOT NULL,
  `PersonID` int(11) NOT NULL,
  `TrainingID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Triggers `persons_trainings`
--
DELIMITER $$
CREATE TRIGGER `UpdatePicked` AFTER INSERT ON `persons_trainings` FOR EACH ROW UPDATE trainings SET picked = picked+1 WHERE TrainingID=new.TrainingID
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `trainers`
--

CREATE TABLE `trainers` (
  `TrainerID` int(11) NOT NULL,
  `CV` varchar(2000) NOT NULL,
  `rated` int(10) DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT NULL,
  `approval` enum('approved','pending') NOT NULL DEFAULT 'pending',
  `picture` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `trainers`
--

INSERT INTO `trainers` (`TrainerID`, `CV`, `rated`, `rating`, `approval`, `picture`) VALUES
(0, '', NULL, NULL, 'pending', NULL),
(1, 'Egy komplex állapotfelmérést követően egyénre szabott, változatos edzéstervet készítek a számodra, így minden edzés új élményeket és sikereket hoz. Ha bízol bennem, és megfogadod a tanácsaim, garantáltan elérheted a kitűzött céljaid.', NULL, NULL, 'approved', 'man-2604149_1920.jpg'),
(11, 'A legfontosabb, hogy megszerettessem az emberekkel a mozgást, és ne egy kötelező dolog legyen, amihez igazából semmi kedve. Segíteni szeretnék, hogy a vendégeim magabiztosabbak, vidámabbak, fittebbek legyenek és megmutassam, hogy a mozgás lehet öröm, kikapcsolódás és hogy a változásért nem is kell olyan sokat szenvedni, mint azt elsőre gondolnánk.\nEdzéseimen az erő, – és állóképesség fejlesztése mellett nagy hangsúlyt kap a koordináció és egyes képességek fejlesztése is. Továbbá funkcionális, saját testúlyos, gépi és szabad súlyos módszerekkel segítek neked a célod elérésében. Mindezt természetesen egy egyénre szabott edzéstervvel preventív körülmények között és egy szuper hangulattal ötvözve.', NULL, NULL, 'approved', 'abs-1850926_1920.jpg'),
(20, 'Célom, hogy segítsek az embereknek egészségük megőrzésében és fejlesztésében. Ha szeretnél fittebbé válni, ellensúlyozni az ülő munka káros hatásait vagy sportolóként szeretnél kiegészítő edzéssel hozzájárulni teljesítményed fokozásához, keress bátran. Hiszek abban, hogy egy optimista és pozitív hozzáállással remek eredményeket tudunk elérni.', NULL, NULL, 'pending', 'fitness-3502830_1920.jpg'),
(22, 'Személyi edzés óráimon a célom, hogy minél jobban megismerjem a vendégeimet és ezáltal egy olyan edzésprogramot állítsak össze számukra, amely az egyénre van formálva. Igyekszem, hogy élvezzétek az edzést, megszeressétek a mozgást. Ha fittebb, energikusabb és magabiztosabb életformára vágysz, ha jobban szeretnéd érezni magad a bőrödben, keress fel bátran! Én segítek Neked az általad kitűzött célok elérésében, legyen szó fogyásról, alakformálásról, izomépítésről vagy életmódváltásról.', NULL, NULL, 'pending', 'street-workout-2629179_1920.jpg'),
(82, 'A konzultációt és a felméréseket követően személyre szabott edzéstervvel, táplálkozás és életmódtanácsokkal kiegészítve kezdjük meg vendégeimmel a közös munkát, természetesen az egyéni céljaiknak megfelelően. Edzéseim során elengedhetetlennek tartom a jó hangulatot, továbbá hogy ne „muszáj”-ból, hanem „szeretet”-ből látogassák az órákat. Edzői szakértelmemmel és sportolói tapasztalataimmal célom, hogy utat mutassak vendégeimnek az egészséges, sportos élet irányába életkortól és nemtől függetlenül, mindezt úgy, hogy a valódi céljaiknak megfelelően testileg (és közben lelkileg egyaránt) fejlődjenek.', NULL, NULL, 'approved', 'street-workout-2629179_1920.jpg'),
(83, 'A mozgással és életmódváltással jelentős életminőség javulás érhető el, büszke vagyok arra, hogy a vendégeimmel megismertettem a mozgás örömét, és az egészséges életmód a mindennapi életük részévé vált.', NULL, NULL, 'approved', 'gym-girl-1391368_1920.jpg'),
(84, 'A vendégeimmel közösen a konzultáció alkalmával, a sport múltjukat és az aktuális életvitelüket figyelembe véve célokat tűzünk ki. Aztán egy átfogó állapotfelmérést követően, a megfelelő mobilitási alapot megteremtve, ráépítjük az alapvető mozgásmintákat, majd az erőnléti és állóképességet. Az edzésekbe bele csempészem a megfelelő korrekciókat is.\nIlletve fontosnak tartom a lehető legbiztonságosabb fejlődést és a jó hangulatot a folyamat egészében.', NULL, NULL, 'approved', 'street-workout-2682499_1920.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `trainings`
--

CREATE TABLE `trainings` (
  `TrainingID` int(11) NOT NULL,
  `TrainerID` int(11) DEFAULT NULL,
  `CategoryID` int(11) NOT NULL DEFAULT '0',
  `description` varchar(100) DEFAULT NULL,
  `Mon` int(11) DEFAULT NULL,
  `Tue` int(11) DEFAULT NULL,
  `Wed` int(11) DEFAULT NULL,
  `Thu` int(11) DEFAULT NULL,
  `Fri` int(11) DEFAULT NULL,
  `Sat` int(11) DEFAULT NULL,
  `Sun` int(11) DEFAULT NULL,
  `picked` int(11) NOT NULL DEFAULT '0',
  `status` enum('active','banned') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `trainings`
--

INSERT INTO `trainings` (`TrainingID`, `TrainerID`, `CategoryID`, `description`, `Mon`, `Tue`, `Wed`, `Thu`, `Fri`, `Sat`, `Sun`, `picked`, `status`) VALUES
(65, 0, 3, 'A hétvége pihenős', 22, 23, 21, 27, 27, 0, 0, 1, 'active'),
(66, 84, 1, 'Futós fogyás', 52, 52, 0, 50, 51, 52, 0, 0, 'active'),
(67, 83, 3, 'Fekvőtámaszos erősítés', 54, 53, 0, 54, 54, 54, 0, 2, 'active'),
(68, 83, 3, 'Guggolós erősítés', 53, 53, 0, 54, 53, 53, 0, 0, 'active'),
(69, 82, 1, 'Futás mindenek felett', 56, 55, 0, 56, 56, 0, 55, 0, 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`CategoryID`),
  ADD UNIQUE KEY `CategoryName` (`CategoryName`);

--
-- Indexes for table `exercises`
--
ALTER TABLE `exercises`
  ADD PRIMARY KEY (`ExerciseID`),
  ADD UNIQUE KEY `exercises_unique_index` (`ExerciseName`,`Description`,`TrainerID`),
  ADD KEY `TrainerID` (`TrainerID`);

--
-- Indexes for table `persons`
--
ALTER TABLE `persons`
  ADD PRIMARY KEY (`PersonID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `persons_trainers_rating`
--
ALTER TABLE `persons_trainers_rating`
  ADD PRIMARY KEY (`PersonID`,`TrainerID`),
  ADD KEY `TrainerID` (`TrainerID`);

--
-- Indexes for table `persons_trainings`
--
ALTER TABLE `persons_trainings`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `PersonsID` (`PersonID`),
  ADD KEY `TrainingsID` (`TrainingID`);

--
-- Indexes for table `trainers`
--
ALTER TABLE `trainers`
  ADD PRIMARY KEY (`TrainerID`);

--
-- Indexes for table `trainings`
--
ALTER TABLE `trainings`
  ADD PRIMARY KEY (`TrainingID`),
  ADD KEY `TrainerID` (`TrainerID`),
  ADD KEY `Mon` (`Mon`),
  ADD KEY `Sun` (`Sun`),
  ADD KEY `Fri` (`Fri`),
  ADD KEY `Thu` (`Thu`),
  ADD KEY `Wed` (`Wed`),
  ADD KEY `Tue` (`Tue`),
  ADD KEY `Sat` (`Sat`),
  ADD KEY `CategoryID` (`CategoryID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `CategoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `exercises`
--
ALTER TABLE `exercises`
  MODIFY `ExerciseID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `persons`
--
ALTER TABLE `persons`
  MODIFY `PersonID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `persons_trainings`
--
ALTER TABLE `persons_trainings`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `trainings`
--
ALTER TABLE `trainings`
  MODIFY `TrainingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `persons_trainers_rating`
--
ALTER TABLE `persons_trainers_rating`
  ADD CONSTRAINT `persons_trainers_rating_ibfk_1` FOREIGN KEY (`PersonID`) REFERENCES `persons_trainings` (`PersonID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `persons_trainers_rating_ibfk_2` FOREIGN KEY (`TrainerID`) REFERENCES `trainers` (`TrainerID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `persons_trainings`
--
ALTER TABLE `persons_trainings`
  ADD CONSTRAINT `persons_trainings_ibfk_1` FOREIGN KEY (`PersonID`) REFERENCES `persons` (`PersonID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `persons_trainings_ibfk_2` FOREIGN KEY (`TrainingID`) REFERENCES `trainings` (`TrainingID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `trainers`
--
ALTER TABLE `trainers`
  ADD CONSTRAINT `trainers_ibfk_1` FOREIGN KEY (`TrainerID`) REFERENCES `persons` (`PersonID`);

--
-- Constraints for table `trainings`
--
ALTER TABLE `trainings`
  ADD CONSTRAINT `trainings_ibfk_1` FOREIGN KEY (`TrainerID`) REFERENCES `trainers` (`TrainerID`),
  ADD CONSTRAINT `trainings_ibfk_10` FOREIGN KEY (`CategoryID`) REFERENCES `categories` (`CategoryID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `trainings_ibfk_3` FOREIGN KEY (`Mon`) REFERENCES `exercises` (`ExerciseID`) ON DELETE CASCADE,
  ADD CONSTRAINT `trainings_ibfk_4` FOREIGN KEY (`Tue`) REFERENCES `exercises` (`ExerciseID`) ON DELETE CASCADE,
  ADD CONSTRAINT `trainings_ibfk_5` FOREIGN KEY (`Wed`) REFERENCES `exercises` (`ExerciseID`) ON DELETE CASCADE,
  ADD CONSTRAINT `trainings_ibfk_6` FOREIGN KEY (`Thu`) REFERENCES `exercises` (`ExerciseID`) ON DELETE CASCADE,
  ADD CONSTRAINT `trainings_ibfk_7` FOREIGN KEY (`Fri`) REFERENCES `exercises` (`ExerciseID`) ON DELETE CASCADE,
  ADD CONSTRAINT `trainings_ibfk_8` FOREIGN KEY (`Sat`) REFERENCES `exercises` (`ExerciseID`) ON DELETE CASCADE,
  ADD CONSTRAINT `trainings_ibfk_9` FOREIGN KEY (`Sun`) REFERENCES `exercises` (`ExerciseID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
