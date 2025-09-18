-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Sep 03, 2025 at 03:25 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vitalize_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `enrolment_id` int(11) NOT NULL,
  `session_date` date NOT NULL,
  `present` tinyint(1) NOT NULL DEFAULT 0,
  `score` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enrolments`
--

CREATE TABLE `enrolments` (
  `id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `gymnast_id` int(11) NOT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrolments`
--

INSERT INTO `enrolments` (`id`, `program_id`, `gymnast_id`, `enrolled_at`) VALUES
(1, 1, 1, '2025-08-25 15:00:54'),
(2, 1, 2, '2025-08-27 15:11:26');

-- --------------------------------------------------------

--
-- Table structure for table `gymnasts`
--

CREATE TABLE `gymnasts` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `age` int(11) NOT NULL,
  `experience` enum('Beginner','Intermediate','Advanced') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gymnasts`
--

INSERT INTO `gymnasts` (`id`, `name`, `age`, `experience`, `created_at`) VALUES
(1, 'Keaobaka', 16, 'Beginner', '2025-08-25 15:00:54'),
(2, 'Neo', 11, 'Intermediate', '2025-08-27 15:11:26');

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `description` text NOT NULL,
  `coach` varchar(120) NOT NULL,
  `contact` varchar(120) NOT NULL,
  `duration_weeks` int(11) NOT NULL,
  `skill_level` enum('Beginner','Intermediate','Advanced') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `name`, `description`, `coach`, `contact`, `duration_weeks`, `skill_level`, `created_at`) VALUES
(1, 'Tumbling', 'rolling over', 'Mike', '0680244458', 2, 'Beginner', '2025-08-25 14:59:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_enrol_date` (`enrolment_id`,`session_date`);

--
-- Indexes for table `enrolments`
--
ALTER TABLE `enrolments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_en_program` (`program_id`),
  ADD KEY `fk_en_gymnast` (`gymnast_id`);

--
-- Indexes for table `gymnasts`
--
ALTER TABLE `gymnasts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `enrolments`
--
ALTER TABLE `enrolments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `gymnasts`
--
ALTER TABLE `gymnasts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `fk_att_enrol` FOREIGN KEY (`enrolment_id`) REFERENCES `enrolments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enrolments`
--
ALTER TABLE `enrolments`
  ADD CONSTRAINT `fk_en_gymnast` FOREIGN KEY (`gymnast_id`) REFERENCES `gymnasts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_en_program` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
