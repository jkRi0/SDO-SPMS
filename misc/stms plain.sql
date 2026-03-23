-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 18, 2026 at 02:17 AM
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
-- Database: `stms`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `target_type` varchar(50) DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `target_type`, `target_id`, `details`, `created_at`) VALUES
(1, 10, 'login', 'user', 10, 'Successful login', '2026-03-18 01:11:53');

-- --------------------------------------------------------

--
-- Table structure for table `app_settings`
--

CREATE TABLE `app_settings` (
  `setting_key` varchar(128) NOT NULL,
  `setting_value` varchar(255) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `department_notifications`
--

CREATE TABLE `department_notifications` (
  `id` int(11) NOT NULL,
  `role` varchar(50) NOT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `email_sent` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(5, 'accounting'),
(1, 'admin'),
(6, 'budget'),
(7, 'cashier'),
(3, 'procurement'),
(2, 'supplier'),
(4, 'supply');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `po_number` varchar(100) NOT NULL,
  `program_title` varchar(255) NOT NULL,
  `po_type` varchar(50) DEFAULT NULL,
  `proponent` varchar(255) DEFAULT NULL,
  `coverage_start` date DEFAULT NULL,
  `coverage_end` date DEFAULT NULL,
  `expected_date` varchar(255) DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `proc_status` varchar(100) DEFAULT NULL,
  `proc_remarks` text DEFAULT NULL,
  `proc_date` date DEFAULT NULL,
  `supply_status` varchar(100) DEFAULT NULL,
  `supply_delivery_receipt` varchar(100) DEFAULT NULL,
  `supply_sales_invoice` varchar(100) DEFAULT NULL,
  `supply_remarks` text DEFAULT NULL,
  `supply_date` date DEFAULT NULL,
  `acct_pre_status` varchar(100) DEFAULT NULL,
  `acct_pre_remarks` text DEFAULT NULL,
  `acct_pre_date` date DEFAULT NULL,
  `budget_dv_number` varchar(100) DEFAULT NULL,
  `budget_dv_date` date DEFAULT NULL,
  `budget_status` varchar(100) DEFAULT NULL,
  `budget_demandability` varchar(100) DEFAULT NULL,
  `budget_remarks` text DEFAULT NULL,
  `acct_post_status` varchar(100) DEFAULT NULL,
  `acct_post_remarks` text DEFAULT NULL,
  `acct_post_date` date DEFAULT NULL,
  `cashier_status` varchar(100) DEFAULT NULL,
  `cashier_remarks` text DEFAULT NULL,
  `cashier_or_number` varchar(100) DEFAULT NULL,
  `cashier_or_date` date DEFAULT NULL,
  `cashier_landbank_ref` varchar(150) DEFAULT NULL,
  `cashier_payment_date` date DEFAULT NULL,
  `supply_partial_delivery_date` date DEFAULT NULL,
  `supply_delivery_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_handoffs`
--

CREATE TABLE `transaction_handoffs` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `from_dept` varchar(32) NOT NULL,
  `to_dept` varchar(32) NOT NULL,
  `forwarded_at` datetime NOT NULL,
  `received_at` datetime DEFAULT NULL,
  `delay_seconds` int(11) DEFAULT NULL,
  `exceeded_grace` tinyint(1) NOT NULL DEFAULT 0,
  `created_by_user_id` int(11) DEFAULT NULL,
  `received_by_user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_updates`
--

CREATE TABLE `transaction_updates` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `stage` varchar(50) NOT NULL,
  `status` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `active_session_id` varchar(128) DEFAULT NULL,
  `active_session_last_seen` datetime DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `full_name` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `role_id`, `supplier_id`, `active_session_id`, `active_session_last_seen`, `last_login_at`, `last_login_ip`, `created_at`, `full_name`) VALUES
(10, 'admin', '$2a$12$XhSdO3SPRj0NL2b2Ln1v8uzgYAQEopTZbNSEl/z7BQvud/YtrqWeS', 1, NULL, NULL, NULL, '2026-03-18 09:11:53', '::1', '2026-02-10 06:04:17', NULL),
(12, 'procurement', '$2y$10$QclzMbbCTh0V3CoawlNNKOKV4SwirQglkHhk6t2DaKyJRsEGKw9Vi', 3, NULL, 'voi1h8b3fqg8kga50qna8i34ko', '2026-03-05 09:51:03', '2026-03-18 03:21:11', '192.168.100.105', '2026-02-10 06:04:17', NULL),
(13, 'supply', '$2a$12$XhSdO3SPRj0NL2b2Ln1v8uzgYAQEopTZbNSEl/z7BQvud/YtrqWeS', 4, NULL, 'ekrk5vsvck4g5u4qq32hffcp2e', '2026-03-05 09:51:03', '2026-03-17 23:38:03', '::1', '2026-02-10 06:04:17', NULL),
(14, 'accounting', '$2a$12$XhSdO3SPRj0NL2b2Ln1v8uzgYAQEopTZbNSEl/z7BQvud/YtrqWeS', 5, NULL, 'mm7dcl78j23eb67dav77vm290p', '2026-03-05 09:51:03', '2026-03-18 00:47:55', '::1', '2026-02-10 06:04:17', NULL),
(15, 'budget', '$2a$12$XhSdO3SPRj0NL2b2Ln1v8uzgYAQEopTZbNSEl/z7BQvud/YtrqWeS', 6, NULL, 'is20s2gsenns7n8dgjp437e4mg', '2026-03-05 09:05:49', '2026-03-18 01:31:42', '::1', '2026-02-10 06:04:17', NULL),
(16, 'cashier', '$2y$10$dyGEN9guUrlc2mUANfKlouzI7yx2gdG5SOyfsCgKPBuJwMrjwgp6S', 7, NULL, 'oiv0qv5qvmjf84o4co8334ghap', '2026-03-05 09:51:03', '2026-03-18 03:24:52', '192.168.100.8', '2026-02-10 06:04:17', NULL),
(23, 'sample cashier', '$2y$10$FWnyLpEBowxNhXNpBcYpm..0XLNQCJK0NjkUtAEdVsb0kVpK2e.0G', 7, NULL, NULL, NULL, NULL, NULL, '2026-03-03 00:47:47', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_preferences`
--

CREATE TABLE `user_preferences` (
  `user_id` int(11) NOT NULL,
  `smart_polling_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_id` varchar(128) NOT NULL,
  `device_label` varchar(100) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_seen` timestamp NULL DEFAULT NULL,
  `revoked_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_sessions`
--

INSERT INTO `user_sessions` (`id`, `user_id`, `session_id`, `device_label`, `ip`, `user_agent`, `created_at`, `last_seen`, `revoked_at`) VALUES
(1, 16, '1fhheb03ntd7etdeo66qjb708v', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 01:15:32', '2026-03-18 01:17:50', NULL),
(58, 15, 'nv0u80eh5cd5n1b3fnbacqksb7', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 01:16:27', '2026-03-18 01:17:28', NULL),
(59, 12, 'pjqdq2ojg127d9qi0jntvg28rd', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-18 01:16:27', '2026-03-18 01:17:28', NULL),
(68, 14, '4ddrbe94km28vtimi5u7p00jqs', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-18 01:16:27', '2026-03-18 01:17:28', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_activity_user` (`user_id`),
  ADD KEY `idx_activity_created_at` (`created_at`);

--
-- Indexes for table `app_settings`
--
ALTER TABLE `app_settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `department_notifications`
--
ALTER TABLE `department_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_role_created` (`role`,`created_at`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_feedback_user` (`user_id`),
  ADD KEY `idx_feedback_role` (`role`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `transaction_handoffs`
--
ALTER TABLE `transaction_handoffs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tx_open` (`transaction_id`,`received_at`),
  ADD KEY `idx_tx_time` (`transaction_id`,`forwarded_at`);

--
-- Indexes for table `transaction_updates`
--
ALTER TABLE `transaction_updates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tx_updates_tx` (`transaction_id`),
  ADD KEY `idx_tx_updates_stage` (`stage`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_session_id` (`session_id`),
  ADD KEY `idx_user_last_seen` (`user_id`,`last_seen`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `department_notifications`
--
ALTER TABLE `department_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `transaction_handoffs`
--
ALTER TABLE `transaction_handoffs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_updates`
--
ALTER TABLE `transaction_updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=181;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Constraints for table `transaction_updates`
--
ALTER TABLE `transaction_updates`
  ADD CONSTRAINT `tx_updates_tx_fk` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
