-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 21, 2026 at 01:23 AM
-- Server version: 12.0.2-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `prosecutor_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_info`
--

CREATE TABLE `about_info` (
  `id` int(11) NOT NULL,
  `head_name` text DEFAULT NULL,
  `mission_text` text DEFAULT NULL,
  `ops_title` text DEFAULT NULL,
  `ops_text` text DEFAULT NULL,
  `card_title` text DEFAULT NULL,
  `card_text` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `about_info`
--

INSERT INTO `about_info` (`id`, `head_name`, `mission_text`, `ops_title`, `ops_text`, `card_title`, `card_text`) VALUES
(1, 'Atty. Juan S. Dela Cruz', 'Our mission is to uphold the rule of law through the fair and efficient administration of justice, ensuring that every citizen of Pagadian City is served with integrity and transparency.', 'Administrative Operations', 'The Administrative Department serves as the backbone of our office, managing case records, clearance processing, and personnel workflows to ensure seamless legal operations.', 'Office Administration', 'Responsible for the coordination of prosecutors, aides, and secretarial staff within the system.');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `author` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `is_pinned` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `author`, `message`, `is_pinned`, `created_at`) VALUES
(1, 'Prosecutor', 'annnoncement', 0, '2026-05-04 04:54:25'),
(2, 'Prosecutor', 'hi guys', 0, '2026-05-04 04:54:37'),
(3, 'Prosecutor', 'wish buss', 0, '2026-05-04 04:54:50'),
(4, 'Prosecutor', 'hi guys everyone', 0, '2026-05-04 04:55:10'),
(5, 'Prosecutor', 'announcements', 0, '2026-05-04 04:55:18'),
(6, 'Prosecutor', 'we have a meeting today', 0, '2026-05-04 04:58:01'),
(7, 'Prosecutor', 'please have a meeting today', 1, '2026-05-04 04:58:31'),
(8, 'Prosecutor', 'hi', 0, '2026-05-04 12:09:59'),
(9, 'Prosecutor', 'hi', 0, '2026-05-04 18:24:53');

-- --------------------------------------------------------

--
-- Table structure for table `cases`
--

