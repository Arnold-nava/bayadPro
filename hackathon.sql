-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 19, 2026 at 09:23 PM
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
-- Database: `hackathon`
--

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `tuition_id` int(11) NOT NULL,
  `payment_reference` varchar(100) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_type` enum('monthly','full') NOT NULL,
  `payment_month` varchar(20) DEFAULT NULL,
  `payment_status` enum('paid','pending','partial') DEFAULT 'pending',
  `payment_method` varchar(50) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_gpa`
--

CREATE TABLE `student_gpa` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `gpa` decimal(3,2) NOT NULL,
  `semester` varchar(20) NOT NULL,
  `school_year` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_list`
--

CREATE TABLE `student_list` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `student_number` varchar(16) NOT NULL,
  `education_level` varchar(50) DEFAULT NULL,
  `program` varchar(50) DEFAULT NULL,
  `year_level` varchar(50) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `status` enum('graduated','active','inactive','dropped') NOT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_list`
--

INSERT INTO `student_list` (`id`, `first_name`, `last_name`, `student_number`, `education_level`, `program`, `year_level`, `email`, `contact_number`, `status`, `registered_at`, `updated_at`) VALUES
(1, 'Arnold', 'Nave', '05-12354', 'college', '', '', 'arnold.nave@example.com', '09123456789', 'active', '2026-04-19 06:08:38', '2026-04-19 06:08:38'),
(2, 'Dwight', 'Torrado', '05-824454', 'college', 'ACT', '1st year', 'dwight24@example.com', '09123456789', 'active', '2026-04-19 07:33:40', '2026-04-19 07:33:40'),
(3, 'Kristian Luiz', 'Erasmo', '05-824445', 'college', 'BSOA', '1st year', 'kristian.luiz@example.com', '09123456789', 'active', '2026-04-19 07:34:40', '2026-04-19 07:34:40'),
(4, 'Krishane', 'Andajer', '05-824845', 'college', 'HM', '1st year', 'andajer123@example.com', '09123456789', 'active', '2026-04-19 07:37:56', '2026-04-19 07:37:56'),
(5, 'Brian', 'Infante', '05-846545', 'college', 'ACT', '1st year', 'brian123@example.com', '09123456789', 'active', '2026-04-19 07:39:33', '2026-04-19 07:39:33'),
(6, 'Methusillah', 'Torrado', '05-848845', 'college', 'HM', '1st year', 'methus123@example.com', '09123456789', 'active', '2026-04-19 07:38:28', '2026-04-19 07:38:28'),
(7, 'Ezean', 'Alagadmo', '05-845245', 'college', 'ACT', '1st year', 'ezean.alagadmo@example.com', '09123456789', 'active', '2026-04-19 07:39:54', '2026-04-19 07:39:54'),
(8, 'Dave', 'Recto', '05-856545', 'college', 'ACT', '2nd year', 'dave.recto@example.com', '09123456789', 'active', '2026-04-19 15:59:31', '2026-04-19 15:59:31'),
(9, 'Juan', 'Tamad', '05-862545', 'Senior High School', 'STEM', '1st year', 'jaun.tamad@example.com', '09123456789', 'active', '2026-04-19 16:05:09', '2026-04-19 16:05:25'),
(16, 'Uno', 'Dos', '05-545664', 'Senior High School', 'STEM', '1st year', 'jaun.tamad@example.com', '09123456789', 'active', '2026-04-19 16:05:54', '2026-04-19 16:06:37'),
(25, 'Dwight', 'Torrado', '05-15647', '', '', '', 'arnoldnava995@gmail.com', '0999956456', 'graduated', '2026-04-19 18:10:39', '2026-04-19 18:10:39'),
(35, 'test', 'test', '05-11445', NULL, NULL, NULL, 'test@gmail.com', '0999956456', 'graduated', '2026-04-19 18:41:22', '2026-04-19 18:41:22');

-- --------------------------------------------------------

--
-- Table structure for table `student_tuition`
--

CREATE TABLE `student_tuition` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `semester` varchar(20) NOT NULL,
  `school_year` varchar(20) NOT NULL,
  `base_tuition` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `final_tuition` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_cred`
--

CREATE TABLE `user_cred` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` char(255) NOT NULL,
  `role` enum('admin','student','','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_cred`
--

INSERT INTO `user_cred` (`id`, `student_id`, `username`, `email`, `password`, `role`) VALUES
(1, 3, 'Test', '', '$2y$10$wPUsRbmtf/YhXz1D8GVPXu0Mjx.SIxOXSQi/n9sT4nTy7j90J4HRq', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `tuition_id` (`tuition_id`);

--
-- Indexes for table `student_gpa`
--
ALTER TABLE `student_gpa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `student_list`
--
ALTER TABLE `student_list`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_number` (`student_number`);

--
-- Indexes for table `student_tuition`
--
ALTER TABLE `student_tuition`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `user_cred`
--
ALTER TABLE `user_cred`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `student_id` (`student_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_gpa`
--
ALTER TABLE `student_gpa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_list`
--
ALTER TABLE `student_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `student_tuition`
--
ALTER TABLE `student_tuition`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_cred`
--
ALTER TABLE `user_cred`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`tuition_id`) REFERENCES `student_tuition` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_gpa`
--
ALTER TABLE `student_gpa`
  ADD CONSTRAINT `student_gpa_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student_list` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_tuition`
--
ALTER TABLE `student_tuition`
  ADD CONSTRAINT `student_tuition_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student_list` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_cred`
--
ALTER TABLE `user_cred`
  ADD CONSTRAINT `user_cred_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student_list` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
