-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2024. Okt 28. 21:56
-- Kiszolgáló verziója: 10.4.32-MariaDB
-- PHP verzió: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `fitnessdb`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `dailycalories`
--

CREATE TABLE `dailycalories` (
  `id` int(11) NOT NULL,
  `TDEE` double NOT NULL,
  `burnedcalories` double NOT NULL,
  `calorieseaten` double NOT NULL,
  `dailysummacalories` double NOT NULL,
  `dailyfluid` double NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `foodtbl`
--

CREATE TABLE `foodtbl` (
  `id` int(11) NOT NULL,
  `mealname` varchar(50) NOT NULL,
  `calories` double DEFAULT NULL,
  `carb` double DEFAULT NULL,
  `protein` double DEFAULT NULL,
  `fat` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `foodtbl`
--

INSERT INTO `foodtbl` (`id`, `mealname`, `calories`, `carb`, `protein`, `fat`) VALUES
(1, 'Alkoholmentes bor', 6, 1.1, 0, 0.5),
(2, 'Alkoholmentes sör', 37, 8.1, 0.1, 0.2),
(3, 'Almabor', 56, 5.9, 0, 0),
(4, 'Félbarna kenyér', 246, 49, 8.2, 3.6),
(5, 'Fehér kenyér', 267, 49.6, 8.2, 3.6);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `mealtbl`
--

CREATE TABLE `mealtbl` (
  `Id` int(11) NOT NULL,
  `food` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `calories` double DEFAULT NULL,
  `carb` double DEFAULT NULL,
  `protein` double DEFAULT NULL,
  `fat` double DEFAULT NULL,
  `fluid` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `persondatatbl`
--

CREATE TABLE `persondatatbl` (
  `Id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `birthdate` date NOT NULL,
  `weight` double DEFAULT NULL,
  `height` double NOT NULL,
  `gender` tinyint(1) NOT NULL,
  `bodyfatpercentage` double NOT NULL,
  `FFM` double DEFAULT NULL,
  `BMI` double NOT NULL,
  `BMR` double NOT NULL,
  `TDEE` double NOT NULL,
  `weeklytraining` int(11) NOT NULL,
  `goalweight` double NOT NULL,
  `goaldate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `persondatatbl`
--

INSERT INTO `persondatatbl` (`Id`, `user_id`, `name`, `birthdate`, `weight`, `height`, `gender`, `bodyfatpercentage`, `FFM`, `BMI`, `BMR`, `TDEE`, `weeklytraining`, `goalweight`, `goaldate`) VALUES
(0, 9, 'Patrik', '2003-09-26', 76, 160, 0, 24.255, 57.5662, 29.6875, 1613.42992, 2420.14488, 6, 68, '2024-10-30');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `training`
--

CREATE TABLE `training` (
  `Id` int(11) NOT NULL,
  `trainingname` varchar(50) NOT NULL,
  `caloriesburn` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `training`
--

INSERT INTO `training` (`Id`, `trainingname`, `caloriesburn`) VALUES
(1, 'Aerobik - alacsony intenzitás', 5.4),
(2, 'Aerobik - közepes intenzitás ', 7.05),
(3, 'Aerobik - magas intenzitás', 7.6),
(4, 'Asztalitenisz (pingpong)', 4.35),
(5, 'Baseball', 5.4),
(6, 'Biciklizés - egykerekű', 5.4),
(7, 'Biciklizés - gyorstempó', 10.85),
(8, 'biciklizés - közepes tempó', 8.65),
(9, 'Biciklizés - lassú tempó', 6.5),
(10, 'Biciklizés - terep', 9.2),
(11, 'Biciklizés- verseny tempó', 13),
(12, 'Bírkózás', 6.5),
(13, 'Bowling', 3.25),
(14, 'Box', 13),
(15, 'Boxzsák ütögetés', 6.5),
(16, 'Búvárkodás', 7.6),
(17, 'Elliptikus tréner', 7.4),
(18, 'Evezés', 7.6),
(19, 'Evezőgép - gyors tempó', 9.2),
(20, 'Evezőgép - közepes tempó', 7.6),
(21, 'Evezőgép - lassú tempó', 3.8),
(22, 'Fallabda (Squash)', 13),
(23, 'Frizbizés', 3.25),
(24, 'Futás - gyors tempó', 16.25),
(25, 'Futás - lassú tempó', 8.65);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `trainingeventtbl`
--

CREATE TABLE `trainingeventtbl` (
  `Id` int(11) NOT NULL,
  `date` date NOT NULL,
  `duration` int(11) NOT NULL,
  `caloriesburned` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `user`
--

CREATE TABLE `user` (
  `Id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `user`
--

INSERT INTO `user` (`Id`, `username`, `email`, `password`) VALUES
(8, 'molnarka', 'molnarka@gmail.com', '1234'),
(9, 'teszt', '123123@valami', 'teszt'),
(10, 'SidyG', 'valami@valami', '12345'),
(12, 'proba1', 'proba1@proba.hu', 'proba'),
(13, 'valaki', 'vak@vakol', 'teszt');

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `dailycalories`
--
ALTER TABLE `dailycalories`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `foodtbl`
--
ALTER TABLE `foodtbl`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `mealtbl`
--
ALTER TABLE `mealtbl`
  ADD PRIMARY KEY (`Id`);

--
-- A tábla indexei `persondatatbl`
--
ALTER TABLE `persondatatbl`
  ADD KEY `user_id` (`user_id`);

--
-- A tábla indexei `training`
--
ALTER TABLE `training`
  ADD PRIMARY KEY (`Id`);

--
-- A tábla indexei `trainingeventtbl`
--
ALTER TABLE `trainingeventtbl`
  ADD PRIMARY KEY (`Id`);

--
-- A tábla indexei `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`Id`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `dailycalories`
--
ALTER TABLE `dailycalories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `foodtbl`
--
ALTER TABLE `foodtbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT a táblához `mealtbl`
--
ALTER TABLE `mealtbl`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `training`
--
ALTER TABLE `training`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT a táblához `trainingeventtbl`
--
ALTER TABLE `trainingeventtbl`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `user`
--
ALTER TABLE `user`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Megkötések a kiírt táblákhoz
--

--
-- Megkötések a táblához `persondatatbl`
--
ALTER TABLE `persondatatbl`
  ADD CONSTRAINT `persondatatbl_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`Id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