CREATE TABLE `cases` (
  `id` int(11) NOT NULL,
  `nps_docket_no` varchar(100) NOT NULL,
  `complainants` text NOT NULL,
  `respondents` text NOT NULL,
  `offense` varchar(255) NOT NULL,
  `commission_dt` datetime DEFAULT NULL,
  `commission_place` text DEFAULT NULL,
  `q1_similar_complaint` varchar(10) DEFAULT NULL,
  `q2_counter_charge` varchar(10) DEFAULT NULL,
  `q3_related_case` varchar(10) DEFAULT NULL,
  `date_received` date DEFAULT NULL,
  `time_received` time DEFAULT NULL,
  `receiving_staff` varchar(100) DEFAULT NULL,
  `assigned_to` varchar(100) DEFAULT NULL,
  `date_assigned` date DEFAULT NULL,
  `status` enum('Pending Review','Ongoing Trial','Resolved') DEFAULT 'Pending Review',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `q1` varchar(10) DEFAULT 'N/A',
  `q2` varchar(10) DEFAULT 'N/A',
  `q3` varchar(10) DEFAULT 'N/A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `cases`
--

INSERT INTO `cases` (`id`, `nps_docket_no`, `complainants`, `respondents`, `offense`, `commission_dt`, `commission_place`, `q1_similar_complaint`, `q2_counter_charge`, `q3_related_case`, `date_received`, `time_received`, `receiving_staff`, `assigned_to`, `date_assigned`, `status`, `created_at`, `q1`, `q2`, `q3`) VALUES
(1, 'NPS-IX-09-INV-26E-0004', 'Maria Santos', 'Pedro Penduko', 'Qualified Theft', '2026-04-12 10:30:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Ongoing Trial', '2026-05-03 09:54:38', 'N/A', 'N/A', 'N/A'),
(2, '002', 'karl,male,26,balintawak', 'karl,male,56,balintawak', 'rape', '2026-05-04 10:06:00', '', 'NO', 'NO', 'NO', '2026-05-05', '10:05:00', 'kane', 'sir gavenia', '2026-05-04', 'Pending Review', '2026-05-04 02:06:40', 'N/A', 'N/A', 'N/A'),
(3, 'NPS-IX-09-INV-2026-0001', 'jason,male,45,balintawak', 'gary,male,67,sto.ninio', 'assault', '2026-05-05 10:13:00', 'pagadian', 'NO', 'NO', 'NO', '2026-05-04', '10:10:00', 'kane', 'sir gavenia', '2026-05-04', 'Pending Review', '2026-05-04 02:13:37', 'N/A', 'N/A', 'N/A'),
(4, 'NPS-IX-09-INV-2026-0002', 'kian,male,21,balintawak', 'kian,male,21,banale', 'robery', '2026-05-05 03:30:00', 'pagadian', 'NO', 'NO', 'NO', '2026-05-05', '03:29:00', 'kane', 'sir gavenia', '2026-05-06', 'Pending Review', '2026-05-04 19:30:46', 'N/A', 'N/A', 'N/A'),
(5, 'NPS-IX-09-INV-2026-0003', 'asdasdqwe', 'qweqsdasd', 'assaoult', '2026-05-05 03:40:00', 'aeqwe', NULL, NULL, NULL, '2026-05-05', '03:40:00', 'Eureka Madona D. Rosales', 'ms.jeska', '2026-05-05', 'Pending Review', '2026-05-04 19:41:09', 'NO', 'NO', 'NO');

-- --------------------------------------------------------

--
-- Table structure for table `case_evidence`
--

CREATE TABLE `case_evidence` (
  `id` int(11) NOT NULL,
  `nps_docket_no` varchar(100) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `uploaded_by` varchar(100) DEFAULT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clearance_applications`
--

CREATE TABLE `clearance_applications` (
  `id` int(11) NOT NULL,
  `applicant_name` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `contact_no` varchar(20) DEFAULT NULL,
  `clearance_type` varchar(100) DEFAULT NULL,
  `purpose` text DEFAULT NULL,
  `status` enum('Not Complete','Pending','Released') DEFAULT 'Not Complete',
  `requirements_checked` text DEFAULT NULL,
  `attached_file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `clearance_applications`
--

INSERT INTO `clearance_applications` (`id`, `applicant_name`, `address`, `dob`, `contact_no`, `clearance_type`, `purpose`, `status`, `requirements_checked`, `attached_file`, `created_at`) VALUES
(1, 'Maria Clara', 'Purok kamunggay Sto Nino Pagadian City', '2026-05-04', '09754656881', 'Firearm License/Permit to Carry', 'for fire arms', 'Pending', 'Valid ID,Barangay Clearance,Police Clearance,2x2 Photo', NULL, '2026-05-04 02:38:59');

-- --------------------------------------------------------

--
-- Table structure for table `contact_info`
--

CREATE TABLE `contact_info` (
  `id` int(11) NOT NULL,
  `address` text NOT NULL,
  `phone` text NOT NULL,
  `email` text NOT NULL,
  `hours` text NOT NULL,
  `hours_note` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `contact_info`
--

INSERT INTO `contact_info` (`id`, `address`, `phone`, `email`, `hours`, `hours_note`) VALUES
(1, 'Hall of Justice Building,<br>Pagadian City, Zamboanga del Sur,<br>Philippines, 7016', 'Main: (062) 214-XXXX<br>Hotline: +63 9XX XXX XXXX', 'ocp.pagadian@doj.gov.ph<br>info.prosecutor@gmail.com', 'Monday - Friday: 8:00 AM - 5:00 PM', 'Closed on Weekends and Public Holidays');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `client_type` varchar(50) DEFAULT NULL,
  `feedback_date` date DEFAULT NULL,
  `sex` varchar(10) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `services` text DEFAULT NULL,
  `cc1` int(11) DEFAULT NULL,
  `cc2` int(11) DEFAULT NULL,
  `sqd0` int(11) DEFAULT NULL,
  `sqd1` int(11) DEFAULT NULL,
  `sqd2` int(11) DEFAULT NULL,
  `sqd3` int(11) DEFAULT NULL,
  `sqd4` int(11) DEFAULT NULL,
  `sqd5` int(11) DEFAULT NULL,
  `sqd6` int(11) DEFAULT NULL,
  `sqd7` int(11) DEFAULT NULL,
  `sqd8` int(11) DEFAULT NULL,
  `suggestions` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `client_type`, `feedback_date`, `sex`, `age`, `services`, `cc1`, `cc2`, `sqd0`, `sqd1`, `sqd2`, `sqd3`, `sqd4`, `sqd5`, `sqd6`, `sqd7`, `sqd8`, `suggestions`, `created_at`) VALUES
(1, 'Business', '2026-05-04', 'Male', 23, 'Preliminary Investigation', 1, 1, 1, 2, 3, 4, 4, 4, 4, 4, 4, 'the dismiss', '2026-05-04 05:12:57');

-- --------------------------------------------------------

--
-- Table structure for table `group_chat`
--

CREATE TABLE `group_chat` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_pinned` tinyint(1) DEFAULT 0,
  `reactions` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `group_chat`
--

INSERT INTO `group_chat` (`id`, `username`, `role`, `message`, `is_pinned`, `reactions`, `created_at`) VALUES
(1, 'msd', 'Staff', 'hi', 0, '[]', '2026-05-05 02:32:01');

-- --------------------------------------------------------

--
-- Table structure for table `reminders`
--

CREATE TABLE `reminders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `task_text` varchar(255) NOT NULL,
  `is_done` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `system_name` varchar(255) DEFAULT 'Office of the Prosecutor Management System',
  `maintenance_mode` tinyint(1) DEFAULT 0,
  `timezone` varchar(100) DEFAULT 'Asia/Manila',
  `clearance_export_format` varchar(50) DEFAULT 'PDF',
  `clearance_signatory` varchar(100) DEFAULT 'Head Prosecutor',
  `clearance_qr_verification` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `system_name`, `maintenance_mode`, `timezone`, `clearance_export_format`, `clearance_signatory`, `clearance_qr_verification`) VALUES
(1, 'Office of the Prosecutor Management System', 0, 'Asia/Manila', 'PDF', 'Head Prosecutor', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `role` varchar(50) DEFAULT 'Staff',
  `sex` enum('Male','Female','Other') DEFAULT 'Male',
  `age` int(11) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT 'default-avatar.png',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `first_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `contact_no` varchar(50) DEFAULT NULL,
  `last_activity` datetime DEFAULT NULL,
  `account_status` varchar(20) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `name`, `role`, `sex`, `age`, `address`, `email`, `profile_pic`, `created_at`, `first_name`, `middle_name`, `last_name`, `dob`, `gender`, `contact_no`, `last_activity`, `account_status`) VALUES
(1, 'Admin', '1234', 'Administrator', 'Admin', 'Male', NULL, NULL, NULL, 'default-avatar.png', '2026-05-04 10:12:05', 'System', NULL, 'Administrator', NULL, NULL, NULL, '2026-05-04 21:42:37', 'Active'),
(3, 'deskuser', 'password123', NULL, 'Staff', 'Male', NULL, NULL, NULL, 'default-avatar.png', '2026-05-04 11:37:41', 'Desk', NULL, 'Management', NULL, NULL, NULL, NULL, 'Active'),
(4, 'maria123', 'maria123', NULL, 'Secretary', 'Male', 26, 'Purok kamunggay Sto Nino Pagadian City', 'geraldunderscore8@gmail.com', 'default-avatar.png', '2026-05-04 13:48:49', 'Maria', 'kian', 'vergara', '2026-05-04', 'Male', '09754656881', NULL, 'Active'),
(6, 'kane', '12345', NULL, 'Secretary', 'Male', 28, 'Purok kamunggay Sto Nino Pagadian City', 'geraldunderscore8@gmail.com', 'default-avatar.png', '2026-05-04 19:28:04', 'Jose', 'kian', 'vergara', '2026-05-05', 'Female', '09754656881', NULL, 'Active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_info`
--
ALTER TABLE `about_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cases`
--
ALTER TABLE `cases`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nps_docket_no` (`nps_docket_no`);

--
-- Indexes for table `case_evidence`
--
ALTER TABLE `case_evidence`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clearance_applications`
--
ALTER TABLE `clearance_applications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_info`
--
ALTER TABLE `contact_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `group_chat`
--
ALTER TABLE `group_chat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reminders`
--
ALTER TABLE `reminders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_info`
--
ALTER TABLE `about_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `cases`
--
ALTER TABLE `cases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `case_evidence`
--
ALTER TABLE `case_evidence`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clearance_applications`
--
ALTER TABLE `clearance_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contact_info`
--
ALTER TABLE `contact_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `group_chat`
--
ALTER TABLE `group_chat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reminders`
--
ALTER TABLE `reminders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reminders`
--
ALTER TABLE `reminders`
  ADD CONSTRAINT `reminders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
