-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 01, 2025 at 06:36 PM
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
-- Database: `expenditure`
--

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `family_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `family_id`, `user_id`, `message`, `image_path`, `created_at`) VALUES
(1, 11, 82, 'hi', NULL, '2025-05-29 17:34:23'),
(2, 11, 82, 'ok', NULL, '2025-05-29 17:34:30'),
(3, 11, 82, 'ok', '../Uploads/chat/chat_68384d56ee81a.png', '2025-05-29 17:34:38'),
(4, 11, 82, 'ok', NULL, '2025-05-29 17:53:38'),
(5, 11, 83, 'ok', NULL, '2025-05-29 17:56:05'),
(6, 11, 83, 'what bro', NULL, '2025-05-29 17:56:22'),
(7, 11, 83, 'what bro', NULL, '2025-05-29 17:56:44'),
(8, 11, 82, '', '../Uploads/chat/chat_68385291e7.png', '2025-05-29 17:57:05');

-- --------------------------------------------------------

--
-- Table structure for table `families`
--

CREATE TABLE `families` (
  `id` int(11) NOT NULL,
  `family_name` varchar(255) NOT NULL,
  `security_code` varchar(10) NOT NULL,
  `creator_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `families`
--

INSERT INTO `families` (`id`, `family_name`, `security_code`, `creator_id`) VALUES
(2, 'Family1', 'SPIQI2HD', NULL),
(3, 'Family2', 'ALYUNGDE', NULL),
(7, 'Family3', 'DO7RM2FM', 78),
(9, 'Family4', 'P9BEO2Y0', 81),
(11, 'Family5', '44RKXU2L', 82);

-- --------------------------------------------------------

--
-- Table structure for table `lending`
--

