-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 24, 2026 at 08:27 PM
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
-- Database: `sdo-ftms`
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
(9, 13, 'transaction_handoff_forward', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"from_dept\":\"supply\",\"to_dept\":\"accounting\"}', '2026-03-18 02:18:38'),
(10, 14, 'transaction_handoff_receive', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"from_dept\":\"supply\",\"to_dept\":\"accounting\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-18 02:19:06'),
(11, 14, 'transaction_update', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"stage\":\"accounting\",\"status\":\"FOR ORS\",\"remarks\":\"complete attachment\",\"dv_amount\":\"\"}', '2026-03-18 02:20:19'),
(12, 14, 'transaction_handoff_forward', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"from_dept\":\"accounting\",\"to_dept\":\"budget\"}', '2026-03-18 02:20:29'),
(13, 15, 'transaction_handoff_receive', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"from_dept\":\"accounting\",\"to_dept\":\"budget\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-18 02:20:51'),
(14, 14, 'transaction_update', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"stage\":\"accounting\",\"status\":\"FOR ORS\",\"remarks\":\"incomplete\",\"dv_amount\":\"\"}', '2026-03-18 02:26:14'),
(15, 15, 'transaction_update', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"stage\":\"budget\",\"status\":\"FOR PAYMENT\",\"remarks\":\"obligated\",\"dv_number\":\"876\",\"dv_date\":\"2026-03-27\",\"demandability\":\"Not Yet Due and Demandable\"}', '2026-03-18 02:31:34'),
(16, 15, 'transaction_handoff_forward', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"from_dept\":\"budget\",\"to_dept\":\"accounting\"}', '2026-03-18 02:31:54'),
(17, 14, 'transaction_handoff_receive', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"from_dept\":\"budget\",\"to_dept\":\"accounting\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-18 02:32:14'),
(18, 14, 'transaction_update', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"stage\":\"accounting\",\"status\":\"FOR VOUCHER\",\"remarks\":\"for payment\",\"dv_amount\":\"\"}', '2026-03-18 02:32:49'),
(19, 14, 'transaction_handoff_forward', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"from_dept\":\"accounting\",\"to_dept\":\"cashier\"}', '2026-03-18 02:32:55'),
(20, 16, 'transaction_handoff_receive', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"from_dept\":\"accounting\",\"to_dept\":\"cashier\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-18 02:39:01'),
(21, 16, 'transaction_update', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"stage\":\"cashier\",\"status\":\"For ACIC\",\"remarks\":\"Amount: 940288.99\",\"or_number\":\"345654\",\"or_date\":\"2026-04-02\",\"payment_date\":\"2026-04-02\",\"landbank_ref\":\"940288.99\"}', '2026-03-18 02:41:16'),
(22, 16, 'transaction_notify_supplier', 'transaction', 51, '{\"transaction_id\":51,\"po_number\":\"2025030039\",\"supplier_id\":8,\"message\":\"Your PO 2025030039 is now marked as COMPLETED. Please check the portal for details.\"}', '2026-03-18 02:42:56'),
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
(35, 16, 'login', 'user', 16, 'Successful login', '2026-03-20 04:11:29'),
(36, 12, 'login', 'user', 12, 'Successful login', '2026-03-21 12:41:07'),
(37, 12, 'transaction_update', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"stage\":\"procurement\",\"status\":\"FOR SUPPLY REVIEW\",\"remarks\":\"\"}', '2026-03-21 12:44:05'),
(38, 15, 'login', 'user', 15, 'Successful login', '2026-03-21 12:51:13'),
(39, 12, 'transaction_handoff_forward', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"from_dept\":\"procurement\",\"to_dept\":\"budget\"}', '2026-03-21 13:13:01'),
(40, 15, 'transaction_handoff_receive', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"from_dept\":\"procurement\",\"to_dept\":\"budget\"}', '2026-03-21 13:16:51'),
(41, 15, 'transaction_update', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"stage\":\"budget\",\"status\":\"FOR PAYMENT\",\"remarks\":\"\",\"ors_number\":\"\",\"ors_date\":\"\",\"demandability\":\"\"}', '2026-03-21 13:17:20'),
(42, 15, 'transaction_handoff_forward', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"from_dept\":\"budget\",\"to_dept\":\"supply\"}', '2026-03-21 13:18:15'),
(43, 13, 'transaction_handoff_receive', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"from_dept\":\"budget\",\"to_dept\":\"supply\"}', '2026-03-21 13:19:22'),
(44, 13, 'transaction_update', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"stage\":\"supply\",\"status\":\"PARTIAL DELIVERY\",\"remarks\":\"\",\"delivery_receipt\":\"\",\"sales_invoice\":\"\"}', '2026-03-21 13:19:48'),
(45, 13, 'transaction_handoff_forward', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"from_dept\":\"supply\",\"to_dept\":\"accounting\"}', '2026-03-21 13:19:54'),
(46, 14, 'transaction_handoff_receive', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"from_dept\":\"supply\",\"to_dept\":\"accounting\"}', '2026-03-21 13:20:04'),
(47, 14, 'transaction_update', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"stage\":\"accounting\",\"status\":\"FOR ORS\",\"remarks\":\"must be the pre-acc\",\"dv_amount\":\"\"}', '2026-03-21 13:20:41'),
(48, 14, 'transaction_update', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"stage\":\"accounting\",\"status\":\"FOR VOUCHER\",\"remarks\":\"must be the post-acc\",\"dv_amount\":\"10000\"}', '2026-03-21 13:57:56'),
(49, 14, 'transaction_handoff_forward', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"from_dept\":\"accounting\",\"to_dept\":\"budget\"}', '2026-03-21 13:59:00'),
(50, 15, 'transaction_handoff_receive', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"from_dept\":\"accounting\",\"to_dept\":\"budget\"}', '2026-03-21 13:59:05'),
(51, 15, 'transaction_update', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"stage\":\"budget\",\"status\":\"ACCOUNTS PAYABLE\",\"remarks\":\"\",\"ors_number\":\"\",\"ors_date\":\"\",\"demandability\":\"\"}', '2026-03-21 13:59:12'),
(52, 15, 'transaction_update', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"stage\":\"budget\",\"status\":\"ACCOUNTS PAYABLE\",\"remarks\":\"\",\"ors_number\":\"876\",\"ors_date\":\"2026-03-21\",\"demandability\":\"\"}', '2026-03-21 13:59:28'),
(53, 15, 'transaction_handoff_forward', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"from_dept\":\"budget\",\"to_dept\":\"cashier\"}', '2026-03-21 13:59:36'),
(54, 16, 'login', 'user', 16, 'Successful login', '2026-03-21 13:59:55'),
(55, 16, 'transaction_handoff_receive', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"from_dept\":\"budget\",\"to_dept\":\"cashier\"}', '2026-03-21 14:00:06'),
(56, 16, 'transaction_notify_supplier', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"supplier_id\":8,\"message\":\"Your PO 34 is now marked as COMPLETED. Please check the portal for details.\"}', '2026-03-21 14:00:43'),
(57, 16, 'transaction_notify_supplier', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"supplier_id\":8,\"message\":\"Your PO 34 is now marked as COMPLETED. Please check the portal for details.\"}', '2026-03-21 14:02:07'),
(58, 16, 'transaction_notify_supplier', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"supplier_id\":8,\"message\":\"Your PO 34 is now marked as COMPLETED. Please check the portal for details.\"}', '2026-03-21 14:05:09'),
(59, 16, 'transaction_notify_supplier', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"supplier_id\":8,\"message\":\"Your PO 34 is now marked as COMPLETED. Please check the portal for details.\"}', '2026-03-21 14:23:07'),
(60, 16, 'transaction_notify_supplier', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"supplier_id\":8,\"message\":\"Your PO 34 is now marked as COMPLETED. Please check the portal for details.\"}', '2026-03-21 14:23:20'),
(61, 16, 'transaction_notify_supplier', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"supplier_id\":8,\"message\":\"Your PO 34 is now marked as COMPLETED. Please check the portal for details.\"}', '2026-03-21 14:23:36'),
(62, 16, 'transaction_update', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"stage\":\"cashier\",\"status\":\"For OR Issuance\",\"remarks\":\"Amount: 10000.00\",\"or_number\":\"\",\"or_date\":\"\",\"payment_date\":\"\",\"landbank_ref\":\"10000.00\"}', '2026-03-21 14:31:20'),
(63, 16, 'transaction_update', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"stage\":\"cashier\",\"status\":\"For ACIC\",\"remarks\":\"Amount: 10000.00\",\"or_number\":\"987\",\"or_date\":\"2026-03-21\",\"payment_date\":\"2026-03-21\",\"landbank_ref\":\"10000.00\"}', '2026-03-21 14:31:40'),
(64, 16, 'transaction_update', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"stage\":\"cashier\",\"status\":\"For SDS\\/ASDS Approval\",\"remarks\":\"Amount: 10000.00\",\"or_number\":\"987\",\"or_date\":\"2026-03-21\",\"payment_date\":\"2026-03-21\",\"landbank_ref\":\"10000.00\"}', '2026-03-21 14:37:19'),
(65, 16, 'transaction_update', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"stage\":\"cashier\",\"status\":\"COMPLETED\",\"remarks\":\"Amount: 10000.00\",\"or_number\":\"987\",\"or_date\":\"2026-03-21\",\"payment_date\":\"2026-03-21\",\"landbank_ref\":\"10000.00\"}', '2026-03-21 14:38:05'),
(66, 16, 'transaction_notify_supplier', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"supplier_id\":8,\"message\":\"Your PO 34 is now marked as COMPLETED. Please check the portal for details.\"}', '2026-03-21 14:38:21'),
(67, 16, 'login', 'user', 16, 'Successful login', '2026-03-21 21:45:47'),
(68, 16, 'transaction_update', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"stage\":\"cashier\",\"status\":\"For SDS\\/ASDS Approval\",\"remarks\":\"Amount: 10000.00\",\"or_number\":\"987\",\"or_date\":\"2026-03-21\",\"payment_date\":\"2026-03-21\",\"landbank_ref\":\"10000.00\"}', '2026-03-21 21:45:58'),
(69, 16, 'transaction_notify_supplier', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"supplier_id\":8,\"message\":\"Your PO 34 is now marked as COMPLETED. Please check the portal for details.\"}', '2026-03-21 21:49:58'),
(70, 16, 'transaction_notify_supplier', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"supplier_id\":8,\"message\":\"Your PO 34 is now marked as COMPLETED. Please check the portal for details.\"}', '2026-03-21 21:50:31'),
(71, 16, 'transaction_notify_supplier', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"supplier_id\":8,\"message\":\"Your PO 34 is now marked as COMPLETED. Please check the portal for details.\"}', '2026-03-21 21:51:10'),
(72, 16, 'transaction_notify_supplier', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"supplier_id\":8,\"message\":\"Your PO 34 is now marked as COMPLETED. Please check the portal for details.\"}', '2026-03-21 22:12:12'),
(73, 16, 'transaction_notify_supplier', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"supplier_id\":8,\"message\":\"Your PO 34 is now marked as COMPLETED. Please check the portal for details.\"}', '2026-03-21 22:15:20'),
(74, 16, 'transaction_notify_supplier', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"supplier_id\":8,\"message\":\"Your PO 34 is now marked as COMPLETED. Please check the portal for details.\"}', '2026-03-21 22:38:53'),
(75, 16, 'transaction_notify_supplier', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"supplier_id\":8,\"message\":\"okay\"}', '2026-03-21 22:46:12'),
(76, 16, 'transaction_notify_supplier', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"supplier_id\":8,\"message\":\"okay\"}', '2026-03-21 22:47:46'),
(77, 16, 'transaction_notify_supplier', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"supplier_id\":8,\"message\":\"Your PO 34 is now marked as COMPLETED. Please check the portal for details.\"}', '2026-03-21 22:59:24'),
(78, 15, 'login', 'user', 15, 'Successful login', '2026-03-22 00:31:14'),
(79, 13, 'login', 'user', 13, 'Successful login', '2026-03-22 00:33:15'),
(80, 14, 'login', 'user', 14, 'Successful login', '2026-03-22 00:33:30'),
(81, 16, 'transaction_handoff_forward', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"from_dept\":\"cashier\",\"to_dept\":\"procurement\"}', '2026-03-22 00:34:01'),
(82, 12, 'transaction_handoff_receive', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"from_dept\":\"cashier\",\"to_dept\":\"procurement\"}', '2026-03-22 00:34:25'),
(83, 12, 'transaction_handoff_forward', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"from_dept\":\"procurement\",\"to_dept\":\"supply\"}', '2026-03-22 00:37:44'),
(84, 13, 'transaction_handoff_receive', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"from_dept\":\"procurement\",\"to_dept\":\"supply\"}', '2026-03-22 00:54:54'),
(85, 13, 'transaction_handoff_forward', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"from_dept\":\"supply\",\"to_dept\":\"procurement\"}', '2026-03-22 00:56:18'),
(86, 12, 'transaction_handoff_receive', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"from_dept\":\"supply\",\"to_dept\":\"procurement\"}', '2026-03-22 00:56:45'),
(87, 12, 'transaction_handoff_forward', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"from_dept\":\"procurement\",\"to_dept\":\"cashier\"}', '2026-03-22 01:10:33'),
(88, 16, 'login', 'user', 16, 'Successful login', '2026-03-22 01:10:53'),
(89, 16, 'transaction_handoff_receive', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"from_dept\":\"procurement\",\"to_dept\":\"cashier\"}', '2026-03-22 01:11:28'),
(90, 16, 'login', 'user', 16, 'Successful login', '2026-03-22 04:16:01'),
(91, 12, 'transaction_update', 'transaction', 54, '{\"transaction_id\":54,\"po_number\":\"234\",\"stage\":\"procurement\",\"status\":\"FOR SUPPLY REVIEW\",\"remarks\":\"\"}', '2026-03-22 04:20:21'),
(92, 12, 'transaction_delete', 'transaction', 54, '{\"transaction_id\":54,\"po_number\":\"234\",\"supplier_id\":8}', '2026-03-22 04:25:41'),
(93, 12, 'transaction_update', 'transaction', 55, '{\"transaction_id\":55,\"po_number\":\"23456\",\"stage\":\"procurement\",\"status\":\"FOR SUPPLY REVIEW\",\"remarks\":\"\"}', '2026-03-22 04:26:28'),
(94, 12, 'transaction_handoff_forward', 'transaction', 55, '{\"transaction_id\":55,\"po_number\":\"23456\",\"from_dept\":\"procurement\",\"to_dept\":\"supply\"}', '2026-03-22 04:26:37'),
(95, 13, 'transaction_handoff_receive', 'transaction', 55, '{\"transaction_id\":55,\"po_number\":\"23456\",\"from_dept\":\"procurement\",\"to_dept\":\"supply\"}', '2026-03-22 04:27:32'),
(96, 13, 'transaction_update', 'transaction', 55, '{\"transaction_id\":55,\"po_number\":\"23456\",\"stage\":\"supply\",\"status\":\"PARTIAL DELIVERY\",\"remarks\":\"\",\"delivery_receipt\":\"\",\"sales_invoice\":\"\"}', '2026-03-22 04:28:10'),
(97, 16, 'login', 'user', 16, 'Successful login', '2026-03-22 04:50:09'),
(98, 16, 'transaction_update', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"stage\":\"cashier\",\"status\":\"COMPLETED\",\"remarks\":\"Amount: 10000.00\",\"or_number\":\"987\",\"or_date\":\"2026-03-21\",\"payment_date\":\"2026-03-21\",\"landbank_ref\":\"10000.00\"}', '2026-03-22 05:16:00'),
(99, 12, 'transaction_delete', 'transaction', 55, '{\"transaction_id\":55,\"po_number\":\"23456\",\"supplier_id\":8}', '2026-03-22 06:11:12'),
(100, 12, 'transaction_update', 'transaction', 56, '{\"transaction_id\":56,\"po_number\":\"123454\",\"stage\":\"procurement\",\"status\":\"FOR SUPPLY REVIEW\",\"remarks\":\"\"}', '2026-03-22 06:20:35'),
(101, 12, 'transaction_delete', 'transaction', 56, '{\"transaction_id\":56,\"po_number\":\"123454\",\"supplier_id\":8}', '2026-03-22 06:45:27'),
(102, 12, 'transaction_update', 'transaction', 57, '{\"transaction_id\":57,\"po_number\":\"12345\",\"stage\":\"procurement\",\"status\":\"FOR SUPPLY REVIEW\",\"remarks\":\"\"}', '2026-03-22 06:47:24'),
(103, 12, 'transaction_handoff_forward', 'transaction', 57, '{\"transaction_id\":57,\"po_number\":\"12345\",\"from_dept\":\"procurement\",\"to_dept\":\"supply\"}', '2026-03-22 06:47:30'),
(104, 13, 'transaction_handoff_receive', 'transaction', 57, '{\"transaction_id\":57,\"po_number\":\"12345\",\"from_dept\":\"procurement\",\"to_dept\":\"supply\"}', '2026-03-22 06:48:25'),
(105, 12, 'transaction_delete', 'transaction', 59, '{\"transaction_id\":59,\"po_number\":\"234\",\"supplier_id\":8}', '2026-03-22 07:22:26'),
(106, 12, 'transaction_delete', 'transaction', 58, '{\"transaction_id\":58,\"po_number\":\"gh67\",\"supplier_id\":8}', '2026-03-22 07:22:31'),
(107, 12, 'transaction_delete', 'transaction', 57, '{\"transaction_id\":57,\"po_number\":\"12345\",\"supplier_id\":8}', '2026-03-22 07:22:34'),
(108, 12, 'transaction_delete', 'transaction', 53, '{\"transaction_id\":53,\"po_number\":\"2345\",\"supplier_id\":8}', '2026-03-22 07:22:37'),
(109, 12, 'transaction_delete', 'transaction', 61, '{\"transaction_id\":61,\"po_number\":\"2345\",\"supplier_id\":8}', '2026-03-22 07:27:07'),
(110, 12, 'transaction_update', 'transaction', 60, '{\"transaction_id\":60,\"po_number\":\"8765\",\"stage\":\"procurement\",\"status\":\"FOR SUPPLY REVIEW\",\"remarks\":\"\"}', '2026-03-22 08:11:06'),
(111, 12, 'transaction_handoff_forward', 'transaction', 60, '{\"transaction_id\":60,\"po_number\":\"8765\",\"from_dept\":\"procurement\",\"to_dept\":\"supply\"}', '2026-03-22 08:11:25'),
(112, 13, 'transaction_handoff_receive', 'transaction', 60, '{\"transaction_id\":60,\"po_number\":\"8765\",\"from_dept\":\"procurement\",\"to_dept\":\"supply\"}', '2026-03-22 08:11:53'),
(113, 12, 'transaction_delete', 'transaction', 60, '{\"transaction_id\":60,\"po_number\":\"8765\",\"supplier_id\":8}', '2026-03-22 08:21:52'),
(114, 15, 'login', 'user', 15, 'Successful login', '2026-03-22 08:22:15'),
(115, 12, 'transaction_update', 'transaction', 63, '{\"transaction_id\":63,\"po_number\":\"09090\",\"stage\":\"procurement\",\"status\":\"FOR SUPPLY REVIEW\",\"remarks\":\"\"}', '2026-03-22 08:23:29'),
(116, 12, 'transaction_handoff_forward', 'transaction', 63, '{\"transaction_id\":63,\"po_number\":\"09090\",\"from_dept\":\"procurement\",\"to_dept\":\"supply\"}', '2026-03-22 08:23:36'),
(117, 13, 'transaction_handoff_receive', 'transaction', 63, '{\"transaction_id\":63,\"po_number\":\"09090\",\"from_dept\":\"procurement\",\"to_dept\":\"supply\"}', '2026-03-22 08:23:59'),
(118, 12, 'transaction_update', 'transaction', 64, '{\"transaction_id\":64,\"po_number\":\"123\",\"stage\":\"procurement\",\"status\":\"FOR SUPPLY REVIEW\",\"remarks\":\"\"}', '2026-03-22 08:25:02'),
(119, 12, 'transaction_handoff_forward', 'transaction', 64, '{\"transaction_id\":64,\"po_number\":\"123\",\"from_dept\":\"procurement\",\"to_dept\":\"supply\"}', '2026-03-22 08:25:29'),
(120, 13, 'transaction_handoff_receive', 'transaction', 64, '{\"transaction_id\":64,\"po_number\":\"123\",\"from_dept\":\"procurement\",\"to_dept\":\"supply\"}', '2026-03-22 08:25:51'),
(121, 13, 'transaction_update', 'transaction', 64, '{\"transaction_id\":64,\"po_number\":\"123\",\"stage\":\"supply\",\"status\":\"PARTIAL DELIVERY\",\"remarks\":\"\",\"delivery_receipt\":\"\"}', '2026-03-22 08:26:02'),
(122, 13, 'transaction_handoff_forward', 'transaction', 64, '{\"transaction_id\":64,\"po_number\":\"123\",\"from_dept\":\"supply\",\"to_dept\":\"accounting\"}', '2026-03-22 08:26:21'),
(123, 14, 'transaction_handoff_receive', 'transaction', 64, '{\"transaction_id\":64,\"po_number\":\"123\",\"from_dept\":\"supply\",\"to_dept\":\"accounting\"}', '2026-03-22 08:26:37'),
(124, 14, 'transaction_update', 'transaction', 64, '{\"transaction_id\":64,\"po_number\":\"123\",\"stage\":\"accounting\",\"status\":\"FOR ORS\",\"remarks\":\"\",\"dv_amount\":\"\"}', '2026-03-22 08:26:43'),
(125, 14, 'transaction_handoff_forward', 'transaction', 64, '{\"transaction_id\":64,\"po_number\":\"123\",\"from_dept\":\"accounting\",\"to_dept\":\"budget\"}', '2026-03-22 08:27:25'),
(126, 15, 'transaction_handoff_receive', 'transaction', 64, '{\"transaction_id\":64,\"po_number\":\"123\",\"from_dept\":\"accounting\",\"to_dept\":\"budget\"}', '2026-03-22 08:27:35'),
(127, 12, 'transaction_delete', 'transaction', 64, '{\"transaction_id\":64,\"po_number\":\"123\",\"supplier_id\":8}', '2026-03-22 08:41:15'),
(128, 10, 'login', 'user', 10, 'Successful login', '2026-03-22 09:42:39'),
(129, 10, 'login', 'user', 10, 'Successful login', '2026-03-22 09:43:05'),
(130, 10, 'login', 'user', 10, 'Successful login', '2026-03-22 09:44:13'),
(131, 10, 'logout', 'user', 10, 'User logged out', '2026-03-22 09:44:37'),
(132, 10, 'reset_password', 'user', 16, '{\"username\":\"cashier\",\"new_password\":\"12345\"}', '2026-03-22 09:58:08'),
(133, NULL, 'login_failed', 'user', NULL, '{\"attempted_username\":\"erica\",\"reason\":\"unknown_username\"}', '2026-03-22 09:58:41'),
(134, 10, 'update_user', 'user', 16, '{\"username\":\"erica\",\"role_id\":3,\"supplier_id\":null}', '2026-03-22 10:00:54'),
(135, 16, 'login', 'user', 16, 'Successful login', '2026-03-22 10:01:06'),
(136, 16, 'update_account', 'user', 16, '{\"old_username\":\"erica\",\"new_username\":\"erica\",\"password_changed\":true}', '2026-03-22 10:04:56'),
(137, 16, 'logout', 'user', 16, 'User logged out', '2026-03-22 10:05:08'),
(138, 16, 'login', 'user', 16, 'Successful login', '2026-03-22 10:05:13'),
(139, 16, 'update_account', 'user', 16, '{\"old_username\":\"erica\",\"new_username\":\"erica shaine\",\"password_changed\":false}', '2026-03-22 10:08:16'),
(140, 12, 'transaction_update', 'transaction', 65, '{\"transaction_id\":65,\"po_number\":\"asdfsads\",\"stage\":\"procurement\",\"status\":\"FOR SUPPLY REVIEW\",\"remarks\":\"\"}', '2026-03-22 10:13:10'),
(141, 16, 'transaction_update', 'transaction', 65, '{\"transaction_id\":65,\"po_number\":\"asdfsads\",\"stage\":\"procurement\",\"status\":\"FOR SUPPLY REVIEW\",\"remarks\":\"asdd\"}', '2026-03-22 10:19:09'),
(142, 10, 'update_user', 'user', 16, '{\"username\":\"erica shaine\",\"role_id\":7,\"supplier_id\":null}', '2026-03-22 10:29:20'),
(143, 16, 'logout', 'user', 16, 'User logged out', '2026-03-22 10:30:11'),
(144, 16, 'login', 'user', 16, 'Successful login', '2026-03-22 10:30:22'),
(145, 10, 'update_user', 'user', 16, '{\"username\":\"erica\",\"role_id\":7,\"supplier_id\":null}', '2026-03-22 10:31:17'),
(146, 15, 'login', 'user', 15, 'Successful login', '2026-03-23 01:19:36'),
(147, 16, 'login', 'user', 16, 'Successful login', '2026-03-23 08:19:55'),
(149, 26, 'login', 'user', 26, 'Successful login (Google OAuth)', '2026-03-23 19:33:16'),
(150, 26, 'logout', 'user', 26, 'User logged out', '2026-03-23 20:14:33'),
(151, 28, 'login', 'user', 28, 'Successful login', '2026-03-23 22:51:17'),
(152, 12, 'login', 'user', 12, 'Successful login', '2026-03-24 00:11:55'),
(153, 12, 'logout', 'user', 12, 'User logged out', '2026-03-24 00:16:41'),
(154, NULL, 'login_failed', 'user', NULL, '{\"attempted_username\":\"cashier\",\"reason\":\"unknown_username\"}', '2026-03-24 00:16:47'),
(155, 16, 'login', 'user', 16, 'Successful login', '2026-03-24 00:16:53'),
(156, 12, 'login', 'user', 12, 'Successful login', '2026-03-24 00:59:05'),
(157, 13, 'login', 'user', 13, 'Successful login', '2026-03-24 00:59:10'),
(158, 14, 'login', 'user', 14, 'Successful login', '2026-03-24 00:59:18'),
(159, 16, 'logout', 'user', 16, 'User logged out', '2026-03-24 00:59:24'),
(160, 15, 'login', 'user', 15, 'Successful login', '2026-03-24 00:59:29'),
(161, 16, 'login', 'user', 16, 'Successful login', '2026-03-24 00:59:36'),
(162, 12, 'login', 'user', 12, 'Successful login', '2026-03-24 02:16:30'),
(163, 28, 'login', 'user', 28, 'Successful login', '2026-03-24 02:53:40'),
(164, NULL, 'login_failed', 'user', NULL, '{\"attempted_username\":\"cashier\",\"reason\":\"unknown_username\"}', '2026-03-24 02:54:25'),
(165, 26, 'login', 'user', 26, 'Successful login (Google OAuth)', '2026-03-24 02:55:45'),
(166, 26, 'login', 'user', 26, 'Successful login (Google OAuth)', '2026-03-24 03:15:39'),
(167, 26, 'login', 'user', 26, 'Successful login (Google OAuth)', '2026-03-24 05:01:10'),
(168, 28, 'login', 'user', 28, 'Successful login', '2026-03-24 05:18:32'),
(169, 28, 'logout', 'user', 28, 'User logged out', '2026-03-24 05:20:19'),
(170, 26, 'login', 'user', 26, 'Successful login (Google OAuth)', '2026-03-24 05:20:27'),
(171, 26, 'logout', 'user', 26, 'User logged out', '2026-03-24 05:20:29'),
(172, 28, 'login', 'user', 28, 'Successful login', '2026-03-24 05:20:37'),
(173, 28, 'logout', 'user', 28, 'User logged out', '2026-03-24 05:20:43'),
(176, NULL, 'login_failed', 'user', NULL, '{\"attempted_username\":\"jacob\",\"reason\":\"unknown_username\"}', '2026-03-24 05:41:00'),
(177, 32, 'login', 'user', 32, 'Successful login', '2026-03-24 05:41:10'),
(178, 32, 'logout', 'user', 32, 'User logged out', '2026-03-24 06:03:10'),
(179, 28, 'login', 'user', 28, 'Successful login', '2026-03-24 06:03:32'),
(180, 28, 'logout', 'user', 28, 'User logged out', '2026-03-24 06:07:14'),
(181, 32, 'login', 'user', 32, 'Successful login', '2026-03-24 06:07:20'),
(182, NULL, 'login_failed', 'user', NULL, '{\"attempted_username\":\"cashier\",\"reason\":\"unknown_username\"}', '2026-03-24 06:21:05'),
(183, 16, 'login', 'user', 16, 'Successful login', '2026-03-24 06:21:09'),
(184, 32, 'update_account', 'user', 32, '{\"old_username\":\"prop2\",\"new_username\":\"prop-2\",\"password_changed\":false}', '2026-03-24 06:46:38'),
(185, 32, 'update_account', 'user', 32, '{\"old_username\":\"prop-2\",\"new_username\":\"prop2\",\"password_changed\":false}', '2026-03-24 06:46:56'),
(186, 32, 'update_account', 'user', 32, '{\"old_username\":\"prop2\",\"new_username\":\"prop-2\",\"password_changed\":false}', '2026-03-24 06:47:10'),
(187, 32, 'update_account', 'user', 32, '{\"old_username\":\"prop-2\",\"new_username\":\"prop2\",\"password_changed\":false}', '2026-03-24 06:47:56'),
(188, 12, 'transaction_update', 'transaction', 66, '{\"transaction_id\":66,\"po_number\":\"234\",\"stage\":\"procurement\",\"status\":\"FOR SUPPLY REVIEW\",\"remarks\":\"\"}', '2026-03-24 06:48:33'),
(189, 12, 'transaction_delete', 'transaction', 66, '{\"transaction_id\":66,\"po_number\":\"234\",\"supplier_id\":8}', '2026-03-24 06:51:33'),
(190, 12, 'transaction_delete', 'transaction', 67, '{\"transaction_id\":67,\"po_number\":\"23\",\"supplier_id\":8}', '2026-03-24 06:54:45'),
(191, 12, 'transaction_update', 'transaction', 68, '{\"transaction_id\":68,\"po_number\":\"87\",\"stage\":\"procurement\",\"status\":\"FOR SUPPLY REVIEW\",\"remarks\":\"\"}', '2026-03-24 06:55:13'),
(192, 16, 'login', 'user', 16, 'Successful login', '2026-03-24 07:16:06'),
(193, 16, 'transaction_notify_supplier', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"supplier_id\":8,\"message\":\"Your PO 34 is now marked as COMPLETED. Please check the portal for details.\"}', '2026-03-24 07:22:55'),
(194, 16, 'transaction_notify_supplier', 'transaction', 52, '{\"transaction_id\":52,\"po_number\":\"34\",\"supplier_id\":8,\"message\":\"Your PO 34 is now marked as COMPLETED. Please check the portal for details.\"}', '2026-03-24 07:23:19'),
(195, 16, 'login', 'user', 16, 'Successful login', '2026-03-24 17:34:09'),
(196, 26, 'login', 'user', 26, 'Successful login (Google OAuth)', '2026-03-24 17:45:02'),
(197, 32, 'login', 'user', 32, 'Successful login', '2026-03-24 17:46:01'),
(198, 32, 'logout', 'user', 32, 'User logged out', '2026-03-24 17:46:16'),
(199, 28, 'login', 'user', 28, 'Successful login', '2026-03-24 17:46:22'),
(200, 28, 'logout', 'user', 28, 'User logged out', '2026-03-24 17:46:50'),
(201, 32, 'login', 'user', 32, 'Successful login', '2026-03-24 17:46:58'),
(202, 13, 'login', 'user', 13, 'Successful login', '2026-03-24 17:53:12'),
(203, 26, 'logout', 'user', 26, 'User logged out', '2026-03-24 18:16:55'),
(204, NULL, 'login_failed', 'user', NULL, '{\"attempted_username\":\"supplier1\",\"reason\":\"unknown_username\"}', '2026-03-24 18:17:00'),
(205, NULL, 'login_failed', 'user', NULL, '{\"attempted_username\":\"supplier\",\"reason\":\"unknown_username\"}', '2026-03-24 18:17:05'),
(206, NULL, 'login_failed', 'user', NULL, '{\"attempted_username\":\"supplier1\",\"reason\":\"unknown_username\"}', '2026-03-24 18:17:30'),
(207, 10, 'login', 'user', 10, 'Successful login', '2026-03-24 18:17:51'),
(208, 10, 'logout', 'user', 10, 'User logged out', '2026-03-24 18:18:32'),
(209, 27, 'login', 'user', 27, 'Successful login', '2026-03-24 18:18:36'),
(210, 32, 'login', 'user', 32, 'Successful login', '2026-03-24 18:21:22'),
(211, 27, 'logout', 'user', 27, 'User logged out', '2026-03-24 18:40:05'),
(212, 12, 'login', 'user', 12, 'Successful login (Google OAuth)', '2026-03-24 18:46:07'),
(213, 12, 'logout', 'user', 12, 'User logged out', '2026-03-24 18:46:34'),
(214, 12, 'login', 'user', 12, 'Successful login (Google OAuth)', '2026-03-24 18:46:42'),
(215, 12, 'logout', 'user', 12, 'User logged out', '2026-03-24 18:54:10'),
(216, 12, 'login', 'user', 12, 'Successful login (Google OAuth)', '2026-03-24 18:54:17'),
(217, 12, 'logout', 'user', 12, 'User logged out', '2026-03-24 18:54:23'),
(218, 12, 'login', 'user', 12, 'Successful login (Google OAuth)', '2026-03-24 18:54:33'),
(219, 12, 'logout', 'user', 12, 'User logged out', '2026-03-24 18:54:37'),
(220, 12, 'login', 'user', 12, 'Successful login (Google OAuth)', '2026-03-24 19:15:19'),
(221, 12, 'logout', 'user', 12, 'User logged out', '2026-03-24 19:15:30'),
(222, 26, 'login', 'user', 26, 'Successful login (Google OAuth)', '2026-03-24 19:16:52'),
(223, 16, 'login', 'user', 16, 'Successful login', '2026-03-24 19:20:02');

-- --------------------------------------------------------

--
-- Table structure for table `app_settings`
--

CREATE TABLE `app_settings` (
  `setting_key` varchar(128) NOT NULL,
  `setting_value` varchar(255) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `app_settings`
--

INSERT INTO `app_settings` (`setting_key`, `setting_value`, `updated_at`) VALUES
('last_notify_message', 'Your PO 34 is now marked as COMPLETED. Please check the portal for details.', '2026-03-21 22:59:20'),
('last_notify_title', 'Payment status update', '2026-03-21 22:59:20'),
('proponent_default_cc', 'rioverosjustine92@gmail.com', '2026-03-24 06:58:40'),
('proponent_default_cc_cashier', '', '2026-03-24 17:40:43'),
('proponent_default_cc_procurement', 'rioverosjustine92@gmail.com', '2026-03-24 06:55:47'),
('proponent_default_message', '2', '2026-03-24 07:11:39'),
('proponent_default_message_cashier', 'cashier', '2026-03-24 07:21:07'),
('proponent_default_message_procurement', 'proc', '2026-03-24 07:21:56'),
('proponent_default_title', '2', '2026-03-24 07:11:39'),
('proponent_default_title_cashier', 'cashier', '2026-03-24 07:21:07'),
('proponent_default_title_procurement', 'proc', '2026-03-24 07:21:56');

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
(1, 'supply', 57, 'Handoff Forwarded', 'PROCUREMENT forwarded PO 12345 to SUPPLY. Please receive it.', 'transaction_view.php?id=57', 1, '2026-03-22 14:47:30'),
(2, 'procurement', 57, 'Handoff Received', 'SUPPLY has received PO 12345 from PROCUREMENT.', 'transaction_view.php?id=57', 1, '2026-03-22 14:48:20'),
(3, 'supply', 60, 'Handoff Forwarded', 'PROCUREMENT forwarded PO 8765 to SUPPLY. Please receive it.', 'transaction_view.php?id=60', 1, '2026-03-22 16:11:25'),
(4, 'procurement', 60, 'Handoff Received', 'SUPPLY has received PO 8765 from PROCUREMENT.', 'transaction_view.php?id=60', 1, '2026-03-22 16:11:48'),
(5, 'supply', 63, 'Handoff Forwarded', 'PROCUREMENT forwarded PO 09090 to SUPPLY. Please receive it.', 'transaction_view.php?id=63', 1, '2026-03-22 16:23:36'),
(6, 'procurement', 63, 'Handoff Received', 'SUPPLY has received PO 09090 from PROCUREMENT.', 'transaction_view.php?id=63', 1, '2026-03-22 16:23:54'),
(7, 'supply', 64, 'Handoff Forwarded', 'PROCUREMENT forwarded PO 123 to SUPPLY. Please receive it.', 'transaction_view.php?id=64', 0, '2026-03-22 16:25:29'),
(8, 'procurement', 64, 'Handoff Received', 'SUPPLY has received PO 123 from PROCUREMENT.', 'transaction_view.php?id=64', 0, '2026-03-22 16:25:46'),
(9, 'accounting', 64, 'Handoff Forwarded', 'SUPPLY forwarded PO 123 to ACCOUNTING. Please receive it.', 'transaction_view.php?id=64', 0, '2026-03-22 16:26:21'),
(10, 'supply', 64, 'Handoff Received', 'ACCOUNTING has received PO 123 from SUPPLY.', 'transaction_view.php?id=64', 0, '2026-03-22 16:26:31'),
(11, 'budget', 64, 'Handoff Forwarded', 'ACCOUNTING forwarded PO 123 to BUDGET. Please receive it.', 'transaction_view.php?id=64', 1, '2026-03-22 16:27:25'),
(12, 'accounting', 64, 'Handoff Received', 'BUDGET has received PO 123 from ACCOUNTING.', 'transaction_view.php?id=64', 0, '2026-03-22 16:27:35');

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
(2, 8, 51, 'Payment status update', 'Your PO 2025030039 is now marked as COMPLETED. Please check the portal for details.', 'transaction_view.php?id=51', 0, 0, '2026-03-18 19:06:28'),
(3, 8, 52, 'Payment status update', 'Your PO 34 is now marked as COMPLETED. Please check the portal for details.', 'transaction_view.php?id=52', 0, 0, '2026-03-21 22:00:34'),
(4, 8, 52, 'Payment status update', 'Your PO 34 is now marked as COMPLETED. Please check the portal for details.', 'transaction_view.php?id=52', 0, 0, '2026-03-21 22:02:01'),
(5, 8, 52, 'Payment status update', 'Your PO 34 is now marked as COMPLETED. Please check the portal for details.', 'transaction_view.php?id=52', 0, 0, '2026-03-21 22:05:04'),
(6, 8, 52, 'Payment status update', 'Your PO 34 is now marked as COMPLETED. Please check the portal for details.', 'transaction_view.php?id=52', 0, 0, '2026-03-21 22:23:02'),
(7, 8, 52, 'Payment status update', 'Your PO 34 is now marked as COMPLETED. Please check the portal for details.', 'transaction_view.php?id=52', 0, 0, '2026-03-21 22:23:15'),
(8, 8, 52, 'Payment status update', 'Your PO 34 is now marked as COMPLETED. Please check the portal for details.', 'transaction_view.php?id=52', 0, 0, '2026-03-21 22:23:30'),
(9, 8, 52, 'Transaction completed', 'Your PO 34 has been completed.', 'transaction_view.php?id=52', 0, 0, '2026-03-21 22:38:05'),
(10, 8, 52, 'Payment status update', 'Your PO 34 is now marked as COMPLETED. Please check the portal for details.', 'transaction_view.php?id=52', 0, 0, '2026-03-21 22:38:16'),
(11, 8, 52, 'Payment status update', 'Your PO 34 is now marked as COMPLETED. Please check the portal for details.', 'transaction_view.php?id=52', 0, 0, '2026-03-22 05:49:52'),
(12, 8, 52, 'Payment status update', 'Your PO 34 is now marked as COMPLETED. Please check the portal for details.', 'transaction_view.php?id=52', 0, 0, '2026-03-22 05:50:27'),
(13, 8, 52, 'Payment status update', 'Your PO 34 is now marked as COMPLETED. Please check the portal for details.', 'transaction_view.php?id=52', 0, 0, '2026-03-22 05:51:05'),
(14, 8, 52, 'Payment status update', 'Your PO 34 is now marked as COMPLETED. Please check the portal for details.', 'transaction_view.php?id=52', 0, 0, '2026-03-22 06:12:07'),
(15, 8, 52, 'Payment status update', 'Your PO 34 is now marked as COMPLETED. Please check the portal for details.', 'transaction_view.php?id=52', 0, 0, '2026-03-22 06:15:15'),
(16, 8, 52, 'Payment status update', 'Your PO 34 is now marked as COMPLETED. Please check the portal for details.', 'transaction_view.php?id=52', 0, 0, '2026-03-22 06:38:47'),
(17, 8, 52, 'Payment status update', 'okay', 'transaction_view.php?id=52', 0, 0, '2026-03-22 06:46:06'),
(18, 8, 52, 'bro', 'okay', 'transaction_view.php?id=52', 0, 0, '2026-03-22 06:47:42'),
(19, 8, 52, 'Payment status update', 'Your PO 34 is now marked as COMPLETED. Please check the portal for details.', 'transaction_view.php?id=52', 0, 0, '2026-03-22 06:59:20'),
(20, 8, 52, 'Transaction completed', 'Your PO 34 has been completed.', 'transaction_view.php?id=52', 0, 0, '2026-03-22 13:16:00'),
(21, 8, 52, 'Payment status update', 'Your PO 34 is now marked as COMPLETED. Please check the portal for details.', 'transaction_view.php?id=52', 0, 0, '2026-03-24 15:22:50'),
(22, 8, 52, 'Payment status update', 'Your PO 34 is now marked as COMPLETED. Please check the portal for details.', 'transaction_view.php?id=52', 0, 0, '2026-03-24 15:23:15');

-- --------------------------------------------------------

--
-- Table structure for table `proponents`
--

CREATE TABLE `proponents` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `proponents`
--

INSERT INTO `proponents` (`id`, `name`, `created_at`) VALUES
(1, 'carlo proponent', '2026-03-23 20:32:49'),
(3, 'jacob lester', '2026-03-24 05:40:51');

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
(8, 'proponent'),
(9, 'school_head'),
(2, 'supplier'),
(4, 'supply');

-- --------------------------------------------------------

--
-- Table structure for table `school_heads`
--

CREATE TABLE `school_heads` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `school_heads`
--

INSERT INTO `school_heads` (`id`, `name`, `created_at`) VALUES
(1, 'school1', '2026-03-23 22:50:59');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `created_at`) VALUES
(8, 'alpha num', '2026-03-18 02:09:09'),
(9, 'supplier1', '2026-03-23 20:27:02');

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
  `acct_status` varchar(100) DEFAULT NULL,
  `acct_remarks` text DEFAULT NULL,
  `acct_dv_number` varchar(100) DEFAULT NULL,
  `acct_dv_date` date DEFAULT NULL,
  `acct_dv_amount` decimal(15,2) DEFAULT NULL,
  `acct_date` date DEFAULT NULL,
  `budget_ors_number` varchar(100) DEFAULT NULL,
  `budget_ors_date` date DEFAULT NULL,
  `budget_status` varchar(100) DEFAULT NULL,
  `budget_demandability` varchar(100) DEFAULT NULL,
  `budget_remarks` text DEFAULT NULL,
  `cashier_status` varchar(100) DEFAULT NULL,
  `cashier_remarks` text DEFAULT NULL,
  `cashier_or_number` varchar(100) DEFAULT NULL,
  `cashier_or_date` date DEFAULT NULL,
  `cashier_landbank_ref` varchar(150) DEFAULT NULL,
  `cashier_payment_date` date DEFAULT NULL,
  `supply_partial_delivery_date` date DEFAULT NULL,
  `supply_delivery_date` date DEFAULT NULL,
  `proponent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `supplier_id`, `po_number`, `program_title`, `po_type`, `proponent`, `coverage_start`, `coverage_end`, `expected_date`, `amount`, `created_at`, `proc_status`, `proc_remarks`, `proc_date`, `supply_status`, `supply_delivery_receipt`, `supply_sales_invoice`, `supply_remarks`, `supply_date`, `acct_status`, `acct_remarks`, `acct_dv_number`, `acct_dv_date`, `acct_dv_amount`, `acct_date`, `budget_ors_number`, `budget_ors_date`, `budget_status`, `budget_demandability`, `budget_remarks`, `cashier_status`, `cashier_remarks`, `cashier_or_number`, `cashier_or_date`, `cashier_landbank_ref`, `cashier_payment_date`, `supply_partial_delivery_date`, `supply_delivery_date`, `proponent_id`) VALUES
(51, 8, '2025030039', 'procurement of supplies', 'Supplies', 'cid', '2026-03-20', '2026-03-31', NULL, 940288.99, '2026-03-18 02:14:59', 'FOR SUPPLY REVIEW', 'for IAR', '2026-03-18', 'COMPLETED', '12314', '98787', 'delivered', '2026-03-18', 'FOR VOUCHER', 'for payment', NULL, NULL, NULL, '2026-03-18', '876', '2026-03-27', 'FOR PAYMENT', 'Not Yet Due and Demandable', 'obligated', 'For ACIC', '', '345654', '2026-04-02', '940288.99', '2026-04-02', NULL, '2026-04-01', NULL),
(52, 8, '34', '22', 'a', '2', NULL, NULL, '2', 2.00, '2026-03-21 12:43:48', 'FOR SUPPLY REVIEW', '', '2026-03-21', 'PARTIAL DELIVERY', '', '', '', '2026-03-21', 'FOR VOUCHER', 'must be the post-acc', '8789', '2026-03-21', 10000.00, '2026-03-21', '876', '2026-03-21', 'ACCOUNTS PAYABLE', '', '', 'COMPLETED', '', '987', '2026-03-21', '10000.00', '2026-03-21', '2026-03-21', NULL, 3),
(62, 8, '123456', 'procurementOnly', 'jlkl', 'qy', NULL, NULL, NULL, 121.00, '2026-03-22 08:22:55', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(63, 8, '09090', 'supplyOnly', 'asd', 'qwe', NULL, NULL, NULL, 234.00, '2026-03-22 08:23:19', 'FOR SUPPLY REVIEW', '', '2026-03-22', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(65, 8, 'asdfsads', 'sample1', 'asd', 'qwert', NULL, NULL, NULL, 90000.00, '2026-03-22 10:12:23', 'FOR SUPPLY REVIEW', 'asdd', '2026-03-22', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(68, 8, '87', 'hgfd', 'j', NULL, NULL, NULL, NULL, 789.00, '2026-03-24 06:55:03', 'FOR SUPPLY REVIEW', '', '2026-03-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3);

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
(2, 51, 'supply', 'accounting', '2026-03-18 10:18:38', '2026-03-18 10:19:06', 0, 0, 13, 14),
(3, 51, 'accounting', 'budget', '2026-03-18 10:20:29', '2026-03-18 10:20:51', 0, 0, 14, 15),
(4, 51, 'budget', 'accounting', '2026-03-18 10:31:54', '2026-03-18 10:32:14', 0, 0, 15, 14),
(5, 51, 'accounting', 'cashier', '2026-03-18 10:32:55', '2026-03-18 10:39:01', 0, 0, 14, 16),
(6, 52, 'procurement', 'budget', '2026-03-21 21:13:01', '2026-03-21 21:16:51', NULL, 0, 12, NULL),
(7, 52, 'budget', 'supply', '2026-03-21 21:18:15', '2026-03-21 21:19:22', NULL, 0, 15, NULL),
(8, 52, 'supply', 'accounting', '2026-03-21 21:19:54', '2026-03-21 21:20:04', NULL, 0, 13, NULL),
(9, 52, 'accounting', 'budget', '2026-03-21 21:59:00', '2026-03-21 21:59:05', NULL, 0, 14, NULL),
(10, 52, 'budget', 'cashier', '2026-03-21 21:59:36', '2026-03-21 22:00:06', NULL, 0, 15, NULL),
(11, 52, 'cashier', 'procurement', '2026-03-22 08:34:01', '2026-03-22 08:34:25', NULL, 0, 16, NULL),
(12, 52, 'procurement', 'supply', '2026-03-22 08:37:44', '2026-03-22 08:54:49', NULL, 0, 12, NULL),
(13, 52, 'supply', 'procurement', '2026-03-22 08:56:18', '2026-03-22 08:56:39', NULL, 0, 13, NULL),
(14, 52, 'procurement', 'cashier', '2026-03-22 09:10:33', '2026-03-22 09:11:23', NULL, 0, 12, NULL),
(15, 55, 'procurement', 'supply', '2026-03-22 12:26:37', '2026-03-22 12:27:27', NULL, 0, 12, NULL),
(16, 57, 'procurement', 'supply', '2026-03-22 14:47:30', '2026-03-22 14:48:20', NULL, 0, 12, NULL),
(17, 60, 'procurement', 'supply', '2026-03-22 16:11:25', '2026-03-22 16:11:48', NULL, 0, 12, NULL),
(18, 63, 'procurement', 'supply', '2026-03-22 16:23:36', '2026-03-22 16:23:54', NULL, 0, 12, NULL),
(19, 64, 'procurement', 'supply', '2026-03-22 16:25:29', '2026-03-22 16:25:46', NULL, 0, 12, NULL),
(20, 64, 'supply', 'accounting', '2026-03-22 16:26:21', '2026-03-22 16:26:31', NULL, 0, 13, NULL),
(21, 64, 'accounting', 'budget', '2026-03-22 16:27:25', '2026-03-22 16:27:35', NULL, 0, 14, NULL);

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
(4, 51, 'accounting', 'FOR ORS', 'complete attachment', '2026-03-18 02:20:19'),
(5, 51, 'accounting', 'FOR ORS', 'incomplete', '2026-03-18 02:26:14'),
(6, 51, 'budget', 'FOR PAYMENT', 'obligated', '2026-03-18 02:31:34'),
(7, 51, 'accounting', 'FOR VOUCHER', 'for payment', '2026-03-18 02:32:49'),
(8, 51, 'cashier', 'For ACIC', 'Amount: 940288.99', '2026-03-18 02:41:16'),
(9, 52, 'procurement', 'FOR SUPPLY REVIEW', '', '2026-03-21 12:44:05'),
(10, 52, 'budget', 'FOR PAYMENT', '', '2026-03-21 13:17:20'),
(11, 52, 'supply', 'PARTIAL DELIVERY', '', '2026-03-21 13:19:48'),
(12, 52, 'accounting', 'FOR ORS', 'must be the pre-acc', '2026-03-21 13:20:41'),
(13, 52, 'accounting', 'FOR VOUCHER', 'must be the post-acc', '2026-03-21 13:57:56'),
(14, 52, 'budget', 'ACCOUNTS PAYABLE', '', '2026-03-21 13:59:12'),
(15, 52, 'budget', 'ACCOUNTS PAYABLE', '', '2026-03-21 13:59:28'),
(16, 52, 'cashier', 'For OR Issuance', 'Amount: 10000.00', '2026-03-21 14:31:20'),
(17, 52, 'cashier', 'For ACIC', 'Amount: 10000.00', '2026-03-21 14:31:40'),
(18, 52, 'cashier', 'For SDS/ASDS Approval', 'Amount: 10000.00', '2026-03-21 14:37:19'),
(19, 52, 'cashier', 'COMPLETED', 'Amount: 10000.00', '2026-03-21 14:38:05'),
(20, 52, 'cashier', 'For SDS/ASDS Approval', 'Amount: 10000.00', '2026-03-21 21:45:58'),
(24, 52, 'cashier', 'COMPLETED', 'Amount: 10000.00', '2026-03-22 05:16:00'),
(28, 63, 'procurement', 'FOR SUPPLY REVIEW', '', '2026-03-22 08:23:29'),
(32, 65, 'procurement', 'FOR SUPPLY REVIEW', '', '2026-03-22 10:13:10'),
(33, 65, 'procurement', 'FOR SUPPLY REVIEW', 'asdd', '2026-03-22 10:19:09'),
(35, 68, 'procurement', 'FOR SUPPLY REVIEW', '', '2026-03-24 06:55:13');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `active_session_id` varchar(128) DEFAULT NULL,
  `active_session_last_seen` datetime DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `full_name` varchar(150) DEFAULT NULL,
  `proponent_id` int(11) DEFAULT NULL,
  `school_head_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role_id`, `supplier_id`, `active_session_id`, `active_session_last_seen`, `last_login_at`, `last_login_ip`, `created_at`, `full_name`, `proponent_id`, `school_head_id`) VALUES
(10, 'admin', NULL, '$2a$12$XhSdO3SPRj0NL2b2Ln1v8uzgYAQEopTZbNSEl/z7BQvud/YtrqWeS', 1, NULL, NULL, NULL, '2026-03-25 02:17:51', '::1', '2026-02-10 06:04:17', NULL, NULL, NULL),
(12, 'procurement', NULL, '$2y$10$QclzMbbCTh0V3CoawlNNKOKV4SwirQglkHhk6t2DaKyJRsEGKw9Vi', 3, NULL, 'voi1h8b3fqg8kga50qna8i34ko', '2026-03-05 09:51:03', '2026-03-25 03:15:19', '::1', '2026-02-10 06:04:17', NULL, NULL, NULL),
(13, 'supply', 'jkrioveros@gmail.com', '$2a$12$XhSdO3SPRj0NL2b2Ln1v8uzgYAQEopTZbNSEl/z7BQvud/YtrqWeS', 4, NULL, 'ekrk5vsvck4g5u4qq32hffcp2e', '2026-03-05 09:51:03', '2026-03-25 01:53:12', '::1', '2026-02-10 06:04:17', NULL, NULL, NULL),
(14, 'accounting', NULL, '$2a$12$XhSdO3SPRj0NL2b2Ln1v8uzgYAQEopTZbNSEl/z7BQvud/YtrqWeS', 5, NULL, 'mm7dcl78j23eb67dav77vm290p', '2026-03-05 09:51:03', '2026-03-24 08:59:18', '::1', '2026-02-10 06:04:17', NULL, NULL, NULL),
(15, 'budget', NULL, '$2a$12$XhSdO3SPRj0NL2b2Ln1v8uzgYAQEopTZbNSEl/z7BQvud/YtrqWeS', 6, NULL, 'is20s2gsenns7n8dgjp437e4mg', '2026-03-05 09:05:49', '2026-03-24 08:59:29', '::1', '2026-02-10 06:04:17', NULL, NULL, NULL),
(16, 'erica', 'rioverosjustine92@gmail.com', '$2y$10$bikOPzugs3ObkP49dIpcHurKtYEbNoSvcJBIyjWMq5aNSGXeD9RBO', 7, NULL, 'oiv0qv5qvmjf84o4co8334ghap', '2026-03-05 09:51:03', '2026-03-25 03:20:02', '::1', '2026-02-10 06:04:17', NULL, NULL, NULL),
(23, 'sample cashier', NULL, '$2y$10$FWnyLpEBowxNhXNpBcYpm..0XLNQCJK0NjkUtAEdVsb0kVpK2e.0G', 7, NULL, NULL, NULL, NULL, NULL, '2026-03-03 00:47:47', NULL, NULL, NULL),
(26, 'alphanum0002', 'alphanum0002@gmail.com', '$2y$10$J0BaAVz6x9kQLugvk5ZOr.EVZ1UjYgcchaPDeRkuXKVKOls033.wS', 2, 8, NULL, NULL, '2026-03-25 03:16:52', '::1', '2026-03-18 02:09:09', NULL, NULL, NULL),
(27, 'sup1', 'forsample.anatomist456@aleeas.com', '$2y$10$w.PiQjwl29/DMQ2BTcifC.UeJnB4j6Urs.D.jKt2CFL7.E6j7wd8u', 2, 9, NULL, NULL, '2026-03-25 02:18:36', '::1', '2026-03-23 20:27:02', NULL, NULL, NULL),
(28, 'prop1', NULL, '$2y$10$XEeOUl/3QDFQz4cG6cRwoO95SjOuG6DBT3K/twL3Gmdv2zmNO6vd6', 8, NULL, NULL, NULL, '2026-03-25 01:46:22', '::1', '2026-03-23 20:32:49', NULL, 1, NULL),
(29, 'sch1', NULL, '$2y$10$Af7ioW/BxM3RR3ytwEedN.2jhJE0b91p..QznKWR69lmZCIKSzZAy', 9, NULL, NULL, NULL, NULL, NULL, '2026-03-23 22:50:59', NULL, NULL, 1),
(32, 'prop2', 'proponent@gmail.com', '$2y$10$FohZ7I97EMD48FUsszWmuOGrc9LE4JwPmi47PAla09Sak3jsTQGTa', 8, NULL, NULL, NULL, '2026-03-25 02:21:22', '::1', '2026-03-24 05:40:51', NULL, 3, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_preferences`
--

CREATE TABLE `user_preferences` (
  `user_id` int(11) NOT NULL,
  `smart_polling_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_preferences`
--

INSERT INTO `user_preferences` (`user_id`, `smart_polling_enabled`, `updated_at`) VALUES
(12, 0, '2026-03-21 12:52:07'),
(13, 0, '2026-03-21 12:52:01'),
(14, 0, '2026-03-21 12:51:53'),
(15, 0, '2026-03-21 12:51:46');

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
(871, 10, 't669qqh4na914lp2avhcqkg81j', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-18 01:26:53', '2026-03-18 01:46:35', '2026-03-22 09:44:39'),
(2391, 13, 'g2km1m3e5meroemk0u6eca2n94', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-18 01:51:15', '2026-03-18 02:18:41', NULL),
(5495, 26, '1jtobovb8e8155qsvqn175hp8c', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-18 02:09:09', '2026-03-18 02:45:13', NULL),
(12170, 10, 'bil15tl1ft8oa03c40lhlosvc6', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-18 02:46:10', '2026-03-18 08:30:34', '2026-03-18 08:30:36'),
(14570, 26, 'p3chp65t33b1mtae04457cosg4', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-18 03:13:35', '2026-03-18 03:20:06', '2026-03-18 03:20:06'),
(16520, 10, 'coo2uijniab67s14d2mqjp1sci', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-18 08:30:53', '2026-03-18 11:31:29', '2026-03-22 09:43:45'),
(18908, 13, 'ct9vgj6facmaa2iaf34csfc2oh', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-18 10:28:23', '2026-03-18 10:33:53', NULL),
(18934, 14, 'vk44lt3aa8icln3945u9njfmtu', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-18 10:28:30', '2026-03-18 10:33:55', NULL),
(27007, 12, 'pbb048semktj1igqdrpfcuf92l', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 04:05:16', '2026-03-20 04:24:38', NULL),
(27015, 13, '269feejn9vsu2569i4gc1dh574', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-20 04:05:24', '2026-03-20 04:11:39', NULL),
(27026, 15, 'fn6naqbk2u6o798neoubgijst5', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 04:05:44', '2026-03-20 06:34:21', NULL),
(27030, 14, '4flqrrfs9h8t702ucckrkh8bgk', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-20 04:05:59', '2026-03-21 15:57:34', NULL),
(27325, 16, 'j1uica1h9jfh1r1p7fqt8upf48', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 04:11:29', '2026-03-20 06:26:43', NULL),
(28379, 13, 'n5r92h320t52sak0pibiit9dpi', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-21 12:36:19', '2026-03-21 15:57:34', NULL),
(28453, 12, 'abkfmgnmocf57ov6efqkobcv35', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 12:41:07', '2026-03-23 10:23:19', NULL),
(29625, 15, 'k9m2kpnkhlip04qvnd95dpp4jc', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 12:51:13', '2026-03-21 22:45:19', NULL),
(47096, 16, 'qr2n55c3p7fnour1d8n67ge0tk', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 13:59:55', '2026-03-21 14:39:13', NULL),
(80620, 16, '8vhh1ra0eq0id8vgb4u8ctnqr6', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:45:47', '2026-03-22 00:34:08', NULL),
(88085, 15, '0usr573sbakronqi8v5u80n57u', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-22 00:31:14', '2026-03-22 06:57:19', NULL),
(88398, 13, 'n3una7b1g8ndgufkdlg9l0qcbb', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-22 00:33:15', '2026-03-23 10:23:19', NULL),
(88454, 14, 'md1kgl0h52m554nqs6cfi8vkev', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-22 00:33:30', '2026-03-23 00:47:20', NULL),
(95159, 16, 'odfbc9funbrullnjpbmbjs8old', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-22 01:10:53', '2026-03-22 01:13:18', NULL),
(99945, 16, '98qmqaijsv729tr6t49rq4cdce', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-22 04:16:01', '2026-03-22 04:18:58', NULL),
(108427, 16, 'neftcjnldpskjdo4bcrplmgtkb', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-22 04:50:09', '2026-03-22 05:16:08', NULL),
(125819, 15, '1fsv920155sodfe4uj4n0dckp7', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-22 08:22:15', '2026-03-22 09:57:20', NULL),
(132488, 10, 'q0fcbl32pkrov3c6qgf53ses4o', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-22 09:42:39', '2026-03-22 10:32:26', NULL),
(132545, 10, 'sbvjt5rl59a5tiue251s9p5953', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-22 09:43:05', '2026-03-22 09:43:39', '2026-03-22 09:43:40'),
(132673, 10, '5c17fc04mokovanslnipm7ot95', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-22 09:44:13', '2026-03-22 09:44:34', '2026-03-22 09:44:37'),
(133845, 16, '16av97toi8r4qvk8rg4qpdhi57', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-22 10:01:05', '2026-03-22 10:05:05', '2026-03-22 10:05:08'),
(134434, 16, '2to0hgjojh8dpsaof9bvkb1apd', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-22 10:05:13', '2026-03-22 10:30:09', '2026-03-22 10:30:11'),
(137293, 16, 'e16vdhcpringc8up9fthvmm0ei', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-22 10:30:22', '2026-03-22 10:32:28', NULL),
(148986, 15, 'od1gl1h15057rog7fcrcecoqd3', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-23 01:19:36', '2026-03-23 10:23:19', NULL),
(164279, 16, 'sgr1f50rrvld9bp7mbcm0s3257', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-23 08:19:55', '2026-03-23 09:52:06', NULL),
(172305, 27, 'c77qrsq99sgrrnfq5mo6u9nfea', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-23 19:32:35', '2026-03-23 19:32:39', '2026-03-23 19:32:39'),
(172310, 26, '6mdd98haphbari9c9fuutdfsq3', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-23 19:33:16', '2026-03-23 20:14:32', '2026-03-23 20:14:33'),
(172329, 28, 'k57dh2hs87anvp7ji0q655iseh', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-23 22:51:17', '2026-03-23 22:51:40', NULL),
(172333, 12, 'cvh8s1mop5cstj8kb1oat4sfp6', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 00:11:55', '2026-03-24 00:16:41', '2026-03-24 00:16:41'),
(172624, 16, 'arbre8h64oajpl4udnnv59iovm', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 00:16:53', '2026-03-24 00:59:24', '2026-03-24 00:59:24'),
(173935, 12, 'kvklshluhantuvmgmd7mqef2kl', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 00:59:05', '2026-03-24 02:16:15', NULL),
(173946, 13, 'b0js4fn53mqide86m7k7b3ufqq', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-24 00:59:10', '2026-03-24 03:23:59', NULL),
(173957, 14, '7fd3sfe9mv25asl1il5aiu90a4', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-24 00:59:18', '2026-03-24 04:57:00', NULL),
(173993, 5, '3p05nq4adu7of6s37t57040fn8', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 00:59:29', '2026-03-24 09:52:47', NULL),
(174013, 16, 'fqnpj3uqj9emof3em1j43i41sl', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 00:59:36', '2026-03-24 02:10:01', NULL),
(180904, 12, 'kioi4c64ac2l0ufofnrr6sjsas', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 02:16:30', '2026-03-24 19:27:16', NULL),
(184611, 28, 'ndah05k64jc26nne352q4goceq', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 02:53:40', '2026-03-24 03:16:30', NULL),
(184861, 26, 'uvo9t1fcvom7st179v7q66oan2', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 02:55:45', '2026-03-24 03:15:12', NULL),
(187340, 26, '08atkuuv3t868me9baohju6rcg', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 03:15:39', '2026-03-24 03:18:13', NULL),
(188640, 26, '53jgiavfgcg79a672198i7d791', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 05:01:10', '2026-03-24 05:20:27', '2026-03-24 05:20:29'),
(189314, 28, '9j991v8esb5ba6223a3qnbjrd8', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 05:18:32', '2026-03-24 05:18:32', '2026-03-24 05:20:19'),
(189390, 26, 'b0mnrbjcfvagg37j8jrphm4p72', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 05:20:27', '2026-03-24 06:48:56', NULL),
(189398, 28, '374i2lodufsavfcgj7ktlkmjnt', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 05:20:37', '2026-03-24 05:20:37', '2026-03-24 05:20:43'),
(189426, 30, 'b9n2qc2i1vdkbfqjaugoof9350', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 05:22:11', '2026-03-24 05:22:59', '2026-03-24 05:23:00'),
(189660, 32, 'oge537st6g4gi525qu3p8mkfbt', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 05:41:10', '2026-03-24 06:03:04', '2026-03-24 06:03:10'),
(189980, 28, 'nomctkq0ttomn4msp1rkmnlt0d', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 06:03:32', '2026-03-24 06:06:52', '2026-03-24 06:07:14'),
(190149, 32, 'vi2if813begenll1au32djrubq', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 06:07:20', '2026-03-24 06:48:06', NULL),
(190532, 16, 't8ml8qiell7rat28c30fkgvlgr', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 06:21:09', '2026-03-24 06:23:27', NULL),
(192753, 16, '1t3mrm5os6qp5l650lkmqrrc70', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 07:16:06', '2026-03-24 08:00:59', NULL),
(199622, 16, 'rhgd0vog4sgatgr642m94ovg0k', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:34:09', '2026-03-24 18:03:14', NULL),
(200166, 26, 'g2hvei05i8f5q6a7o8tjgcjgdq', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:45:01', '2026-03-24 18:16:54', '2026-03-24 18:16:55'),
(200203, 32, '4eb9aoune3cibldmeirigbkqnp', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:46:01', '2026-03-24 17:46:12', '2026-03-24 17:46:16'),
(200207, 28, 'jjmr8trigjoorto14kk5v6r7cu', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:46:22', '2026-03-24 17:46:25', '2026-03-24 17:46:50'),
(200232, 32, 'h0uqpcikl4vh67rd8cofuf5euv', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:46:58', '2026-03-24 17:48:46', NULL),
(200503, 13, 'vlisrthvls6ir4ja2dogrm0e3t', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-24 17:53:12', '2026-03-24 19:27:16', NULL),
(202576, 10, 'a88j85mlljk46ip0j8du8s5ls4', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 18:17:51', '2026-03-24 18:18:32', '2026-03-24 18:18:32'),
(202677, 27, 'sfamvhn3pfu1i5e78pq2p5q07g', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 18:18:36', '2026-03-24 18:40:04', '2026-03-24 18:40:05'),
(202898, 32, 'gdadftdnso6vet40ibr4r0uelu', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 18:21:22', '2026-03-24 18:22:25', NULL),
(204113, 12, '1i56ci4lo01dor42h5lrmt8p1l', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 18:46:07', '2026-03-24 18:46:07', '2026-03-24 18:46:34'),
(204115, 12, 'pkcgvgpr0o9gn385n6so5fkq0g', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 18:46:42', '2026-03-24 18:46:42', '2026-03-24 18:54:10'),
(204501, 12, 'tubbab5brr8f3mcm00dq7stahp', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 18:54:17', '2026-03-24 18:54:17', '2026-03-24 18:54:23'),
(204528, 12, 'th9dp9e0e430oc3itri7742k8d', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 18:54:33', '2026-03-24 18:54:33', '2026-03-24 18:54:37'),
(205045, 12, 'g69vnkch1lj1be5f4r4ffc1qm0', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 19:15:19', '2026-03-24 19:15:29', '2026-03-24 19:15:30'),
(205061, 26, 'h2mmlbksfn7mm9f3utr4cd8egb', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 19:16:52', '2026-03-24 19:17:14', NULL),
(205266, 16, '91cv1ot3oi6hm4abr4gtubiejo', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 19:20:02', '2026-03-24 19:21:32', NULL);

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
-- Indexes for table `proponents`
--
ALTER TABLE `proponents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `school_heads`
--
ALTER TABLE `school_heads`
  ADD PRIMARY KEY (`id`);

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
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `proponent_id` (`proponent_id`);

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
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `users_proponent_fk` (`proponent_id`),
  ADD KEY `users_school_head_fk` (`school_head_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=224;

--
-- AUTO_INCREMENT for table `department_notifications`
--
ALTER TABLE `department_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `proponents`
--
ALTER TABLE `proponents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `school_heads`
--
ALTER TABLE `school_heads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `transaction_handoffs`
--
ALTER TABLE `transaction_handoffs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `transaction_updates`
--
ALTER TABLE `transaction_updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=205576;

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
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`proponent_id`) REFERENCES `proponents` (`id`);

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
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  ADD CONSTRAINT `users_proponent_fk` FOREIGN KEY (`proponent_id`) REFERENCES `proponents` (`id`),
  ADD CONSTRAINT `users_school_head_fk` FOREIGN KEY (`school_head_id`) REFERENCES `school_heads` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
