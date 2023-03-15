-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 09, 2023 at 04:19 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `FoodEntryDB`
--

-- --------------------------------------------------------

--
-- Table structure for table `food_entries`
--

CREATE TABLE `food_entries` (
  `id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `food` varchar(255) DEFAULT NULL,
  `calories` int(11) DEFAULT NULL,
  `protein` float DEFAULT NULL,
  `carbs` float DEFAULT NULL,
  `fats` float DEFAULT NULL,
  `userID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `food_entries`
--

INSERT INTO `food_entries` (`id`, `date`, `food`, `calories`, `protein`, `carbs`, `fats`, `userID`) VALUES
(1, '2023-03-08', 'Chicken Breast', 234, 34, 2, 5, 130),
(6, '2023-03-08', 'Pizza', 300, 32, 55, 22, 131),
(7, '2023-03-08', 'Hotdog', 300, 22, 33, 23, NULL),
(8, '2023-03-08', 'celery', 1, 2, 3, 4, NULL);

-- --------------------------------------------------------
--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `weight` decimal(10,2) DEFAULT NULL,
  `height` decimal(10,2) DEFAULT NULL,
  `age` int(255) DEFAULT NULL,
  `gender` char(6) DEFAULT NULL,
  `activity_level` varchar(255) DEFAULT NULL,
  `goal` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `weight`, `height`, `age`, `gender`, `activity_level`, `goal`) VALUES
(130, 'CatDaddy', 'Admin#1', 'cat@uga.edu', '107', '1.83', '44', 'Male', 'MedHighActivity', 'LoseWeight'),
(131, 'Tree', 'Admin#1', 'tree@me.com', '80', '1.68', '45', 'Female', 'MedLowActivity', 'LoseWeight');


--
-- Database Table: Food Entry DB
--

-- --------------------------------------------------------
--
-- Table structure for table `Recommended Values`
--

CREATE TABLE `recommended_values` (
  `username` varchar(50) NOT NULL,
  `fats` int(11) DEFAULT NULL,
  `carbs` int(11) DEFAULT NULL,
  `proteins` int(11) DEFAULT NULL,
  `calories` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Providing recommended macro levels for default users (i.e., the creators)
--

INSERT INTO `recommended_values` (`username`, `fats`, `carbs`, `proteins`, `calories`) VALUES
('CatDaddy', 40, 60, 50, 2000),
('Tree', 43, 68, 55, 2200);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `food_entries`
--
ALTER TABLE `food_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_userID` (`userID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `recommended_values`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `food_entries`
--
ALTER TABLE `food_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `food_entries`
--
ALTER TABLE `food_entries`
  ADD CONSTRAINT `fk_userID` FOREIGN KEY (`userID`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