CREATE TABLE `lending` (
  `id` int(11) UNSIGNED NOT NULL,
  `UserId` int(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date_of_lending` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `status` enum('pending','received') NOT NULL,
  `current_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lending`
--

INSERT INTO `lending` (`id`, `UserId`, `name`, `date_of_lending`, `amount`, `description`, `status`, `current_time`) VALUES
(10, 26, 'User1', '2023-03-16', 5000.00, 'hiii', 'pending', '2023-04-04 13:31:19'),
(11, 30, 'User2', '2023-03-31', 6000.00, 'llll', 'received', '2023-04-04 14:28:52'),
(12, 26, 'User3', '2023-03-22', 6000.00, 'hii give me my money 💵💵', 'pending', '2023-04-07 05:04:21'),
(13, 31, 'User4', '2023-04-05', 5000.00, 'friend', 'pending', '2023-04-05 14:13:15'),
(14, 31, 'User5', '2023-04-04', 2000.00, 'Friends', 'received', '2023-04-05 14:13:48'),
(15, 66, 'User6', '2023-04-03', 1000.00, 'I want to take money from user', 'received', '2023-04-11 13:26:38'),
(16, 67, 'User7', '2023-04-05', 500.00, 'I want to take money from user', 'received', '2023-04-11 13:42:16'),
(17, 68, 'User8', '2023-04-04', 500.00, 'i want to take from user on 14/04/23', 'received', '2023-04-12 05:23:44'),
(19, 68, 'User9', '2023-04-12', 5000.00, 'i want to take money from user', 'pending', '2023-04-12 05:28:56');

-- --------------------------------------------------------

--
-- Table structure for table `tblcategory`
--

CREATE TABLE `tblcategory` (
  `CategoryId` int(11) NOT NULL,
  `CategoryName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `UserId` int(11) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblcategory`
--

INSERT INTO `tblcategory` (`CategoryId`, `CategoryName`, `UserId`, `CreatedAt`) VALUES
(81, 'fruits', 74, '2025-05-28 10:15:35'),
(82, 'bills', 74, '2025-05-28 10:17:40'),
(85, 'chicken', 78, '2025-05-28 16:26:44'),
(86, 'petrole', 78, '2025-05-28 16:27:37'),
(87, 'petrol', 78, '2025-05-28 16:27:59'),
(88, 'leaf vegetables', 78, '2025-05-28 16:28:55'),
(89, 'hair cutting', 78, '2025-05-28 16:30:16'),
(90, 'fruits', 79, '2025-05-28 16:40:34'),
(91, 'chicken', 81, '2025-05-29 07:49:45'),
(92, 'chicken', 82, '2025-05-29 08:43:02');

-- --------------------------------------------------------

--
-- Table structure for table `tblexpense`
--

CREATE TABLE `tblexpense` (
  `ID` int(10) NOT NULL,
  `UserId` int(10) NOT NULL,
  `ExpenseDate` date DEFAULT NULL,
  `CategoryId` int(11) NOT NULL,
  `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ExpenseCost` varchar(200) DEFAULT NULL,
  `Description` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NoteDate` timestamp NULL DEFAULT current_timestamp(),
  `product_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblexpense`
--

INSERT INTO `tblexpense` (`ID`, `UserId`, `ExpenseDate`, `CategoryId`, `category`, `ExpenseCost`, `Description`, `NoteDate`, `product_name`) VALUES
(159, 81, '2025-05-29', 91, 'chicken', '120', 'wdq', '2025-05-29 07:49:54', '0'),
(160, 82, '2025-05-29', 92, 'chicken', '342', 'kf', '2025-05-29 08:43:11', '0');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(30) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `verification_code` varchar(12) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `family_id` int(11) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `verification_code`, `created_at`, `family_id`, `profile_image`) VALUES
(81, 'User10', 'user10@gmail.com', '1234567890', '$2y$10$3k6Tu8fD8ZV30HLEusG8iOJDmG/r0QysAYoanGOK4BxR4 Ml3bd5Vji', '9840a77fc0e7', '2025-05-29 13:05:16', 9, NULL),
(82, 'User11', 'user11@gmail.com', '0987654321', '$2y$10$Mj9ajxyXytTgx0JHfL4LAeyv6O1WQx6BWXNsm67N0yVu8NIDr7NIG', 'e92dfc181142', '2025-05-29 14:01:02', 11, 'images/profile_82_1748517664.png'),
(83, 'User12', 'user12@gmail.com', '5432167890', '$2y$10$/lTMO8XNNDmQ179BTa5NkOpRpm4jEKkHXtnthKwjkXg2pIZHxwGwK', '6372e0b057a3', '2025-05-29 17:55:20', 11, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_families`
--

CREATE TABLE `user_families` (
  `user_id` int(11) NOT NULL,
  `family_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_families`
--

INSERT INTO `user_families` (`user_id`, `family_id`) VALUES
(74, 2),
(78, 7),
(79, 7),
(80, 7),
(81, 9),
(82, 11),
(83, 11);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `family_id` (`family_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `families`
--
ALTER TABLE `families`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `security_code` (`security_code`),
  ADD KEY `creator_id` (`creator_id`);

--
-- Indexes for table `lending`
--
ALTER TABLE `lending`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblcategory`
--
ALTER TABLE `tblcategory`
  ADD PRIMARY KEY (`CategoryId`),
  ADD KEY `UserId` (`UserId`);

--
-- Indexes for table `tblexpense`
--
ALTER TABLE `tblexpense`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`name`),
  ADD KEY `family_id` (`family_id`);

--
-- Indexes for table `user_families`
--
ALTER TABLE `user_families`
  ADD PRIMARY KEY (`user_id`,`family_id`),
  ADD KEY `family_id` (`family_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `families`
--
ALTER TABLE `families`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `lending`
--
ALTER TABLE `lending`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tblcategory`
--
ALTER TABLE `tblcategory`
  MODIFY `CategoryId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `tblexpense`
--
ALTER TABLE `tblexpense`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`family_id`) REFERENCES `families` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_messages_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `families`
--
ALTER TABLE `families`
  ADD CONSTRAINT `families_ibfk_1` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `tblcategory`
--
ALTER TABLE `tblcategory`
  ADD CONSTRAINT `tblcategory_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`family_id`) REFERENCES `families` (`id`);

--
-- Constraints for table `user_families`
--
ALTER TABLE `user_families`
  ADD CONSTRAINT `user_families_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_families_ibfk_2` FOREIGN KEY (`family_id`) REFERENCES `families` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
