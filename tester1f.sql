-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 20, 2026 at 10:13 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tester1f`
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

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `student_id`, `tuition_id`, `payment_reference`, `amount_paid`, `payment_type`, `payment_month`, `payment_status`, `payment_method`, `payment_date`) VALUES
(10, 5, 3, 'REF-1776669899-5', 2500.00, '', '3', 'paid', 'GCash', '2026-04-20 07:24:59'),
(11, 3, 4, 'REF-1776671403-3', 2340.00, '', '1', 'paid', 'GCash', '2026-04-20 07:50:03');

-- --------------------------------------------------------

--
-- Table structure for table `payment_installments`
--

CREATE TABLE `payment_installments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `tuition_id` int(11) NOT NULL,
  `month_number` int(11) NOT NULL,
  `amount_due` decimal(10,2) NOT NULL,
  `amount_paid` decimal(10,2) DEFAULT 0.00,
  `due_date` date NOT NULL,
  `status` enum('unpaid','paid') DEFAULT 'unpaid',
  `payment_reference` varchar(100) DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_installments`
--

INSERT INTO `payment_installments` (`id`, `student_id`, `tuition_id`, `month_number`, `amount_due`, `amount_paid`, `due_date`, `status`, `payment_reference`, `paid_at`) VALUES
(12, 5, 3, 1, 2500.00, 0.00, '2026-05-20', 'paid', 'REF-1776669397-5', '2026-04-20 07:16:37'),
(13, 5, 3, 2, 2500.00, 0.00, '2026-06-20', 'paid', 'REF-1776669598-5', '2026-04-20 07:19:58'),
(14, 5, 3, 3, 2500.00, 0.00, '2026-07-20', 'paid', 'REF-1776669899-5', '2026-04-20 07:24:59'),
(17, 3, 4, 1, 2340.00, 0.00, '2026-05-20', 'paid', 'REF-1776671403-3', '2026-04-20 07:50:03'),
(18, 3, 4, 2, 2340.00, 0.00, '2026-06-20', 'unpaid', NULL, NULL),
(19, 3, 4, 3, 2340.00, 0.00, '2026-07-20', 'unpaid', NULL, NULL),
(20, 3, 4, 4, 2340.00, 0.00, '2026-08-20', 'unpaid', NULL, NULL),
(21, 3, 4, 5, 2340.00, 0.00, '2026-09-20', 'unpaid', NULL, NULL);

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

--
-- Dumping data for table `student_gpa`
--

INSERT INTO `student_gpa` (`id`, `student_id`, `gpa`, `semester`, `school_year`, `created_at`) VALUES
(1, 2, 1.60, '1st sem', '1st year', '2026-04-20 05:19:33'),
(2, 3, 1.60, '1st sem', '1st year', '2026-04-20 05:20:40'),
(3, 5, 1.25, '1st sem', '2nd year', '2026-04-20 05:20:40');

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
(1, 'Shane', 'Andajer', '05-10730', 'college', 'ACT', '1st', 'ahasdgbasdgash@gmail.com', '45445634', 'active', '2026-04-20 00:50:11', '2026-04-20 00:50:11'),
(2, 'Arnold', 'Nava', '05-12345', 'college', 'ACT', '1st', 'ahgdshuifdsjhi@gmail.com', '091423456789', 'active', '2026-04-20 00:52:16', '2026-04-20 00:52:16'),
(3, 'Lyndon', 'Torrado', '05-9578', 'college', 'ACT', '1st', 'example123@gmail.com', '012365478912', 'active', '2026-04-20 01:05:34', '2026-04-20 01:05:34'),
(5, 'Kristian', 'Luiz', '05-45878', 'college', 'HM', '2nd', 'exaasdasmple123@gmail.com', '012365478912', 'active', '2026-04-20 01:05:34', '2026-04-20 01:05:34');

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

--
-- Dumping data for table `student_tuition`
--

INSERT INTO `student_tuition` (`id`, `student_id`, `semester`, `school_year`, `base_tuition`, `discount_amount`, `final_tuition`, `created_at`) VALUES
(1, 3, '1st sem', '1st year', 13000.00, 0.00, 9100.00, '2026-04-20 04:46:31'),
(3, 5, '1st Sem', '2026-2027', 25000.00, 12500.00, 12500.00, '2026-04-20 07:05:46'),
(4, 3, '1st Sem', '2026-2027', 13000.00, 1300.00, 11700.00, '2026-04-20 07:32:51');

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
(3, 2, 'arnold', '', '$2y$10$F3LL7hkbyKAmuamTh/wz2uv2bqWevwqxdvfKT0h4Jo9k03OPgSrKm', 'student'),
(4, 3, 'lyn', '', '$2y$10$uxrn.LAfuJ58/CfWvusHsulQjz2vkQXb0g4V/GsF9WxKRSJGzy7Ou', 'student'),
(5, 5, 'kristian', '', '$2y$10$gpjMB78oqNqs/cuHMxhFEeaY/B8INxt3N2NIRx/U1/qFH109A8lnu', 'student');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `payment_installments`
--
ALTER TABLE `payment_installments`
  ADD PRIMARY KEY (`id`);

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
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `payment_installments`
--
ALTER TABLE `payment_installments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `student_gpa`
--
ALTER TABLE `student_gpa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `student_list`
--
ALTER TABLE `student_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_tuition`
--
ALTER TABLE `student_tuition`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_cred`
--
ALTER TABLE `user_cred`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student_list` (`id`);

--
-- Constraints for table `student_gpa`
--
ALTER TABLE `student_gpa`
  ADD CONSTRAINT `student_gpa_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student_list` (`id`);

--
-- Constraints for table `student_tuition`
--
ALTER TABLE `student_tuition`
  ADD CONSTRAINT `student_tuition_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student_list` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
