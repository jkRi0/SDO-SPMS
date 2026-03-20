-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 20, 2026 at 07:36 AM
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
(1, 10, 'login', 'user', 10, 'Successful login', '2026-03-18 01:11:53'),
(2, 13, 'login', 'user', 13, 'Successful login', '2026-03-18 01:51:15'),
(3, 26, 'login', 'user', 26, 'Successful login (Google OAuth)', '2026-03-18 02:09:09'),
(4, 12, 'transaction_update', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"stage\":\"procurement\",\"status\":\"FOR SUPPLY REVIEW\",\"remarks\":\"for IAR\"}', '2026-03-18 02:15:46'),
(5, 12, 'transaction_handoff_forward', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"from_dept\":\"procurement\",\"to_dept\":\"supply\"}', '2026-03-18 02:16:10'),
(6, 13, 'transaction_handoff_receive', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"from_dept\":\"procurement\",\"to_dept\":\"supply\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-18 02:17:04'),
(7, 13, 'transaction_update', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"stage\":\"supply\",\"status\":\"COMPLETED\",\"remarks\":\"delivered\",\"delivery_receipt\":\"12314\",\"sales_invoice\":\"98787\"}', '2026-03-18 02:18:14'),
(8, 13, 'transaction_update', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"stage\":\"supply\",\"status\":\"COMPLETED\",\"remarks\":\"delivered\",\"delivery_receipt\":\"12314\",\"sales_invoice\":\"98787\"}', '2026-03-18 02:18:14'),
(9, 13, 'transaction_handoff_forward', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"from_dept\":\"supply\",\"to_dept\":\"accounting_pre\"}', '2026-03-18 02:18:38'),
(10, 14, 'transaction_handoff_receive', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"from_dept\":\"supply\",\"to_dept\":\"accounting_pre\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-18 02:19:06'),
(11, 14, 'transaction_update', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"stage\":\"accounting_pre\",\"status\":\"FOR ORS\",\"remarks\":\"complete attachment\",\"dv_amount\":\"\"}', '2026-03-18 02:20:19'),
(12, 14, 'transaction_handoff_forward', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"from_dept\":\"accounting_pre\",\"to_dept\":\"budget\"}', '2026-03-18 02:20:29'),
(13, 15, 'transaction_handoff_receive', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"from_dept\":\"accounting_pre\",\"to_dept\":\"budget\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-18 02:20:51'),
(14, 14, 'transaction_update', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"stage\":\"accounting_pre\",\"status\":\"FOR ORS\",\"remarks\":\"incomplete\",\"dv_amount\":\"\"}', '2026-03-18 02:26:14'),
(15, 15, 'transaction_update', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"stage\":\"budget\",\"status\":\"FOR PAYMENT\",\"remarks\":\"obligated\",\"dv_number\":\"876\",\"dv_date\":\"2026-03-27\",\"demandability\":\"Not Yet Due and Demandable\"}', '2026-03-18 02:31:34'),
(16, 15, 'transaction_handoff_forward', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"from_dept\":\"budget\",\"to_dept\":\"accounting_post\"}', '2026-03-18 02:31:54'),
(17, 14, 'transaction_handoff_receive', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"from_dept\":\"budget\",\"to_dept\":\"accounting_post\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-18 02:32:14'),
(18, 14, 'transaction_update', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"stage\":\"accounting_post\",\"status\":\"FOR VOUCHER\",\"remarks\":\"for payment\",\"dv_amount\":\"\"}', '2026-03-18 02:32:49'),
(19, 14, 'transaction_handoff_forward', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"from_dept\":\"accounting_post\",\"to_dept\":\"cashier\"}', '2026-03-18 02:32:55'),
(20, 16, 'transaction_handoff_receive', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"from_dept\":\"accounting_post\",\"to_dept\":\"cashier\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-18 02:39:01'),
(21, 16, 'transaction_update', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"stage\":\"cashier\",\"status\":\"For ACIC\",\"remarks\":\"Amount: 940288.99\",\"or_number\":\"345654\",\"or_date\":\"2026-04-02\",\"payment_date\":\"2026-04-02\",\"landbank_ref\":\"940288.99\"}', '2026-03-18 02:41:16'),
(22, 16, 'transaction_notify_supplier', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"supplier_id\":8,\"message\":\"Your PO 2025030039 is now marked as COMPLETED. Please check the portal for details.\"}', '2026-03-18 02:42:56'),
(23, 10, 'login', 'user', 10, 'Successful login', '2026-03-18 02:46:10'),
(24, 26, 'login', 'user', 26, 'Successful login (Google OAuth)', '2026-03-18 03:13:35'),
(25, 26, 'logout', 'user', 26, 'User logged out', '2026-03-18 03:20:06'),
(26, 10, 'logout', 'user', 10, 'User logged out', '2026-03-18 08:30:36'),
(27, 10, 'login', 'user', 10, 'Successful login', '2026-03-18 08:30:53'),
(28, 13, 'login', 'user', 13, 'Successful login', '2026-03-18 10:28:23'),
(29, 14, 'login', 'user', 14, 'Successful login', '2026-03-18 10:28:30'),
(30, 16, 'transaction_notify_supplier', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"supplier_id\":8,\"message\":\"Your PO 2025030039 is now marked as COMPLETED. Please check the portal for details.\"}', '2026-03-18 11:06:35'),
(31, 12, 'login', 'user', 12, 'Successful login', '2026-03-20 04:05:16'),
(32, 13, 'login', 'user', 13, 'Successful login', '2026-03-20 04:05:24'),
(33, 15, 'login', 'user', 15, 'Successful login', '2026-03-20 04:05:44'),
(34, 14, 'login', 'user', 14, 'Successful login', '2026-03-20 04:05:59'),
(35, 16, 'login', 'user', 16, 'Successful login', '2026-03-20 04:11:29');

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

--
-- Dumping data for table `department_notifications`
--

INSERT INTO `department_notifications` (`id`, `role`, `transaction_id`, `title`, `message`, `link`, `is_read`, `created_at`) VALUES
(1, 'supply', 51, 'Pending Transaction', 'Upcoming PO 2025030039', 'transaction_view.php?id=51', 0, '2026-03-18 10:15:46'),
(2, 'supply', 51, 'Handoff Forwarded', 'PROCUREMENT forwarded PO 2025030039 to SUPPLY. Please receive it.', 'transaction_view.php?id=51', 0, '2026-03-18 10:16:10'),
(3, 'procurement', 51, 'Handoff Received', 'Transaction was successfully received for PO 2025030039.', 'transaction_view.php?id=51', 0, '2026-03-18 10:17:04'),
(4, 'accounting', 51, 'Pending Transaction', 'Upcoming PO 2025030039', 'transaction_view.php?id=51', 0, '2026-03-18 10:18:14'),
(5, 'accounting', 51, 'Supply Completed', 'Supply marked PO 2025030039 as Completed.', 'transaction_view.php?id=51', 0, '2026-03-18 10:18:14'),
(6, 'accounting', 51, 'Handoff Forwarded', 'SUPPLY forwarded PO 2025030039 to ACCOUNTING_PRE. Please receive it.', 'transaction_view.php?id=51', 0, '2026-03-18 10:18:38'),
(7, 'supply', 51, 'Handoff Received', 'Transaction was successfully received for PO 2025030039.', 'transaction_view.php?id=51', 0, '2026-03-18 10:19:06'),
(8, 'budget', 51, 'Pending Transaction', 'Upcoming PO 2025030039', 'transaction_view.php?id=51', 0, '2026-03-18 10:20:19'),
(9, 'budget', 51, 'Handoff Forwarded', 'ACCOUNTING_PRE forwarded PO 2025030039 to BUDGET. Please receive it.', 'transaction_view.php?id=51', 0, '2026-03-18 10:20:29'),
(10, 'accounting', 51, 'Handoff Received', 'Transaction was successfully received for PO 2025030039.', 'transaction_view.php?id=51', 0, '2026-03-18 10:20:51'),
(11, 'accounting', 51, 'Pending Transaction', 'Upcoming PO 2025030039', 'transaction_view.php?id=51', 0, '2026-03-18 10:31:34'),
(12, 'accounting', 51, 'Handoff Forwarded', 'BUDGET forwarded PO 2025030039 to ACCOUNTING_POST. Please receive it.', 'transaction_view.php?id=51', 0, '2026-03-18 10:31:54'),
(13, 'budget', 51, 'Handoff Received', 'Transaction was successfully received for PO 2025030039.', 'transaction_view.php?id=51', 0, '2026-03-18 10:32:14'),
(14, 'cashier', 51, 'Pending Transaction', 'Upcoming PO 2025030039', 'transaction_view.php?id=51', 0, '2026-03-18 10:32:49'),
(15, 'cashier', 51, 'Handoff Forwarded', 'ACCOUNTING_POST forwarded PO 2025030039 to CASHIER. Please receive it.', 'transaction_view.php?id=51', 0, '2026-03-18 10:32:55'),
(16, 'accounting', 51, 'Handoff Received', 'Transaction was successfully received for PO 2025030039.', 'transaction_view.php?id=51', 0, '2026-03-18 10:39:01');

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

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `supplier_id`, `transaction_id`, `title`, `message`, `link`, `is_read`, `email_sent`, `created_at`) VALUES
(1, 8, 51, 'Payment status update', 'Your PO 2025030039 is now marked as COMPLETED. Please check the portal for details.', 'transaction_view.php?id=51', 0, 0, '2026-03-18 10:42:48'),
(2, 8, 51, 'Payment status update', 'Your PO 2025030039 is now marked as COMPLETED. Please check the portal for details.', 'transaction_view.php?id=51', 0, 0, '2026-03-18 19:06:28');

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

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `created_at`, `email`) VALUES
(8, 'alpha num', '2026-03-18 02:09:09', 'alphanum0002@gmail.com');

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
  `budget_ors_number` varchar(100) DEFAULT NULL,
  `budget_ors_date` date DEFAULT NULL,
  `budget_status` varchar(100) DEFAULT NULL,
  `budget_demandability` varchar(100) DEFAULT NULL,
  `budget_remarks` text DEFAULT NULL,
  `acct_post_status` varchar(100) DEFAULT NULL,
  `acct_post_remarks` text DEFAULT NULL,
  `acct_post_dv_number` varchar(100) DEFAULT NULL,
  `acct_post_dv_date` date DEFAULT NULL,
  `acct_post_dv_amount` decimal(15,2) DEFAULT NULL,
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

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `supplier_id`, `po_number`, `program_title`, `po_type`, `proponent`, `coverage_start`, `coverage_end`, `expected_date`, `amount`, `created_at`, `proc_status`, `proc_remarks`, `proc_date`, `supply_status`, `supply_delivery_receipt`, `supply_sales_invoice`, `supply_remarks`, `supply_date`, `acct_pre_status`, `acct_pre_remarks`, `acct_pre_date`, `budget_ors_number`, `budget_ors_date`, `budget_status`, `budget_demandability`, `budget_remarks`, `acct_post_status`, `acct_post_remarks`, `acct_post_dv_number`, `acct_post_dv_date`, `acct_post_dv_amount`, `acct_post_date`, `cashier_status`, `cashier_remarks`, `cashier_or_number`, `cashier_or_date`, `cashier_landbank_ref`, `cashier_payment_date`, `supply_partial_delivery_date`, `supply_delivery_date`) VALUES
(51, 8, '2025030039', 'procurement of supplies', 'Supplies', 'cid', '2026-03-20', '2026-03-31', NULL, 940288.99, '2026-03-18 02:14:59', 'FOR SUPPLY REVIEW', 'for IAR', '2026-03-18', 'COMPLETED', '12314', '98787', 'delivered', '2026-03-18', 'FOR ORS', 'incomplete', '2026-03-18', '876', '2026-03-27', 'FOR PAYMENT', 'Not Yet Due and Demandable', 'obligated', 'FOR VOUCHER', 'for payment', NULL, NULL, NULL, '2026-03-18', 'For ACIC', '', '345654', '2026-04-02', '940288.99', '2026-04-02', NULL, '2026-04-01');

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

--
-- Dumping data for table `transaction_handoffs`
--

INSERT INTO `transaction_handoffs` (`id`, `transaction_id`, `from_dept`, `to_dept`, `forwarded_at`, `received_at`, `delay_seconds`, `exceeded_grace`, `created_by_user_id`, `received_by_user_id`) VALUES
(1, 51, 'procurement', 'supply', '2026-03-18 10:16:10', '2026-03-18 10:17:04', 0, 0, 12, 13),
(2, 51, 'supply', 'accounting_pre', '2026-03-18 10:18:38', '2026-03-18 10:19:06', 0, 0, 13, 14),
(3, 51, 'accounting_pre', 'budget', '2026-03-18 10:20:29', '2026-03-18 10:20:51', 0, 0, 14, 15),
(4, 51, 'budget', 'accounting_post', '2026-03-18 10:31:54', '2026-03-18 10:32:14', 0, 0, 15, 14),
(5, 51, 'accounting_post', 'cashier', '2026-03-18 10:32:55', '2026-03-18 10:39:01', 0, 0, 14, 16);

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

--
-- Dumping data for table `transaction_updates`
--

INSERT INTO `transaction_updates` (`id`, `transaction_id`, `stage`, `status`, `remarks`, `created_at`) VALUES
(1, 51, 'procurement', 'FOR SUPPLY REVIEW', 'for IAR', '2026-03-18 02:15:46'),
(2, 51, 'supply', 'COMPLETED', 'delivered', '2026-03-18 02:18:14'),
(3, 51, 'supply', 'COMPLETED', 'delivered', '2026-03-18 02:18:14'),
(4, 51, 'accounting_pre', 'FOR ORS', 'complete attachment', '2026-03-18 02:20:19'),
(5, 51, 'accounting_pre', 'FOR ORS', 'incomplete', '2026-03-18 02:26:14'),
(6, 51, 'budget', 'FOR PAYMENT', 'obligated', '2026-03-18 02:31:34'),
(7, 51, 'accounting_post', 'FOR VOUCHER', 'for payment', '2026-03-18 02:32:49'),
(8, 51, 'cashier', 'For ACIC', 'Amount: 940288.99', '2026-03-18 02:41:16');

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
(10, 'admin', '$2a$12$XhSdO3SPRj0NL2b2Ln1v8uzgYAQEopTZbNSEl/z7BQvud/YtrqWeS', 1, NULL, NULL, NULL, '2026-03-18 16:30:53', '::1', '2026-02-10 06:04:17', NULL),
(12, 'procurement', '$2y$10$QclzMbbCTh0V3CoawlNNKOKV4SwirQglkHhk6t2DaKyJRsEGKw9Vi', 3, NULL, 'voi1h8b3fqg8kga50qna8i34ko', '2026-03-05 09:51:03', '2026-03-20 12:05:16', '::1', '2026-02-10 06:04:17', NULL),
(13, 'supply', '$2a$12$XhSdO3SPRj0NL2b2Ln1v8uzgYAQEopTZbNSEl/z7BQvud/YtrqWeS', 4, NULL, 'ekrk5vsvck4g5u4qq32hffcp2e', '2026-03-05 09:51:03', '2026-03-20 12:05:24', '::1', '2026-02-10 06:04:17', NULL),
(14, 'accounting', '$2a$12$XhSdO3SPRj0NL2b2Ln1v8uzgYAQEopTZbNSEl/z7BQvud/YtrqWeS', 5, NULL, 'mm7dcl78j23eb67dav77vm290p', '2026-03-05 09:51:03', '2026-03-20 12:05:59', '::1', '2026-02-10 06:04:17', NULL),
(15, 'budget', '$2a$12$XhSdO3SPRj0NL2b2Ln1v8uzgYAQEopTZbNSEl/z7BQvud/YtrqWeS', 6, NULL, 'is20s2gsenns7n8dgjp437e4mg', '2026-03-05 09:05:49', '2026-03-20 12:05:44', '::1', '2026-02-10 06:04:17', NULL),
(16, 'cashier', '$2y$10$dyGEN9guUrlc2mUANfKlouzI7yx2gdG5SOyfsCgKPBuJwMrjwgp6S', 7, NULL, 'oiv0qv5qvmjf84o4co8334ghap', '2026-03-05 09:51:03', '2026-03-20 12:11:29', '::1', '2026-02-10 06:04:17', NULL),
(23, 'sample cashier', '$2y$10$FWnyLpEBowxNhXNpBcYpm..0XLNQCJK0NjkUtAEdVsb0kVpK2e.0G', 7, NULL, NULL, NULL, NULL, NULL, '2026-03-03 00:47:47', NULL),
(26, 'alphanum0002', '$2y$10$J0BaAVz6x9kQLugvk5ZOr.EVZ1UjYgcchaPDeRkuXKVKOls033.wS', 2, 8, NULL, NULL, '2026-03-18 11:13:35', '::1', '2026-03-18 02:09:09', NULL);

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
(1, 16, '1fhheb03ntd7etdeo66qjb708v', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 01:15:32', '2026-03-18 12:22:27', NULL),
(58, 15, 'nv0u80eh5cd5n1b3fnbacqksb7', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 01:16:27', '2026-03-18 12:14:23', NULL),
(59, 12, 'pjqdq2ojg127d9qi0jntvg28rd', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-18 01:16:27', '2026-03-18 12:14:54', NULL),
(68, 14, '4ddrbe94km28vtimi5u7p00jqs', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-18 01:16:27', '2026-03-18 05:16:28', NULL),
(871, 10, 't669qqh4na914lp2avhcqkg81j', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-18 01:26:53', '2026-03-18 01:46:35', NULL),
(2391, 13, 'g2km1m3e5meroemk0u6eca2n94', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-18 01:51:15', '2026-03-18 02:18:41', NULL),
(5495, 26, '1jtobovb8e8155qsvqn175hp8c', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-18 02:09:09', '2026-03-18 02:45:13', NULL),
(12170, 10, 'bil15tl1ft8oa03c40lhlosvc6', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-18 02:46:10', '2026-03-18 08:30:34', '2026-03-18 08:30:36'),
(14570, 26, 'p3chp65t33b1mtae04457cosg4', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-18 03:13:35', '2026-03-18 03:20:06', '2026-03-18 03:20:06'),
(16520, 10, 'coo2uijniab67s14d2mqjp1sci', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-18 08:30:53', '2026-03-18 11:31:29', NULL),
(18908, 13, 'ct9vgj6facmaa2iaf34csfc2oh', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-18 10:28:23', '2026-03-18 10:33:53', NULL),
(18934, 14, 'vk44lt3aa8icln3945u9njfmtu', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-18 10:28:30', '2026-03-18 10:33:55', NULL),
(27007, 12, 'pbb048semktj1igqdrpfcuf92l', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 04:05:16', '2026-03-20 04:24:38', NULL),
(27015, 13, '269feejn9vsu2569i4gc1dh574', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-20 04:05:24', '2026-03-20 04:11:39', NULL),
(27026, 15, 'fn6naqbk2u6o798neoubgijst5', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 04:05:44', '2026-03-20 06:34:21', NULL),
(27030, 14, '4flqrrfs9h8t702ucckrkh8bgk', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-20 04:05:59', '2026-03-20 06:34:15', NULL),
(27325, 16, 'j1uica1h9jfh1r1p7fqt8upf48', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 04:11:29', '2026-03-20 06:26:43', NULL);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `department_notifications`
--
ALTER TABLE `department_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `transaction_handoffs`
--
ALTER TABLE `transaction_handoffs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `transaction_updates`
--
ALTER TABLE `transaction_updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28379;

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
