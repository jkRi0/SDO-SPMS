-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 18, 2026 at 01:55 AM
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
(80, 12, 'logout', 'user', 12, 'User logged out', '2026-03-04 11:34:06'),
(81, 21, 'logout', 'user', 21, 'User logged out', '2026-03-04 12:09:29'),
(82, 18, 'login', 'user', 18, 'Successful login', '2026-03-04 12:10:00'),
(83, 18, 'logout', 'user', 18, 'User logged out', '2026-03-04 12:10:28'),
(84, 18, 'login', 'user', 18, 'Successful login', '2026-03-04 12:10:40'),
(85, 12, 'login', 'user', 12, 'Successful login', '2026-03-04 12:12:20'),
(86, 13, 'login', 'user', 13, 'Successful login', '2026-03-04 12:13:06'),
(87, 14, 'login', 'user', 14, 'Successful login', '2026-03-04 12:19:50'),
(88, 14, 'update_account', 'user', 14, '{\"old_username\":\"accounting@deped.gov\",\"new_username\":\"accounting\",\"password_changed\":false}', '2026-03-04 12:19:59'),
(89, 12, 'update_account', 'user', 12, '{\"old_username\":\"procurement\",\"new_username\":\"procurement\",\"password_changed\":true}', '2026-03-04 12:21:08'),
(90, 12, 'update_account', 'user', 12, '{\"old_username\":\"procurement\",\"new_username\":\"procurement\",\"password_changed\":true}', '2026-03-04 12:21:19'),
(91, 15, 'login', 'user', 15, 'Successful login', '2026-03-04 12:55:23'),
(92, 15, 'update_account', 'user', 15, '{\"old_username\":\"budget@deped.gov\",\"new_username\":\"budget\",\"password_changed\":false}', '2026-03-04 12:56:15'),
(93, 16, 'login', 'user', 16, 'Successful login', '2026-03-04 12:56:43'),
(94, 18, 'update_account', 'user', 18, '{\"old_username\":\"supplier1\",\"new_username\":\"supplier\",\"password_changed\":false}', '2026-03-04 13:01:33'),
(95, 18, 'logout', 'user', 18, 'User logged out', '2026-03-04 13:04:11'),
(96, 18, 'login_failed', 'user', 18, 'Invalid password', '2026-03-04 13:04:54'),
(97, 18, 'login_failed', 'user', 18, 'Invalid password', '2026-03-04 13:08:51'),
(98, 18, 'login_failed', 'user', 18, 'Invalid password', '2026-03-04 13:08:51'),
(99, 18, 'login_failed', 'user', 18, 'Invalid password', '2026-03-04 13:10:20'),
(100, NULL, 'login_failed', 'user', NULL, '{\"attempted_username\":\"supplier1\",\"reason\":\"unknown_username\"}', '2026-03-04 13:15:40'),
(101, 18, 'login_failed', 'user', 18, '{\"attempted_username\":\"supplier\",\"reason\":\"invalid_password\"}', '2026-03-04 13:16:01'),
(102, 18, 'login', 'user', 18, 'Successful login', '2026-03-04 13:16:15'),
(103, NULL, 'login_failed', 'user', NULL, '{\"attempted_username\":\"supplier1\",\"reason\":\"unknown_username\"}', '2026-03-04 18:02:54'),
(104, 18, 'login_failed', 'user', 18, '{\"attempted_username\":\"supplier\",\"reason\":\"invalid_password\"}', '2026-03-04 18:03:12'),
(105, 18, 'login_failed', 'user', 18, '{\"attempted_username\":\"supplier\",\"reason\":\"invalid_password\"}', '2026-03-04 18:03:24'),
(106, 10, 'update_user', 'user', 18, '{\"username\":\"supplier\",\"role_id\":2,\"supplier_id\":3}', '2026-03-04 18:03:52'),
(107, 18, 'login', 'user', 18, 'Successful login', '2026-03-04 18:08:19'),
(108, 18, 'logout', 'user', 18, 'User logged out', '2026-03-04 18:53:22'),
(109, 18, 'login_failed', 'user', 18, '{\"attempted_username\":\"supplier\",\"reason\":\"invalid_password\"}', '2026-03-04 18:53:34'),
(110, 18, 'login', 'user', 18, 'Successful login', '2026-03-04 18:53:41'),
(111, 18, 'logout', 'user', 18, 'User logged out', '2026-03-04 18:54:07'),
(112, 18, 'login', 'user', 18, 'Successful login', '2026-03-04 18:54:21'),
(113, 18, 'logout', 'user', 18, 'User logged out', '2026-03-04 19:20:42'),
(114, 21, 'login', 'user', 21, 'Successful login (Google OAuth)', '2026-03-04 19:20:52'),
(115, 10, 'delete_user', 'user', 22, NULL, '2026-03-04 19:21:22'),
(116, 21, 'logout', 'user', 21, 'User logged out', '2026-03-05 01:53:07'),
(117, NULL, 'login_failed', 'user', NULL, '{\"attempted_username\":\"supplier1\",\"reason\":\"unknown_username\"}', '2026-03-05 01:53:16'),
(118, 18, 'login', 'user', 18, 'Successful login', '2026-03-05 01:53:22'),
(119, 12, 'logout', 'user', 12, 'User logged out', '2026-03-05 01:53:45'),
(120, 12, 'login', 'user', 12, 'Successful login', '2026-03-05 01:53:56'),
(121, 13, 'logout', 'user', 13, 'User logged out', '2026-03-05 01:54:16'),
(122, 13, 'login', 'user', 13, 'Successful login', '2026-03-05 01:56:10'),
(123, 14, 'logout', 'user', 14, 'User logged out', '2026-03-05 01:56:21'),
(124, 14, 'login', 'user', 14, 'Successful login', '2026-03-05 01:56:31'),
(125, 15, 'login', 'user', 15, 'Successful login', '2026-03-05 01:56:40'),
(126, 16, 'logout', 'user', 16, 'User logged out', '2026-03-05 01:56:48'),
(127, 16, 'login', 'user', 16, 'Successful login', '2026-03-05 01:56:55'),
(128, 10, 'logout', 'user', 10, 'User logged out', '2026-03-05 02:03:53'),
(130, 15, 'login', 'user', 15, 'Successful login', '2026-03-05 02:13:10'),
(131, 10, 'login', 'user', 10, 'Successful login', '2026-03-05 02:49:55'),
(132, 18, 'logout', 'user', 18, 'User logged out', '2026-03-05 02:50:16'),
(133, NULL, 'login_failed', 'user', NULL, '{\"attempted_username\":\"supplier1\",\"reason\":\"unknown_username\"}', '2026-03-05 02:50:24'),
(134, 18, 'login', 'user', 18, 'Successful login', '2026-03-05 02:50:30'),
(135, 18, 'logout', 'user', 18, 'User logged out', '2026-03-05 03:01:22'),
(136, 18, 'login', 'user', 18, 'Successful login', '2026-03-05 03:01:31'),
(137, 18, 'login', 'user', 18, 'Successful login', '2026-03-05 06:29:12'),
(138, 16, 'login', 'user', 16, 'Successful login', '2026-03-05 07:07:37'),
(139, 10, 'login_failed', 'user', 10, '{\"attempted_username\":\"admin\",\"reason\":\"invalid_password\"}', '2026-03-05 08:16:40'),
(140, 10, 'login', 'user', 10, 'Successful login', '2026-03-05 08:16:57'),
(141, 12, 'login', 'user', 12, 'Successful login', '2026-03-10 14:23:00'),
(142, 25, 'login', 'user', 25, 'Successful login (Google OAuth)', '2026-03-10 14:23:45'),
(143, 13, 'login', 'user', 13, 'Successful login', '2026-03-10 14:26:05'),
(144, 14, 'login', 'user', 14, 'Successful login', '2026-03-10 14:26:32'),
(145, 15, 'login', 'user', 15, 'Successful login', '2026-03-10 14:26:44'),
(146, 16, 'login', 'user', 16, 'Successful login', '2026-03-10 14:27:11'),
(147, 14, 'logout', 'user', 14, 'User logged out', '2026-03-10 23:29:51'),
(148, 15, 'logout', 'user', 15, 'User logged out', '2026-03-10 23:30:01'),
(149, 16, 'logout', 'user', 16, 'User logged out', '2026-03-10 23:30:08'),
(150, 25, 'logout', 'user', 25, 'User logged out', '2026-03-10 23:30:51'),
(151, 12, 'login', 'user', 12, 'Successful login', '2026-03-11 00:07:35'),
(152, 13, 'login', 'user', 13, 'Successful login', '2026-03-11 00:08:22'),
(153, 14, 'login', 'user', 14, 'Successful login', '2026-03-11 00:08:36'),
(154, 15, 'login', 'user', 15, 'Successful login', '2026-03-11 00:08:53'),
(155, 16, 'login', 'user', 16, 'Successful login', '2026-03-11 00:08:59'),
(156, 25, 'login', 'user', 25, 'Successful login (Google OAuth)', '2026-03-11 00:09:34'),
(157, 10, 'login', 'user', 10, 'Successful login', '2026-03-11 00:53:25'),
(158, 16, 'login', 'user', 16, 'Successful login', '2026-03-11 01:14:53'),
(159, 16, 'logout', 'user', 16, 'User logged out', '2026-03-11 01:15:52'),
(160, 15, 'login', 'user', 15, 'Successful login', '2026-03-11 01:15:58'),
(161, 12, 'login', 'user', 12, 'Successful login', '2026-03-12 01:28:36'),
(162, 13, 'login', 'user', 13, 'Successful login', '2026-03-12 01:29:52'),
(163, 14, 'login', 'user', 14, 'Successful login', '2026-03-12 01:30:06'),
(164, 15, 'login', 'user', 15, 'Successful login', '2026-03-12 01:30:14'),
(165, 16, 'login', 'user', 16, 'Successful login', '2026-03-12 01:30:21'),
(166, 10, 'login', 'user', 10, 'Successful login', '2026-03-12 02:18:32'),
(167, 25, 'login', 'user', 25, 'Successful login (Google OAuth)', '2026-03-12 02:38:37'),
(168, 12, 'transaction_update', 'transaction', 41, '{\"transaction_id\":41,\"po_number\":\"1235776543\",\"stage\":\"procurement\",\"status\":\"COMPLETED\",\"remarks\":\"\"}', '2026-03-12 07:01:06'),
(169, 12, 'transaction_update', 'transaction', 41, '{\"transaction_id\":41,\"po_number\":\"1235776543\",\"stage\":\"procurement\",\"status\":\"FOR SUPPLY REVIEW\",\"remarks\":\"\"}', '2026-03-12 07:01:13'),
(170, 12, 'transaction_update', 'transaction', 41, '{\"transaction_id\":41,\"po_number\":\"1235776543\",\"stage\":\"procurement\",\"status\":\"COMPLETED\",\"remarks\":\"\"}', '2026-03-12 07:02:29'),
(171, 12, 'transaction_handoff_forward', 'transaction', 41, '{\"transaction_id\":41,\"po_number\":\"1235776543\",\"from_dept\":\"procurement\",\"to_dept\":\"supply\"}', '2026-03-12 07:02:58'),
(172, 13, 'transaction_handoff_receive', 'transaction', 41, '{\"transaction_id\":41,\"po_number\":\"1235776543\",\"from_dept\":\"procurement\",\"to_dept\":\"supply\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-12 07:03:06'),
(173, 13, 'transaction_update', 'transaction', 41, '{\"transaction_id\":41,\"po_number\":\"1235776543\",\"stage\":\"supply\",\"status\":\"COMPLETED\",\"remarks\":\"\",\"delivery_receipt\":\"\",\"sales_invoice\":\"\"}', '2026-03-12 07:07:00'),
(174, 13, 'transaction_handoff_forward', 'transaction', 41, '{\"transaction_id\":41,\"po_number\":\"1235776543\",\"from_dept\":\"supply\",\"to_dept\":\"accounting_pre\"}', '2026-03-12 07:12:57'),
(175, 14, 'transaction_handoff_receive', 'transaction', 41, '{\"transaction_id\":41,\"po_number\":\"1235776543\",\"from_dept\":\"supply\",\"to_dept\":\"accounting_pre\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-12 07:13:04'),
(176, 14, 'transaction_update', 'transaction', 41, '{\"transaction_id\":41,\"po_number\":\"1235776543\",\"stage\":\"accounting_pre\",\"status\":\"COMPLETED\",\"remarks\":\"\",\"dv_amount\":\"\"}', '2026-03-12 07:13:32'),
(177, 14, 'transaction_handoff_forward', 'transaction', 41, '{\"transaction_id\":41,\"po_number\":\"1235776543\",\"from_dept\":\"accounting_pre\",\"to_dept\":\"budget\"}', '2026-03-12 07:13:50'),
(178, 15, 'transaction_handoff_receive', 'transaction', 41, '{\"transaction_id\":41,\"po_number\":\"1235776543\",\"from_dept\":\"accounting_pre\",\"to_dept\":\"budget\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-12 07:13:54'),
(179, 15, 'login', 'user', 15, 'Successful login', '2026-03-12 11:55:46'),
(180, 16, 'login', 'user', 16, 'Successful login', '2026-03-12 12:15:55'),
(181, 15, 'login', 'user', 15, 'Successful login', '2026-03-13 00:28:04'),
(182, 15, 'transaction_update', 'transaction', 41, '{\"transaction_id\":41,\"po_number\":\"1235776543\",\"stage\":\"budget\",\"status\":\"FOR PAYMENT\",\"remarks\":\"\",\"dv_number\":\"\",\"dv_date\":\"\",\"demandability\":\"\"}', '2026-03-13 02:11:23'),
(183, 12, 'transaction_update', 'transaction', 42, '{\"transaction_id\":42,\"po_number\":\"1234567654\",\"stage\":\"procurement\",\"status\":\"FOR SUPPLY REVIEW\",\"remarks\":\"\"}', '2026-03-13 04:01:35'),
(184, 12, 'transaction_update', 'transaction', 42, '{\"transaction_id\":42,\"po_number\":\"1234567654\",\"stage\":\"procurement\",\"status\":\"COMPLETED\",\"remarks\":\"\"}', '2026-03-13 04:01:54'),
(185, 12, 'transaction_handoff_forward', 'transaction', 42, '{\"transaction_id\":42,\"po_number\":\"1234567654\",\"from_dept\":\"procurement\",\"to_dept\":\"supply\"}', '2026-03-13 04:01:58'),
(186, 13, 'transaction_handoff_receive', 'transaction', 42, '{\"transaction_id\":42,\"po_number\":\"1234567654\",\"from_dept\":\"procurement\",\"to_dept\":\"supply\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-13 04:02:09'),
(187, 13, 'transaction_update', 'transaction', 42, '{\"transaction_id\":42,\"po_number\":\"1234567654\",\"stage\":\"supply\",\"status\":\"PARTIAL DELIVER\",\"remarks\":\"\",\"delivery_receipt\":\"\",\"sales_invoice\":\"\"}', '2026-03-13 04:02:15'),
(188, 13, 'transaction_update', 'transaction', 42, '{\"transaction_id\":42,\"po_number\":\"1234567654\",\"stage\":\"supply\",\"status\":\"COMPLETED\",\"remarks\":\"\",\"delivery_receipt\":\"\",\"sales_invoice\":\"\"}', '2026-03-13 05:06:31'),
(189, 12, 'transaction_update', 'transaction', 43, '{\"transaction_id\":43,\"po_number\":\"65678\",\"stage\":\"procurement\",\"status\":\"FOR SUPPLY REVIEW\",\"remarks\":\"\"}', '2026-03-13 05:21:09'),
(190, 12, 'transaction_update', 'transaction', 43, '{\"transaction_id\":43,\"po_number\":\"65678\",\"stage\":\"procurement\",\"status\":\"COMPLETED\",\"remarks\":\"\"}', '2026-03-13 05:21:52'),
(191, 12, 'transaction_update', 'transaction', 43, '{\"transaction_id\":43,\"po_number\":\"65678\",\"stage\":\"procurement\",\"status\":\"COMPLETED\",\"remarks\":\"\"}', '2026-03-13 05:24:57'),
(192, 12, 'transaction_update', 'transaction', 43, '{\"transaction_id\":43,\"po_number\":\"65678\",\"stage\":\"procurement\",\"status\":\"COMPLETED\",\"remarks\":\"\"}', '2026-03-13 05:25:23'),
(193, 12, 'transaction_update', 'transaction', 43, '{\"transaction_id\":43,\"po_number\":\"65678\",\"stage\":\"procurement\",\"status\":\"COMPLETED\",\"remarks\":\"\"}', '2026-03-13 05:26:18'),
(194, 12, 'transaction_update', 'transaction', 43, '{\"transaction_id\":43,\"po_number\":\"65678\",\"stage\":\"procurement\",\"status\":\"FOR SUPPLY REVIEW\",\"remarks\":\"\"}', '2026-03-13 05:27:02'),
(195, 12, 'transaction_update', 'transaction', 43, '{\"transaction_id\":43,\"po_number\":\"65678\",\"stage\":\"procurement\",\"status\":\"COMPLETED\",\"remarks\":\"\"}', '2026-03-13 05:28:10'),
(196, 12, 'transaction_handoff_forward', 'transaction', 43, '{\"transaction_id\":43,\"po_number\":\"65678\",\"from_dept\":\"procurement\",\"to_dept\":\"supply\"}', '2026-03-13 05:28:28'),
(197, 13, 'transaction_handoff_receive', 'transaction', 43, '{\"transaction_id\":43,\"po_number\":\"65678\",\"from_dept\":\"procurement\",\"to_dept\":\"supply\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-13 05:30:18'),
(198, 13, 'transaction_update', 'transaction', 43, '{\"transaction_id\":43,\"po_number\":\"65678\",\"stage\":\"supply\",\"status\":\"PARTIAL DELIVER\",\"remarks\":\"\",\"delivery_receipt\":\"\",\"sales_invoice\":\"\"}', '2026-03-13 05:31:47'),
(199, 13, 'transaction_update', 'transaction', 43, '{\"transaction_id\":43,\"po_number\":\"65678\",\"stage\":\"supply\",\"status\":\"PARTIAL DELIVER\",\"remarks\":\"\",\"delivery_receipt\":\"\",\"sales_invoice\":\"\"}', '2026-03-13 05:33:12'),
(200, 16, 'login', 'user', 16, 'Successful login', '2026-03-13 05:54:15'),
(201, 25, 'login', 'user', 25, 'Successful login (Google OAuth)', '2026-03-13 05:54:31'),
(202, 10, 'login', 'user', 10, 'Successful login', '2026-03-13 05:55:08'),
(203, 12, 'login', 'user', 12, 'Successful login', '2026-03-13 05:58:57'),
(204, 12, 'login', 'user', 12, 'Successful login', '2026-03-13 06:03:56'),
(205, 12, 'login', 'user', 12, 'Successful login', '2026-03-13 07:53:43'),
(206, 12, 'login', 'user', 12, 'Successful login', '2026-03-13 08:04:08'),
(207, 12, 'login', 'user', 12, 'Successful login', '2026-03-13 08:13:30'),
(208, 25, 'logout', 'user', 25, 'User logged out', '2026-03-13 08:32:10'),
(209, 10, 'login', 'user', 10, 'Successful login', '2026-03-13 12:45:48'),
(210, 12, 'login', 'user', 12, 'Successful login', '2026-03-14 00:50:16'),
(211, 10, 'login', 'user', 10, 'Successful login', '2026-03-14 01:05:06'),
(212, 12, 'logout', 'user', 12, 'User logged out', '2026-03-14 01:20:28'),
(213, 12, 'login', 'user', 12, 'Successful login', '2026-03-14 01:24:08'),
(214, 12, 'login', 'user', 12, 'Successful login', '2026-03-14 04:49:39'),
(215, 12, 'logout', 'user', 12, 'User logged out', '2026-03-14 05:03:50'),
(216, 13, 'login', 'user', 13, 'Successful login', '2026-03-14 05:04:01'),
(217, 13, 'transaction_update', 'transaction', 43, '{\"transaction_id\":43,\"po_number\":\"65678\",\"stage\":\"supply\",\"status\":\"PARTIAL DELIVER\",\"remarks\":\"\",\"delivery_receipt\":\"1234\",\"sales_invoice\":\"1234543\"}', '2026-03-14 05:04:53'),
(218, 13, 'transaction_update', 'transaction', 43, '{\"transaction_id\":43,\"po_number\":\"65678\",\"stage\":\"supply\",\"status\":\"PARTIAL DELIVER\",\"remarks\":\"waiting\",\"delivery_receipt\":\"1234\",\"sales_invoice\":\"1234543\"}', '2026-03-14 05:25:51'),
(219, 13, 'transaction_update', 'transaction', 43, '{\"transaction_id\":43,\"po_number\":\"65678\",\"stage\":\"supply\",\"status\":\"COMPLETED\",\"remarks\":\"okay\",\"delivery_receipt\":\"1234\",\"sales_invoice\":\"1234543\"}', '2026-03-14 05:26:16'),
(220, 13, 'transaction_update', 'transaction', 43, '{\"transaction_id\":43,\"po_number\":\"65678\",\"stage\":\"supply\",\"status\":\"PARTIAL DELIVER\",\"remarks\":\"okay\",\"delivery_receipt\":\"1234\",\"sales_invoice\":\"1234543\"}', '2026-03-14 05:32:20'),
(221, 12, 'login', 'user', 12, 'Successful login', '2026-03-14 05:32:46'),
(222, 12, 'logout', 'user', 12, 'User logged out', '2026-03-14 05:34:37'),
(223, 13, 'login', 'user', 13, 'Successful login', '2026-03-14 05:34:44'),
(224, 13, 'transaction_update', 'transaction', 43, '{\"transaction_id\":43,\"po_number\":\"65678\",\"stage\":\"supply\",\"status\":\"COMPLETED\",\"remarks\":\"okay\",\"delivery_receipt\":\"1234\",\"sales_invoice\":\"1234543\"}', '2026-03-14 05:34:57'),
(225, 13, 'transaction_handoff_forward', 'transaction', 43, '{\"transaction_id\":43,\"po_number\":\"65678\",\"from_dept\":\"supply\",\"to_dept\":\"accounting_pre\"}', '2026-03-14 05:35:03'),
(226, 13, 'transaction_update', 'transaction', 43, '{\"transaction_id\":43,\"po_number\":\"65678\",\"stage\":\"supply\",\"status\":\"PARTIAL DELIVER\",\"remarks\":\"okay\",\"delivery_receipt\":\"1234\",\"sales_invoice\":\"1234543\"}', '2026-03-14 05:35:49'),
(227, 25, 'login', 'user', 25, 'Successful login (Google OAuth)', '2026-03-14 10:34:51'),
(228, 12, 'login', 'user', 12, 'Successful login', '2026-03-14 10:35:26'),
(229, 13, 'login', 'user', 13, 'Successful login', '2026-03-14 10:35:33'),
(230, 14, 'login', 'user', 14, 'Successful login', '2026-03-14 10:35:37'),
(231, 15, 'login', 'user', 15, 'Successful login', '2026-03-14 10:35:42'),
(232, 16, 'login', 'user', 16, 'Successful login', '2026-03-14 10:36:03'),
(233, 10, 'login', 'user', 10, 'Successful login', '2026-03-14 10:36:20'),
(234, 12, 'transaction_update', 'transaction', 44, '{\"transaction_id\":44,\"po_number\":\"876543456\",\"stage\":\"procurement\",\"status\":\"FOR SUPPLY REVIEW\",\"remarks\":\"\"}', '2026-03-14 11:41:03'),
(235, 12, 'transaction_update', 'transaction', 44, '{\"transaction_id\":44,\"po_number\":\"876543456\",\"stage\":\"procurement\",\"status\":\"COMPLETED\",\"remarks\":\"\"}', '2026-03-14 11:43:13'),
(236, 12, 'transaction_handoff_forward', 'transaction', 44, '{\"transaction_id\":44,\"po_number\":\"876543456\",\"from_dept\":\"procurement\",\"to_dept\":\"supply\"}', '2026-03-14 11:44:30'),
(237, 13, 'transaction_handoff_receive', 'transaction', 44, '{\"transaction_id\":44,\"po_number\":\"876543456\",\"from_dept\":\"procurement\",\"to_dept\":\"supply\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-14 11:44:38'),
(238, 13, 'transaction_update', 'transaction', 44, '{\"transaction_id\":44,\"po_number\":\"876543456\",\"stage\":\"supply\",\"status\":\"PARTIAL DELIVER\",\"remarks\":\"\",\"delivery_receipt\":\"234\",\"sales_invoice\":\"345\"}', '2026-03-14 11:44:59'),
(239, 13, 'transaction_update', 'transaction', 44, '{\"transaction_id\":44,\"po_number\":\"876543456\",\"stage\":\"supply\",\"status\":\"PARTIAL DELIVER\",\"remarks\":\"\",\"delivery_receipt\":\"234\",\"sales_invoice\":\"345\"}', '2026-03-14 11:46:20'),
(240, 13, 'transaction_update', 'transaction', 44, '{\"transaction_id\":44,\"po_number\":\"876543456\",\"stage\":\"supply\",\"status\":\"PARTIAL DELIVER\",\"remarks\":\"note\",\"delivery_receipt\":\"234\",\"sales_invoice\":\"345\"}', '2026-03-14 11:46:43'),
(241, 13, 'transaction_update', 'transaction', 44, '{\"transaction_id\":44,\"po_number\":\"876543456\",\"stage\":\"supply\",\"status\":\"COMPLETED\",\"remarks\":\"okay\",\"delivery_receipt\":\"234\",\"sales_invoice\":\"345\"}', '2026-03-14 11:48:36'),
(242, 13, 'transaction_handoff_forward', 'transaction', 44, '{\"transaction_id\":44,\"po_number\":\"876543456\",\"from_dept\":\"supply\",\"to_dept\":\"accounting_pre\"}', '2026-03-14 11:48:59'),
(243, 14, 'transaction_handoff_receive', 'transaction', 44, '{\"transaction_id\":44,\"po_number\":\"876543456\",\"from_dept\":\"supply\",\"to_dept\":\"accounting_pre\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-14 11:49:10'),
(244, 12, 'login', 'user', 12, 'Successful login', '2026-03-14 12:19:10'),
(245, 12, 'login', 'user', 12, 'Successful login', '2026-03-15 04:25:05'),
(246, 13, 'login', 'user', 13, 'Successful login', '2026-03-15 04:25:17'),
(247, 14, 'login', 'user', 14, 'Successful login', '2026-03-15 04:25:31'),
(248, 15, 'login', 'user', 15, 'Successful login', '2026-03-15 04:25:37'),
(249, 16, 'login', 'user', 16, 'Successful login', '2026-03-15 04:25:51'),
(250, 12, 'transaction_update', 'transaction', 45, '{\"transaction_id\":45,\"po_number\":\"0987678\",\"stage\":\"procurement\",\"status\":\"FOR SUPPLY REVIEW\",\"remarks\":\"\"}', '2026-03-15 05:01:50'),
(251, 12, 'transaction_update', 'transaction', 45, '{\"transaction_id\":45,\"po_number\":\"0987678\",\"stage\":\"procurement\",\"status\":\"COMPLETED\",\"remarks\":\"\"}', '2026-03-15 05:02:03'),
(252, 12, 'transaction_handoff_forward', 'transaction', 45, '{\"transaction_id\":45,\"po_number\":\"0987678\",\"from_dept\":\"procurement\",\"to_dept\":\"supply\"}', '2026-03-15 05:02:06'),
(253, 13, 'transaction_handoff_receive', 'transaction', 45, '{\"transaction_id\":45,\"po_number\":\"0987678\",\"from_dept\":\"procurement\",\"to_dept\":\"supply\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-15 05:02:17'),
(254, 13, 'transaction_update', 'transaction', 45, '{\"transaction_id\":45,\"po_number\":\"0987678\",\"stage\":\"supply\",\"status\":\"PARTIAL DELIVER\",\"remarks\":\"\",\"delivery_receipt\":\"\",\"sales_invoice\":\"\"}', '2026-03-15 05:02:36'),
(255, 12, 'transaction_update', 'transaction', 45, '{\"transaction_id\":45,\"po_number\":\"0987678\",\"stage\":\"procurement\",\"status\":\"COMPLETED\",\"remarks\":\"okay\"}', '2026-03-15 05:02:53'),
(256, 12, 'transaction_update', 'transaction', 45, '{\"transaction_id\":45,\"po_number\":\"0987678\",\"stage\":\"procurement\",\"status\":\"FOR SUPPLY REVIEW\",\"remarks\":\"okay\"}', '2026-03-15 05:08:55'),
(257, 13, 'transaction_update', 'transaction', 45, '{\"transaction_id\":45,\"po_number\":\"0987678\",\"stage\":\"supply\",\"status\":\"PARTIAL DELIVER\",\"remarks\":\"\",\"delivery_receipt\":\"767\",\"sales_invoice\":\"786\"}', '2026-03-15 05:34:36'),
(258, 12, 'transaction_update', 'transaction', 45, '{\"transaction_id\":45,\"po_number\":\"0987678\",\"stage\":\"procurement\",\"status\":\"FOR SUPPLY REVIEW\",\"remarks\":\"nop\"}', '2026-03-15 05:35:15'),
(259, 13, 'transaction_update', 'transaction', 45, '{\"transaction_id\":45,\"po_number\":\"0987678\",\"stage\":\"supply\",\"status\":\"COMPLETED\",\"remarks\":\"\",\"delivery_receipt\":\"767\",\"sales_invoice\":\"786\"}', '2026-03-15 05:36:34'),
(260, 13, 'transaction_handoff_forward', 'transaction', 45, '{\"transaction_id\":45,\"po_number\":\"0987678\",\"from_dept\":\"supply\",\"to_dept\":\"accounting_pre\"}', '2026-03-15 05:37:15'),
(261, 14, 'transaction_handoff_receive', 'transaction', 45, '{\"transaction_id\":45,\"po_number\":\"0987678\",\"from_dept\":\"supply\",\"to_dept\":\"accounting_pre\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-15 05:37:21'),
(262, 14, 'transaction_update', 'transaction', 45, '{\"transaction_id\":45,\"po_number\":\"0987678\",\"stage\":\"accounting_pre\",\"status\":\"COMPLETED\",\"remarks\":\"DV Amount: 8000\",\"dv_amount\":\"8000\"}', '2026-03-15 05:44:43'),
(263, 14, 'transaction_handoff_forward', 'transaction', 45, '{\"transaction_id\":45,\"po_number\":\"0987678\",\"from_dept\":\"accounting_pre\",\"to_dept\":\"budget\"}', '2026-03-15 05:47:22'),
(264, 15, 'transaction_handoff_receive', 'transaction', 45, '{\"transaction_id\":45,\"po_number\":\"0987678\",\"from_dept\":\"accounting_pre\",\"to_dept\":\"budget\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-15 05:48:42'),
(265, 15, 'transaction_update', 'transaction', 45, '{\"transaction_id\":45,\"po_number\":\"0987678\",\"stage\":\"budget\",\"status\":\"ACCOUNTS PAYABLE\",\"remarks\":\"\",\"dv_number\":\"87678\",\"dv_date\":\"2026-03-10\",\"demandability\":\"Not Yet Due and Demandable\"}', '2026-03-15 05:49:21'),
(266, 15, 'transaction_update', 'transaction', 45, '{\"transaction_id\":45,\"po_number\":\"0987678\",\"stage\":\"budget\",\"status\":\"COMPLETED\",\"remarks\":\"\",\"dv_number\":\"87678\",\"dv_date\":\"2026-03-10\",\"demandability\":\"\"}', '2026-03-15 05:50:08'),
(267, 15, 'transaction_handoff_forward', 'transaction', 45, '{\"transaction_id\":45,\"po_number\":\"0987678\",\"from_dept\":\"budget\",\"to_dept\":\"accounting_post\"}', '2026-03-15 05:51:01'),
(268, 14, 'transaction_handoff_receive', 'transaction', 45, '{\"transaction_id\":45,\"po_number\":\"0987678\",\"from_dept\":\"budget\",\"to_dept\":\"accounting_post\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-15 05:51:25'),
(269, 14, 'transaction_update', 'transaction', 45, '{\"transaction_id\":45,\"po_number\":\"0987678\",\"stage\":\"accounting_post\",\"status\":\"COMPLETED\",\"remarks\":\"DV Amount: 1000\",\"dv_amount\":\"1000\"}', '2026-03-15 05:52:28'),
(270, 14, 'transaction_handoff_forward', 'transaction', 45, '{\"transaction_id\":45,\"po_number\":\"0987678\",\"from_dept\":\"accounting_post\",\"to_dept\":\"cashier\"}', '2026-03-15 05:52:47'),
(271, 16, 'transaction_handoff_receive', 'transaction', 45, '{\"transaction_id\":45,\"po_number\":\"0987678\",\"from_dept\":\"accounting_post\",\"to_dept\":\"cashier\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-15 05:53:14'),
(272, 16, 'transaction_update', 'transaction', 45, '{\"transaction_id\":45,\"po_number\":\"0987678\",\"stage\":\"cashier\",\"status\":\"For OR Issuance\",\"remarks\":\"Amount: 100\",\"or_number\":\"2234\",\"or_date\":\"2026-03-06\",\"payment_date\":\"2026-03-17\",\"landbank_ref\":\"100\"}', '2026-03-15 05:53:57'),
(273, 14, 'transaction_update', 'transaction', 44, '{\"transaction_id\":44,\"po_number\":\"876543456\",\"stage\":\"accounting_pre\",\"status\":\"COMPLETED\",\"remarks\":\"\",\"dv_amount\":\"\"}', '2026-03-15 07:32:38'),
(274, 14, 'transaction_handoff_forward', 'transaction', 44, '{\"transaction_id\":44,\"po_number\":\"876543456\",\"from_dept\":\"accounting_pre\",\"to_dept\":\"budget\"}', '2026-03-15 07:32:51'),
(275, 14, 'login', 'user', 14, 'Successful login', '2026-03-15 07:46:35'),
(276, 15, 'transaction_handoff_receive', 'transaction', 44, '{\"transaction_id\":44,\"po_number\":\"876543456\",\"from_dept\":\"accounting_pre\",\"to_dept\":\"budget\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-15 07:46:51'),
(277, 15, 'transaction_update', 'transaction', 44, '{\"transaction_id\":44,\"po_number\":\"876543456\",\"stage\":\"budget\",\"status\":\"COMPLETED\",\"remarks\":\"\",\"dv_number\":\"\",\"dv_date\":\"\",\"demandability\":\"\"}', '2026-03-15 07:49:28'),
(278, 15, 'transaction_handoff_forward', 'transaction', 44, '{\"transaction_id\":44,\"po_number\":\"876543456\",\"from_dept\":\"budget\",\"to_dept\":\"accounting_post\"}', '2026-03-15 07:49:31'),
(279, 14, 'transaction_handoff_receive', 'transaction', 44, '{\"transaction_id\":44,\"po_number\":\"876543456\",\"from_dept\":\"budget\",\"to_dept\":\"accounting_post\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-15 08:21:05'),
(280, 14, 'transaction_update', 'transaction', 44, '{\"transaction_id\":44,\"po_number\":\"876543456\",\"stage\":\"accounting_post\",\"status\":\"COMPLETED\",\"remarks\":\"\",\"dv_amount\":\"\"}', '2026-03-15 08:22:15'),
(281, 14, 'transaction_handoff_forward', 'transaction', 44, '{\"transaction_id\":44,\"po_number\":\"876543456\",\"from_dept\":\"accounting_post\",\"to_dept\":\"cashier\"}', '2026-03-15 08:22:31'),
(282, 16, 'transaction_handoff_receive', 'transaction', 44, '{\"transaction_id\":44,\"po_number\":\"876543456\",\"from_dept\":\"accounting_post\",\"to_dept\":\"cashier\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-15 08:24:59'),
(283, 16, 'transaction_update', 'transaction', 44, '{\"transaction_id\":44,\"po_number\":\"876543456\",\"stage\":\"cashier\",\"status\":\"COMPLETED\",\"remarks\":\"\",\"or_number\":\"\",\"or_date\":\"\",\"payment_date\":\"\",\"landbank_ref\":\"\"}', '2026-03-15 08:25:19'),
(284, 12, 'login', 'user', 12, 'Successful login', '2026-03-15 20:45:46'),
(285, 13, 'login', 'user', 13, 'Successful login', '2026-03-15 20:47:21'),
(286, 12, 'logout', 'user', 12, 'User logged out', '2026-03-15 21:29:31'),
(287, 13, 'logout', 'user', 13, 'User logged out', '2026-03-15 21:29:59'),
(288, 12, 'login', 'user', 12, 'Successful login', '2026-03-15 21:31:01'),
(289, 12, 'login', 'user', 12, 'Successful login', '2026-03-15 21:31:13'),
(290, 14, 'login', 'user', 14, 'Successful login', '2026-03-15 21:31:34'),
(291, 12, 'logout', 'user', 12, 'User logged out', '2026-03-15 21:31:40'),
(292, 13, 'login', 'user', 13, 'Successful login', '2026-03-15 21:31:45'),
(293, 15, 'login', 'user', 15, 'Successful login', '2026-03-15 21:32:02'),
(294, 16, 'login', 'user', 16, 'Successful login', '2026-03-15 21:32:11'),
(295, 14, 'transaction_handoff_receive', 'transaction', 43, '{\"transaction_id\":43,\"po_number\":\"65678\",\"from_dept\":\"supply\",\"to_dept\":\"accounting_pre\",\"delay_seconds\":118657,\"exceeded_grace\":1}', '2026-03-15 21:32:40'),
(296, NULL, 'login_failed', 'user', NULL, '{\"attempted_username\":\"cahier\",\"reason\":\"unknown_username\"}', '2026-03-16 02:54:10'),
(297, 16, 'login', 'user', 16, 'Successful login', '2026-03-16 02:54:14'),
(298, 10, 'login', 'user', 10, 'Successful login', '2026-03-16 02:54:57'),
(299, 25, 'login', 'user', 25, 'Successful login (Google OAuth)', '2026-03-16 02:56:10'),
(300, 12, 'login', 'user', 12, 'Successful login', '2026-03-16 02:56:38'),
(301, 14, 'login', 'user', 14, 'Successful login', '2026-03-16 02:56:49'),
(302, 15, 'login', 'user', 15, 'Successful login', '2026-03-16 02:57:32'),
(303, 12, 'transaction_update', 'transaction', 47, '{\"transaction_id\":47,\"po_number\":\"0987654894\",\"stage\":\"procurement\",\"status\":\"FOR SUPPLY REVIEW\",\"remarks\":\"\"}', '2026-03-16 03:02:38'),
(304, 12, 'transaction_update', 'transaction', 47, '{\"transaction_id\":47,\"po_number\":\"0987654894\",\"stage\":\"procurement\",\"status\":\"COMPLETED\",\"remarks\":\"\"}', '2026-03-16 03:02:47'),
(305, 12, 'transaction_handoff_forward', 'transaction', 47, '{\"transaction_id\":47,\"po_number\":\"0987654894\",\"from_dept\":\"procurement\",\"to_dept\":\"supply\"}', '2026-03-16 03:05:58'),
(306, 13, 'transaction_handoff_receive', 'transaction', 47, '{\"transaction_id\":47,\"po_number\":\"0987654894\",\"from_dept\":\"procurement\",\"to_dept\":\"supply\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-16 03:06:33'),
(307, 13, 'transaction_update', 'transaction', 47, '{\"transaction_id\":47,\"po_number\":\"0987654894\",\"stage\":\"supply\",\"status\":\"PARTIAL DELIVER\",\"remarks\":\"\",\"delivery_receipt\":\"3454325\",\"sales_invoice\":\"6543\"}', '2026-03-16 03:07:30'),
(308, 13, 'transaction_update', 'transaction', 47, '{\"transaction_id\":47,\"po_number\":\"0987654894\",\"stage\":\"supply\",\"status\":\"COMPLETED\",\"remarks\":\"\",\"delivery_receipt\":\"3454325\",\"sales_invoice\":\"6543\"}', '2026-03-16 03:08:14'),
(309, 13, 'transaction_handoff_forward', 'transaction', 47, '{\"transaction_id\":47,\"po_number\":\"0987654894\",\"from_dept\":\"supply\",\"to_dept\":\"accounting_pre\"}', '2026-03-16 03:09:30'),
(310, 14, 'transaction_handoff_receive', 'transaction', 47, '{\"transaction_id\":47,\"po_number\":\"0987654894\",\"from_dept\":\"supply\",\"to_dept\":\"accounting_pre\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-16 03:09:34'),
(311, 14, 'transaction_update', 'transaction', 47, '{\"transaction_id\":47,\"po_number\":\"0987654894\",\"stage\":\"accounting_pre\",\"status\":\"FOR ORS\",\"remarks\":\"\",\"dv_amount\":\"\"}', '2026-03-16 03:09:45'),
(312, 14, 'transaction_update', 'transaction', 47, '{\"transaction_id\":47,\"po_number\":\"0987654894\",\"stage\":\"accounting_pre\",\"status\":\"FOR VOUCHER\",\"remarks\":\"\",\"dv_amount\":\"\"}', '2026-03-16 03:09:50'),
(313, 14, 'transaction_update', 'transaction', 47, '{\"transaction_id\":47,\"po_number\":\"0987654894\",\"stage\":\"accounting_pre\",\"status\":\"COMPLETED\",\"remarks\":\"\",\"dv_amount\":\"\"}', '2026-03-16 03:10:29'),
(314, 14, 'transaction_handoff_forward', 'transaction', 47, '{\"transaction_id\":47,\"po_number\":\"0987654894\",\"from_dept\":\"accounting_pre\",\"to_dept\":\"budget\"}', '2026-03-16 03:10:31'),
(315, 15, 'transaction_handoff_receive', 'transaction', 47, '{\"transaction_id\":47,\"po_number\":\"0987654894\",\"from_dept\":\"accounting_pre\",\"to_dept\":\"budget\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-16 03:10:36'),
(316, 15, 'transaction_update', 'transaction', 47, '{\"transaction_id\":47,\"po_number\":\"0987654894\",\"stage\":\"budget\",\"status\":\"ACCOUNTS PAYABLE\",\"remarks\":\"\",\"dv_number\":\"34442\",\"dv_date\":\"2026-03-10\",\"demandability\":\"\"}', '2026-03-16 03:11:26'),
(317, 15, 'transaction_update', 'transaction', 47, '{\"transaction_id\":47,\"po_number\":\"0987654894\",\"stage\":\"budget\",\"status\":\"COMPLETED\",\"remarks\":\"\",\"dv_number\":\"34442\",\"dv_date\":\"2026-03-10\",\"demandability\":\"Due and Demandable\"}', '2026-03-16 03:12:19'),
(318, 15, 'transaction_handoff_forward', 'transaction', 47, '{\"transaction_id\":47,\"po_number\":\"0987654894\",\"from_dept\":\"budget\",\"to_dept\":\"accounting_post\"}', '2026-03-16 03:14:08'),
(319, 14, 'transaction_handoff_receive', 'transaction', 47, '{\"transaction_id\":47,\"po_number\":\"0987654894\",\"from_dept\":\"budget\",\"to_dept\":\"accounting_post\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-16 03:14:18'),
(320, 14, 'transaction_update', 'transaction', 47, '{\"transaction_id\":47,\"po_number\":\"0987654894\",\"stage\":\"accounting_post\",\"status\":\"COMPLETED\",\"remarks\":\"DV Amount: 9000\",\"dv_amount\":\"9000\"}', '2026-03-16 03:15:08'),
(321, 14, 'transaction_handoff_forward', 'transaction', 47, '{\"transaction_id\":47,\"po_number\":\"0987654894\",\"from_dept\":\"accounting_post\",\"to_dept\":\"cashier\"}', '2026-03-16 03:15:18'),
(322, 16, 'transaction_handoff_receive', 'transaction', 47, '{\"transaction_id\":47,\"po_number\":\"0987654894\",\"from_dept\":\"accounting_post\",\"to_dept\":\"cashier\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-16 03:15:30'),
(323, 16, 'transaction_update', 'transaction', 47, '{\"transaction_id\":47,\"po_number\":\"0987654894\",\"stage\":\"cashier\",\"status\":\"For ACIC\",\"remarks\":\"Amount: 7890\",\"or_number\":\"876789\",\"or_date\":\"2026-03-11\",\"payment_date\":\"2026-03-27\",\"landbank_ref\":\"7890\"}', '2026-03-16 03:16:33'),
(324, 10, 'login', 'user', 10, 'Successful login', '2026-03-16 03:35:08'),
(325, 10, 'login', 'user', 10, 'Successful login', '2026-03-17 06:41:48'),
(326, 12, 'transaction_update', 'transaction', 48, '{\"transaction_id\":48,\"po_number\":\"345654\",\"stage\":\"procurement\",\"status\":\"COMPLETED\",\"remarks\":\"\"}', '2026-03-17 06:55:56'),
(327, 13, 'login', 'user', 13, 'Successful login', '2026-03-17 06:58:21'),
(328, 15, 'login', 'user', 15, 'Successful login', '2026-03-17 06:58:27'),
(329, 12, 'transaction_handoff_forward', 'transaction', 48, '{\"transaction_id\":48,\"po_number\":\"345654\",\"from_dept\":\"procurement\",\"to_dept\":\"supply\"}', '2026-03-17 06:58:40'),
(330, 13, 'transaction_handoff_receive', 'transaction', 48, '{\"transaction_id\":48,\"po_number\":\"345654\",\"from_dept\":\"procurement\",\"to_dept\":\"supply\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-17 07:01:24'),
(331, 13, 'transaction_update', 'transaction', 48, '{\"transaction_id\":48,\"po_number\":\"345654\",\"stage\":\"supply\",\"status\":\"COMPLETED\",\"remarks\":\"\",\"delivery_receipt\":\"34564\",\"sales_invoice\":\"76567\"}', '2026-03-17 07:03:36'),
(332, 13, 'transaction_handoff_forward', 'transaction', 48, '{\"transaction_id\":48,\"po_number\":\"345654\",\"from_dept\":\"supply\",\"to_dept\":\"accounting_pre\"}', '2026-03-17 07:03:59'),
(333, 14, 'login', 'user', 14, 'Successful login', '2026-03-17 07:04:24'),
(334, 14, 'transaction_handoff_receive', 'transaction', 48, '{\"transaction_id\":48,\"po_number\":\"345654\",\"from_dept\":\"supply\",\"to_dept\":\"accounting_pre\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-17 07:05:17'),
(335, 14, 'transaction_update', 'transaction', 48, '{\"transaction_id\":48,\"po_number\":\"345654\",\"stage\":\"accounting_pre\",\"status\":\"FOR ORS\",\"remarks\":\"DV Amount: 600\",\"dv_amount\":\"600\"}', '2026-03-17 07:07:53'),
(336, 14, 'transaction_update', 'transaction', 48, '{\"transaction_id\":48,\"po_number\":\"345654\",\"stage\":\"accounting_pre\",\"status\":\"COMPLETED\",\"remarks\":\"\",\"dv_amount\":\"\"}', '2026-03-17 07:08:37'),
(337, 14, 'transaction_handoff_forward', 'transaction', 48, '{\"transaction_id\":48,\"po_number\":\"345654\",\"from_dept\":\"accounting_pre\",\"to_dept\":\"budget\"}', '2026-03-17 07:08:54'),
(338, 15, 'transaction_handoff_receive', 'transaction', 48, '{\"transaction_id\":48,\"po_number\":\"345654\",\"from_dept\":\"accounting_pre\",\"to_dept\":\"budget\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-17 07:09:01'),
(339, 15, 'transaction_update', 'transaction', 48, '{\"transaction_id\":48,\"po_number\":\"345654\",\"stage\":\"budget\",\"status\":\"ACCOUNTS PAYABLE\",\"remarks\":\"\",\"dv_number\":\"876\",\"dv_date\":\"\",\"demandability\":\"\"}', '2026-03-17 07:10:01'),
(340, 15, 'transaction_update', 'transaction', 48, '{\"transaction_id\":48,\"po_number\":\"345654\",\"stage\":\"budget\",\"status\":\"ACCOUNTS PAYABLE\",\"remarks\":\"\",\"dv_number\":\"876\",\"dv_date\":\"\",\"demandability\":\"Due and Demandable\"}', '2026-03-17 07:10:53'),
(341, 15, 'transaction_update', 'transaction', 48, '{\"transaction_id\":48,\"po_number\":\"345654\",\"stage\":\"budget\",\"status\":\"COMPLETED\",\"remarks\":\"\",\"dv_number\":\"876\",\"dv_date\":\"\",\"demandability\":\"Due and Demandable\"}', '2026-03-17 07:11:25'),
(342, 15, 'transaction_handoff_forward', 'transaction', 48, '{\"transaction_id\":48,\"po_number\":\"345654\",\"from_dept\":\"budget\",\"to_dept\":\"accounting_post\"}', '2026-03-17 07:11:34'),
(343, 14, 'transaction_handoff_receive', 'transaction', 48, '{\"transaction_id\":48,\"po_number\":\"345654\",\"from_dept\":\"budget\",\"to_dept\":\"accounting_post\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-17 07:11:40'),
(344, 16, 'login', 'user', 16, 'Successful login', '2026-03-17 07:12:02'),
(345, 14, 'transaction_update', 'transaction', 48, '{\"transaction_id\":48,\"po_number\":\"345654\",\"stage\":\"accounting_post\",\"status\":\"COMPLETED\",\"remarks\":\"\",\"dv_amount\":\"\"}', '2026-03-17 07:12:32'),
(346, 14, 'transaction_handoff_forward', 'transaction', 48, '{\"transaction_id\":48,\"po_number\":\"345654\",\"from_dept\":\"accounting_post\",\"to_dept\":\"cashier\"}', '2026-03-17 07:12:49'),
(347, 16, 'transaction_handoff_receive', 'transaction', 48, '{\"transaction_id\":48,\"po_number\":\"345654\",\"from_dept\":\"accounting_post\",\"to_dept\":\"cashier\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-17 07:12:54'),
(348, 25, 'login', 'user', 25, 'Successful login (Google OAuth)', '2026-03-17 07:13:32'),
(349, 16, 'transaction_update', 'transaction', 48, '{\"transaction_id\":48,\"po_number\":\"345654\",\"stage\":\"cashier\",\"status\":\"COMPLETED\",\"remarks\":\"\",\"or_number\":\"\",\"or_date\":\"\",\"payment_date\":\"\",\"landbank_ref\":\"\"}', '2026-03-17 07:13:49'),
(350, 16, 'transaction_notify_supplier', 'transaction', 48, '{\"transaction_id\":48,\"po_number\":\"345654\",\"supplier_id\":7,\"message\":\"Your PO 345654 is now marked as COMPLETED. Please check the portal for details.\"}', '2026-03-17 07:14:03'),
(351, 12, 'login', 'user', 12, 'Successful login', '2026-03-17 12:51:36'),
(352, 13, 'login', 'user', 13, 'Successful login', '2026-03-17 12:51:41'),
(353, 14, 'login', 'user', 14, 'Successful login', '2026-03-17 12:51:58'),
(354, 15, 'login', 'user', 15, 'Successful login', '2026-03-17 12:52:04'),
(355, 16, 'login', 'user', 16, 'Successful login', '2026-03-17 12:52:12'),
(356, 25, 'login', 'user', 25, 'Successful login (Google OAuth)', '2026-03-17 12:52:30'),
(357, 10, 'login', 'user', 10, 'Successful login', '2026-03-17 12:58:11'),
(358, 12, 'login', 'user', 12, 'Successful login', '2026-03-17 15:37:33'),
(359, 13, 'login', 'user', 13, 'Successful login', '2026-03-17 15:38:03'),
(360, 12, 'transaction_update', 'transaction', 49, '{\"transaction_id\":49,\"po_number\":\"8765678\",\"stage\":\"procurement\",\"status\":\"FOR SUPPLY REVIEW\",\"remarks\":\"\"}', '2026-03-17 15:38:17'),
(361, 12, 'transaction_handoff_forward', 'transaction', 49, '{\"transaction_id\":49,\"po_number\":\"8765678\",\"from_dept\":\"procurement\",\"to_dept\":\"supply\"}', '2026-03-17 15:38:38'),
(362, 13, 'transaction_handoff_receive', 'transaction', 49, '{\"transaction_id\":49,\"po_number\":\"8765678\",\"from_dept\":\"procurement\",\"to_dept\":\"supply\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-17 15:38:45'),
(363, 25, 'login', 'user', 25, 'Successful login (Google OAuth)', '2026-03-17 16:38:11'),
(364, 25, 'login', 'user', 25, 'Successful login (Google OAuth)', '2026-03-17 16:38:25'),
(365, 10, 'login', 'user', 10, 'Successful login', '2026-03-17 16:42:18'),
(366, 13, 'transaction_update', 'transaction', 49, '{\"transaction_id\":49,\"po_number\":\"8765678\",\"stage\":\"supply\",\"status\":\"PARTIAL DELIVERY\",\"remarks\":\"\",\"delivery_receipt\":\"\",\"sales_invoice\":\"\"}', '2026-03-17 16:47:45'),
(367, 14, 'login', 'user', 14, 'Successful login', '2026-03-17 16:47:55'),
(368, 13, 'transaction_handoff_forward', 'transaction', 49, '{\"transaction_id\":49,\"po_number\":\"8765678\",\"from_dept\":\"supply\",\"to_dept\":\"accounting_pre\"}', '2026-03-17 16:48:14'),
(369, 14, 'transaction_handoff_receive', 'transaction', 49, '{\"transaction_id\":49,\"po_number\":\"8765678\",\"from_dept\":\"supply\",\"to_dept\":\"accounting_pre\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-17 17:06:39'),
(370, 14, 'transaction_update', 'transaction', 49, '{\"transaction_id\":49,\"po_number\":\"8765678\",\"stage\":\"accounting_pre\",\"status\":\"FOR ORS\",\"remarks\":\"\",\"dv_amount\":\"\"}', '2026-03-17 17:29:43'),
(371, 14, 'transaction_update', 'transaction', 49, '{\"transaction_id\":49,\"po_number\":\"8765678\",\"stage\":\"accounting_pre\",\"status\":\"FOR ORS\",\"remarks\":\"DV Amount: 8000\",\"dv_amount\":\"8000\"}', '2026-03-17 17:30:01'),
(372, 14, 'transaction_handoff_forward', 'transaction', 49, '{\"transaction_id\":49,\"po_number\":\"8765678\",\"from_dept\":\"accounting_pre\",\"to_dept\":\"budget\"}', '2026-03-17 17:30:27'),
(373, 15, 'login', 'user', 15, 'Successful login', '2026-03-17 17:31:42'),
(374, 15, 'transaction_handoff_receive', 'transaction', 49, '{\"transaction_id\":49,\"po_number\":\"8765678\",\"from_dept\":\"accounting_pre\",\"to_dept\":\"budget\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-17 17:45:54'),
(375, 15, 'transaction_update', 'transaction', 49, '{\"transaction_id\":49,\"po_number\":\"8765678\",\"stage\":\"budget\",\"status\":\"FOR PAYMENT\",\"remarks\":\"\",\"dv_number\":\"\",\"dv_date\":\"\",\"demandability\":\"\"}', '2026-03-17 17:46:39'),
(376, 15, 'transaction_handoff_forward', 'transaction', 49, '{\"transaction_id\":49,\"po_number\":\"8765678\",\"from_dept\":\"budget\",\"to_dept\":\"accounting_post\"}', '2026-03-17 17:46:57'),
(377, 14, 'transaction_handoff_receive', 'transaction', 49, '{\"transaction_id\":49,\"po_number\":\"8765678\",\"from_dept\":\"budget\",\"to_dept\":\"accounting_post\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-17 17:47:17'),
(378, 15, 'transaction_update', 'transaction', 49, '{\"transaction_id\":49,\"po_number\":\"8765678\",\"stage\":\"budget\",\"status\":\"ACCOUNTS PAYABLE\",\"remarks\":\"\",\"dv_number\":\"\",\"dv_date\":\"\",\"demandability\":\"\"}', '2026-03-17 17:47:32'),
(379, 14, 'transaction_update', 'transaction', 49, '{\"transaction_id\":49,\"po_number\":\"8765678\",\"stage\":\"accounting_post\",\"status\":\"FOR VOUCHER\",\"remarks\":\"\",\"dv_amount\":\"\"}', '2026-03-17 17:47:44'),
(380, 14, 'transaction_handoff_forward', 'transaction', 49, '{\"transaction_id\":49,\"po_number\":\"8765678\",\"from_dept\":\"accounting_post\",\"to_dept\":\"cashier\"}', '2026-03-17 17:48:25'),
(381, 16, 'transaction_handoff_receive', 'transaction', 49, '{\"transaction_id\":49,\"po_number\":\"8765678\",\"from_dept\":\"accounting_post\",\"to_dept\":\"cashier\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-17 17:49:12'),
(382, 16, 'transaction_update', 'transaction', 49, '{\"transaction_id\":49,\"po_number\":\"8765678\",\"stage\":\"cashier\",\"status\":\"COMPLETED\",\"remarks\":\"\",\"or_number\":\"\",\"or_date\":\"\",\"payment_date\":\"\",\"landbank_ref\":\"\"}', '2026-03-17 17:49:21'),
(383, 12, 'transaction_handoff_forward', 'transaction', 50, '{\"transaction_id\":50,\"po_number\":\"5678987\",\"from_dept\":\"procurement\",\"to_dept\":\"supply\"}', '2026-03-17 17:50:14'),
(384, 12, 'transaction_update', 'transaction', 50, '{\"transaction_id\":50,\"po_number\":\"5678987\",\"stage\":\"procurement\",\"status\":\"FOR SUPPLY REVIEW\",\"remarks\":\"\"}', '2026-03-17 17:50:21'),
(385, 13, 'transaction_handoff_receive', 'transaction', 50, '{\"transaction_id\":50,\"po_number\":\"5678987\",\"from_dept\":\"procurement\",\"to_dept\":\"supply\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-17 17:50:28'),
(386, 13, 'transaction_update', 'transaction', 50, '{\"transaction_id\":50,\"po_number\":\"5678987\",\"stage\":\"supply\",\"status\":\"COMPLETED\",\"remarks\":\"\",\"delivery_receipt\":\"\",\"sales_invoice\":\"\"}', '2026-03-17 17:50:37'),
(387, 13, 'transaction_handoff_forward', 'transaction', 50, '{\"transaction_id\":50,\"po_number\":\"5678987\",\"from_dept\":\"supply\",\"to_dept\":\"accounting_pre\"}', '2026-03-17 17:50:40'),
(388, 14, 'transaction_handoff_receive', 'transaction', 50, '{\"transaction_id\":50,\"po_number\":\"5678987\",\"from_dept\":\"supply\",\"to_dept\":\"accounting_pre\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-17 17:50:45'),
(389, 14, 'transaction_update', 'transaction', 50, '{\"transaction_id\":50,\"po_number\":\"5678987\",\"stage\":\"accounting_pre\",\"status\":\"FOR ORS\",\"remarks\":\"\",\"dv_amount\":\"\"}', '2026-03-17 17:50:51'),
(390, 14, 'transaction_handoff_forward', 'transaction', 50, '{\"transaction_id\":50,\"po_number\":\"5678987\",\"from_dept\":\"accounting_pre\",\"to_dept\":\"budget\"}', '2026-03-17 17:50:56'),
(391, 15, 'transaction_handoff_receive', 'transaction', 50, '{\"transaction_id\":50,\"po_number\":\"5678987\",\"from_dept\":\"accounting_pre\",\"to_dept\":\"budget\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-17 17:51:06'),
(392, 15, 'transaction_update', 'transaction', 50, '{\"transaction_id\":50,\"po_number\":\"5678987\",\"stage\":\"budget\",\"status\":\"FOR PAYMENT\",\"remarks\":\"\",\"dv_number\":\"\",\"dv_date\":\"\",\"demandability\":\"\"}', '2026-03-17 17:51:13'),
(393, 15, 'transaction_handoff_forward', 'transaction', 50, '{\"transaction_id\":50,\"po_number\":\"5678987\",\"from_dept\":\"budget\",\"to_dept\":\"accounting_post\"}', '2026-03-17 17:51:18'),
(394, 14, 'transaction_handoff_receive', 'transaction', 50, '{\"transaction_id\":50,\"po_number\":\"5678987\",\"from_dept\":\"budget\",\"to_dept\":\"accounting_post\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-17 17:51:30'),
(395, 14, 'transaction_update', 'transaction', 50, '{\"transaction_id\":50,\"po_number\":\"5678987\",\"stage\":\"accounting_post\",\"status\":\"FOR VOUCHER\",\"remarks\":\"\",\"dv_amount\":\"\"}', '2026-03-17 17:51:40'),
(396, 14, 'transaction_handoff_forward', 'transaction', 50, '{\"transaction_id\":50,\"po_number\":\"5678987\",\"from_dept\":\"accounting_post\",\"to_dept\":\"cashier\"}', '2026-03-17 17:51:43'),
(397, 16, 'transaction_handoff_receive', 'transaction', 50, '{\"transaction_id\":50,\"po_number\":\"5678987\",\"from_dept\":\"accounting_post\",\"to_dept\":\"cashier\",\"delay_seconds\":0,\"exceeded_grace\":0}', '2026-03-17 17:51:49'),
(398, 10, 'login', 'user', 10, 'Successful login', '2026-03-17 18:07:19'),
(399, 10, 'login', 'user', 10, 'Successful login', '2026-03-17 19:20:59'),
(400, 12, 'login', 'user', 12, 'Successful login', '2026-03-17 19:21:11'),
(401, 10, 'logout', 'user', 10, 'User logged out', '2026-03-17 19:24:33'),
(402, 16, 'login', 'user', 16, 'Successful login', '2026-03-17 19:24:52');

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
('handoff_grace_seconds', '120', '2026-03-14 01:13:01');

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
(1, 'supply', 22, 'Pending Transaction', 'PO 12345 needs your action.', 'transaction_view.php?id=22', 1, '2026-03-04 22:10:42'),
(2, 'supply', 22, 'Pending Transaction', 'PO 12345 needs your action.', 'transaction_view.php?id=22', 1, '2026-03-04 22:11:07'),
(3, 'accounting', 22, 'Pending Transaction', 'PO 12345 needs your action.', 'transaction_view.php?id=22', 1, '2026-03-04 22:14:42'),
(4, 'accounting', 22, 'Supply Completed', 'Supply marked PO 12345 as Completed.', 'transaction_view.php?id=22', 1, '2026-03-04 22:14:42'),
(5, 'budget', 22, 'Pending Transaction', 'PO 12345 needs your action.', 'transaction_view.php?id=22', 1, '2026-03-04 22:18:08'),
(6, 'budget', 22, 'Accounting Completed', 'Accounting marked PO 12345 as Completed.', 'transaction_view.php?id=22', 0, '2026-03-04 22:18:08'),
(7, 'accounting', 22, 'Pending Transaction', 'PO 12345 needs your action.', 'transaction_view.php?id=22', 1, '2026-03-04 22:33:06'),
(8, 'accounting', 22, 'Budget Completed', 'Budget marked PO 12345 as Completed.', 'transaction_view.php?id=22', 1, '2026-03-04 22:33:06'),
(9, 'procurement', 22, 'Accounting Completed', 'Accounting marked PO 12345 as Completed.', 'transaction_view.php?id=22', 1, '2026-03-04 22:42:05'),
(10, 'supply', 22, 'Accounting Completed', 'Accounting marked PO 12345 as Completed.', 'transaction_view.php?id=22', 0, '2026-03-04 22:42:05'),
(11, 'accounting', 22, 'Accounting Completed', 'Accounting marked PO 12345 as Completed.', 'transaction_view.php?id=22', 0, '2026-03-04 22:42:05'),
(12, 'budget', 22, 'Accounting Completed', 'Accounting marked PO 12345 as Completed.', 'transaction_view.php?id=22', 0, '2026-03-04 22:42:05'),
(13, 'procurement', 22, 'Cashier Completed', 'Cashier marked PO 12345 as Completed.', 'transaction_view.php?id=22', 1, '2026-03-04 22:43:19'),
(14, 'supply', 22, 'Cashier Completed', 'Cashier marked PO 12345 as Completed.', 'transaction_view.php?id=22', 0, '2026-03-04 22:43:19'),
(15, 'accounting', 22, 'Cashier Completed', 'Cashier marked PO 12345 as Completed.', 'transaction_view.php?id=22', 0, '2026-03-04 22:43:19'),
(16, 'budget', 22, 'Cashier Completed', 'Cashier marked PO 12345 as Completed.', 'transaction_view.php?id=22', 1, '2026-03-04 22:43:19'),
(17, 'supply', 23, 'Pending Transaction', 'PO 4567 needs your action.', 'transaction_view.php?id=23', 1, '2026-03-04 22:45:23'),
(18, 'accounting', 23, 'Pending Transaction', 'PO 4567 needs your action.', 'transaction_view.php?id=23', 1, '2026-03-04 22:49:18'),
(19, 'budget', 23, 'Pending Transaction', 'PO 4567 needs your action.', 'transaction_view.php?id=23', 1, '2026-03-04 22:55:33'),
(20, 'accounting', 23, 'Pending Transaction', 'PO 4567 needs your action.', 'transaction_view.php?id=23', 1, '2026-03-04 22:56:31'),
(21, 'cashier', 23, 'Pending Transaction', 'PO 4567 needs your action.', 'transaction_view.php?id=23', 1, '2026-03-04 22:57:00'),
(22, 'procurement', 23, 'Accounting Completed', 'Accounting marked PO 4567 as Completed.', 'transaction_view.php?id=23', 0, '2026-03-04 22:57:00'),
(23, 'supply', 23, 'Accounting Completed', 'Accounting marked PO 4567 as Completed.', 'transaction_view.php?id=23', 0, '2026-03-04 22:57:00'),
(24, 'accounting', 23, 'Accounting Completed', 'Accounting marked PO 4567 as Completed.', 'transaction_view.php?id=23', 0, '2026-03-04 22:57:00'),
(25, 'budget', 23, 'Accounting Completed', 'Accounting marked PO 4567 as Completed.', 'transaction_view.php?id=23', 0, '2026-03-04 22:57:00'),
(26, 'procurement', 23, 'Cashier Completed', 'Cashier marked PO 4567 as Completed.', 'transaction_view.php?id=23', 0, '2026-03-04 22:58:12'),
(27, 'supply', 23, 'Cashier Completed', 'Cashier marked PO 4567 as Completed.', 'transaction_view.php?id=23', 0, '2026-03-04 22:58:12'),
(28, 'accounting', 23, 'Cashier Completed', 'Cashier marked PO 4567 as Completed.', 'transaction_view.php?id=23', 0, '2026-03-04 22:58:12'),
(29, 'budget', 23, 'Cashier Completed', 'Cashier marked PO 4567 as Completed.', 'transaction_view.php?id=23', 0, '2026-03-04 22:58:12'),
(30, 'supply', 19, 'Pending Transaction', 'Upcoming PO asd', 'transaction_view.php?id=19', 1, '2026-03-04 23:09:06'),
(31, 'supply', 13, 'Pending Transaction', 'Upcoming PO asdlklk123', 'transaction_view.php?id=13', 1, '2026-03-04 23:10:25'),
(32, 'accounting', 19, 'Pending Transaction', 'Upcoming PO asd', 'transaction_view.php?id=19', 0, '2026-03-04 23:13:01'),
(33, 'procurement', 19, 'Supply Completed', 'Supply marked PO asd as Completed.', 'transaction_view.php?id=19', 0, '2026-03-04 23:13:01'),
(34, 'accounting', 13, 'Pending Transaction', 'Upcoming PO asdlklk123', 'transaction_view.php?id=13', 1, '2026-03-04 23:13:12'),
(35, 'procurement', 13, 'Supply Completed', 'Supply marked PO asdlklk123 as Completed.', 'transaction_view.php?id=13', 0, '2026-03-04 23:13:12'),
(36, 'supply', 24, 'Pending Transaction', 'Upcoming PO 123', 'transaction_view.php?id=24', 1, '2026-03-05 01:56:05'),
(37, 'accounting', 24, 'Pending Transaction', 'Upcoming PO 123', 'transaction_view.php?id=24', 1, '2026-03-05 01:57:00'),
(38, 'procurement', 24, 'Supply Completed', 'Supply marked PO 123 as Completed.', 'transaction_view.php?id=24', 0, '2026-03-05 01:57:00'),
(39, 'budget', 24, 'Pending Transaction', 'Upcoming PO 123', 'transaction_view.php?id=24', 1, '2026-03-05 01:58:39'),
(40, 'accounting', 24, 'Pending Transaction', 'Upcoming PO 123', 'transaction_view.php?id=24', 1, '2026-03-05 01:59:09'),
(41, 'procurement', 24, 'Budget Completed', 'Budget marked PO 123 as Completed.', 'transaction_view.php?id=24', 0, '2026-03-05 01:59:33'),
(42, 'supply', 24, 'Budget Completed', 'Budget marked PO 123 as Completed.', 'transaction_view.php?id=24', 0, '2026-03-05 01:59:33'),
(43, 'accounting', 24, 'Budget Completed', 'Budget marked PO 123 as Completed.', 'transaction_view.php?id=24', 0, '2026-03-05 01:59:33'),
(44, 'cashier', 24, 'Pending Transaction', 'Upcoming PO 123', 'transaction_view.php?id=24', 1, '2026-03-05 01:59:50'),
(45, 'procurement', 24, 'Accounting Completed', 'Accounting marked PO 123 as Completed.', 'transaction_view.php?id=24', 0, '2026-03-05 01:59:50'),
(46, 'supply', 24, 'Accounting Completed', 'Accounting marked PO 123 as Completed.', 'transaction_view.php?id=24', 0, '2026-03-05 01:59:50'),
(47, 'accounting', 24, 'Accounting Completed', 'Accounting marked PO 123 as Completed.', 'transaction_view.php?id=24', 0, '2026-03-05 01:59:50'),
(48, 'budget', 24, 'Accounting Completed', 'Accounting marked PO 123 as Completed.', 'transaction_view.php?id=24', 0, '2026-03-05 01:59:50'),
(49, 'procurement', 24, 'Cashier Completed', 'Cashier marked PO 123 as Completed.', 'transaction_view.php?id=24', 0, '2026-03-05 02:00:14'),
(50, 'supply', 24, 'Cashier Completed', 'Cashier marked PO 123 as Completed.', 'transaction_view.php?id=24', 0, '2026-03-05 02:00:14'),
(51, 'accounting', 24, 'Cashier Completed', 'Cashier marked PO 123 as Completed.', 'transaction_view.php?id=24', 0, '2026-03-05 02:00:14'),
(52, 'budget', 24, 'Cashier Completed', 'Cashier marked PO 123 as Completed.', 'transaction_view.php?id=24', 0, '2026-03-05 02:00:14'),
(53, 'supply', 25, 'Pending Transaction', 'Upcoming PO 565', 'transaction_view.php?id=25', 1, '2026-03-05 02:56:06'),
(54, 'accounting', 25, 'Pending Transaction', 'Upcoming PO 565', 'transaction_view.php?id=25', 1, '2026-03-05 02:56:26'),
(55, 'procurement', 25, 'Supply Completed', 'Supply marked PO 565 as Completed.', 'transaction_view.php?id=25', 0, '2026-03-05 03:27:34'),
(56, 'budget', 25, 'Pending Transaction', 'Upcoming PO 565', 'transaction_view.php?id=25', 1, '2026-03-05 03:30:09'),
(57, 'accounting', 25, 'Pending Transaction', 'Upcoming PO 565', 'transaction_view.php?id=25', 0, '2026-03-05 03:36:27'),
(58, 'cashier', 25, 'Pending Transaction', 'Upcoming PO 565', 'transaction_view.php?id=25', 1, '2026-03-05 03:37:08'),
(59, 'procurement', 25, 'Accounting Completed', 'Accounting marked PO 565 as Completed.', 'transaction_view.php?id=25', 0, '2026-03-05 03:37:15'),
(60, 'supply', 25, 'Accounting Completed', 'Accounting marked PO 565 as Completed.', 'transaction_view.php?id=25', 0, '2026-03-05 03:37:15'),
(61, 'accounting', 25, 'Accounting Completed', 'Accounting marked PO 565 as Completed.', 'transaction_view.php?id=25', 0, '2026-03-05 03:37:15'),
(62, 'budget', 25, 'Accounting Completed', 'Accounting marked PO 565 as Completed.', 'transaction_view.php?id=25', 0, '2026-03-05 03:37:15'),
(63, 'budget', 19, 'Pending Transaction', 'Upcoming PO asd', 'transaction_view.php?id=19', 1, '2026-03-05 06:35:08'),
(64, 'supply', 26, 'Pending Transaction', 'Upcoming PO 12345678', 'transaction_view.php?id=26', 1, '2026-03-05 13:05:42'),
(65, 'supply', 26, 'Pending Transaction', 'Upcoming PO 12345678', 'transaction_view.php?id=26', 0, '2026-03-05 13:18:38'),
(66, 'procurement', 26, 'Handoff Received', 'Transaction was successfully received for PO 12345678.', 'transaction_view.php?id=26', 0, '2026-03-05 13:19:34'),
(67, 'accounting', 26, 'Pending Transaction', 'Upcoming PO 12345678', 'transaction_view.php?id=26', 0, '2026-03-05 13:21:58'),
(68, 'supply', 26, 'Handoff Received', 'Transaction was successfully received for PO 12345678.', 'transaction_view.php?id=26', 0, '2026-03-05 13:28:43'),
(69, 'accounting', 26, 'Pending Transaction', 'Upcoming PO 12345678', 'transaction_view.php?id=26', 0, '2026-03-05 13:31:04'),
(70, 'supply', 26, 'Handoff Received', 'Transaction was successfully received for PO 12345678.', 'transaction_view.php?id=26', 0, '2026-03-05 13:35:27'),
(71, 'accounting', 26, 'Pending Transaction', 'Upcoming PO 12345678', 'transaction_view.php?id=26', 0, '2026-03-05 13:38:01'),
(72, 'supply', 26, 'Handoff Received', 'Transaction was successfully received for PO 12345678.', 'transaction_view.php?id=26', 0, '2026-03-05 13:39:56'),
(73, 'accounting', 26, 'Pending Transaction', 'Upcoming PO 12345678', 'transaction_view.php?id=26', 0, '2026-03-05 13:41:11'),
(74, 'supply', 26, 'Handoff Received', 'Transaction was successfully received for PO 12345678.', 'transaction_view.php?id=26', 0, '2026-03-05 13:41:58'),
(75, 'accounting', 26, 'Pending Transaction', 'Upcoming PO 12345678', 'transaction_view.php?id=26', 0, '2026-03-05 13:45:22'),
(76, 'supply', 26, 'Handoff Received', 'Transaction was successfully received for PO 12345678.', 'transaction_view.php?id=26', 0, '2026-03-05 13:47:51'),
(77, 'budget', 26, 'Pending Transaction', 'Upcoming PO 12345678', 'transaction_view.php?id=26', 1, '2026-03-05 13:48:16'),
(78, 'accounting', 26, 'Handoff Received', 'Transaction was successfully received for PO 12345678.', 'transaction_view.php?id=26', 0, '2026-03-05 13:48:39'),
(79, 'accounting', 26, 'Handoff Received', 'Transaction was successfully received for PO 12345678.', 'transaction_view.php?id=26', 1, '2026-03-05 13:50:58'),
(80, 'supply', 27, 'Pending Transaction', 'Upcoming PO 1245678', 'transaction_view.php?id=27', 1, '2026-03-05 13:57:15'),
(81, 'procurement', 27, 'Handoff Received', 'Transaction was successfully received for PO 1245678.', 'transaction_view.php?id=27', 0, '2026-03-05 13:58:07'),
(82, 'supply', 27, 'Pending Transaction', 'Upcoming PO 1245678', 'transaction_view.php?id=27', 1, '2026-03-05 14:03:33'),
(83, 'procurement', 27, 'Handoff Received', 'Transaction was successfully received for PO 1245678.', 'transaction_view.php?id=27', 0, '2026-03-05 14:04:05'),
(84, 'accounting', 27, 'Pending Transaction', 'Upcoming PO 1245678', 'transaction_view.php?id=27', 1, '2026-03-05 14:09:45'),
(85, 'supply', 27, 'Handoff Received', 'Transaction was successfully received for PO 1245678.', 'transaction_view.php?id=27', 1, '2026-03-05 14:11:10'),
(86, 'supply', 28, 'Pending Transaction', 'Upcoming PO 123456789', 'transaction_view.php?id=28', 1, '2026-03-05 14:18:02'),
(87, 'procurement', 28, 'Handoff Received', 'Transaction was successfully received for PO 123456789.', 'transaction_view.php?id=28', 0, '2026-03-05 14:18:48'),
(88, 'accounting', 28, 'Pending Transaction', 'Upcoming PO 123456789', 'transaction_view.php?id=28', 0, '2026-03-05 14:21:15'),
(89, 'procurement', 28, 'Supply Completed', 'Supply marked PO 123456789 as Completed.', 'transaction_view.php?id=28', 0, '2026-03-05 14:21:23'),
(90, 'accounting', 28, 'Handoff Forwarded', 'SUPPLY forwarded PO 123456789 to ACCOUNTING_PRE. Please receive it.', 'transaction_view.php?id=28', 1, '2026-03-05 14:21:52'),
(91, 'supply', 28, 'Handoff Received', 'Transaction was successfully received for PO 123456789.', 'transaction_view.php?id=28', 1, '2026-03-05 14:22:48'),
(92, 'budget', 28, 'Pending Transaction', 'Upcoming PO 123456789', 'transaction_view.php?id=28', 1, '2026-03-05 14:26:01'),
(93, 'procurement', 28, 'Accounting Completed', 'Accounting marked PO 123456789 as Completed.', 'transaction_view.php?id=28', 0, '2026-03-05 14:26:22'),
(94, 'supply', 28, 'Accounting Completed', 'Accounting marked PO 123456789 as Completed.', 'transaction_view.php?id=28', 0, '2026-03-05 14:26:22'),
(95, 'procurement', 28, 'Supply Completed', 'Supply marked PO 123456789 as Completed.', 'transaction_view.php?id=28', 0, '2026-03-05 14:32:55'),
(96, 'accounting', 28, 'Pending Transaction', 'Upcoming PO 123456789', 'transaction_view.php?id=28', 0, '2026-03-05 14:35:20'),
(97, 'procurement', 28, 'Budget Completed', 'Budget marked PO 123456789 as Completed.', 'transaction_view.php?id=28', 0, '2026-03-05 14:41:54'),
(98, 'supply', 28, 'Budget Completed', 'Budget marked PO 123456789 as Completed.', 'transaction_view.php?id=28', 0, '2026-03-05 14:41:54'),
(99, 'accounting', 28, 'Budget Completed', 'Budget marked PO 123456789 as Completed.', 'transaction_view.php?id=28', 1, '2026-03-05 14:41:54'),
(100, 'supply', 29, 'Pending Transaction', 'Upcoming PO 1234567890', 'transaction_view.php?id=29', 1, '2026-03-05 14:44:08'),
(101, 'supply', 29, 'Handoff Forwarded', 'PROCUREMENT forwarded PO 1234567890 to SUPPLY. Please receive it.', 'transaction_view.php?id=29', 1, '2026-03-05 14:47:23'),
(102, 'procurement', 29, 'Handoff Received', 'Transaction was successfully received for PO 1234567890.', 'transaction_view.php?id=29', 1, '2026-03-05 14:47:45'),
(103, 'accounting', 29, 'Pending Transaction', 'Upcoming PO 1234567890', 'transaction_view.php?id=29', 0, '2026-03-05 14:51:17'),
(104, 'procurement', 29, 'Supply Completed', 'Supply marked PO 1234567890 as Completed.', 'transaction_view.php?id=29', 1, '2026-03-05 14:51:24'),
(105, 'accounting', 29, 'Supply Completed', 'Supply marked PO 1234567890 as Completed.', 'transaction_view.php?id=29', 0, '2026-03-05 15:02:56'),
(106, 'accounting', 29, 'Handoff Forwarded', 'SUPPLY forwarded PO 1234567890 to ACCOUNTING_PRE. Please receive it.', 'transaction_view.php?id=29', 1, '2026-03-05 15:03:27'),
(107, 'supply', 29, 'Handoff Received', 'Transaction was successfully received for PO 1234567890.', 'transaction_view.php?id=29', 1, '2026-03-05 15:03:39'),
(108, 'budget', 29, 'Pending Transaction', 'Upcoming PO 1234567890', 'transaction_view.php?id=29', 1, '2026-03-05 15:03:59'),
(109, 'budget', 29, 'Accounting Completed', 'Accounting marked PO 1234567890 as Completed.', 'transaction_view.php?id=29', 0, '2026-03-05 15:04:39'),
(110, 'budget', 29, 'Handoff Forwarded', 'ACCOUNTING_PRE forwarded PO 1234567890 to BUDGET. Please receive it.', 'transaction_view.php?id=29', 0, '2026-03-05 15:04:47'),
(111, 'accounting', 29, 'Handoff Received', 'Transaction was successfully received for PO 1234567890.', 'transaction_view.php?id=29', 0, '2026-03-05 15:05:08'),
(112, 'accounting', 29, 'Pending Transaction', 'Upcoming PO 1234567890', 'transaction_view.php?id=29', 0, '2026-03-05 15:05:21'),
(113, 'accounting', 29, 'Budget Completed', 'Budget marked PO 1234567890 as Completed.', 'transaction_view.php?id=29', 0, '2026-03-05 15:06:06'),
(114, 'cashier', 29, 'Pending Transaction', 'Upcoming PO 1234567890', 'transaction_view.php?id=29', 0, '2026-03-05 15:06:46'),
(115, 'cashier', 29, 'Accounting Completed', 'Accounting marked PO 1234567890 as Completed.', 'transaction_view.php?id=29', 0, '2026-03-05 15:06:56'),
(116, 'accounting', 29, 'Handoff Forwarded', 'BUDGET forwarded PO 1234567890 to ACCOUNTING_POST. Please receive it.', 'transaction_view.php?id=29', 0, '2026-03-05 15:07:03'),
(117, 'budget', 29, 'Handoff Received', 'Transaction was successfully received for PO 1234567890.', 'transaction_view.php?id=29', 0, '2026-03-05 15:07:06'),
(118, 'cashier', 29, 'Handoff Forwarded', 'ACCOUNTING_POST forwarded PO 1234567890 to CASHIER. Please receive it.', 'transaction_view.php?id=29', 1, '2026-03-05 15:07:24'),
(119, 'accounting', 29, 'Handoff Received', 'Transaction was successfully received for PO 1234567890.', 'transaction_view.php?id=29', 0, '2026-03-05 15:07:49'),
(120, 'supply', 32, 'Pending Transaction', 'Upcoming PO 123', 'transaction_view.php?id=32', 1, '2026-03-05 15:17:20'),
(121, 'supply', 32, 'Procurement Completed', 'Procurement marked PO 123 as Completed.', 'transaction_view.php?id=32', 1, '2026-03-05 15:17:39'),
(122, 'supply', 32, 'Handoff Forwarded', 'PROCUREMENT forwarded PO 123 to SUPPLY. Please receive it.', 'transaction_view.php?id=32', 0, '2026-03-05 15:36:09'),
(123, 'procurement', 32, 'Handoff Received', 'Transaction was successfully received for PO 123.', 'transaction_view.php?id=32', 0, '2026-03-05 15:36:18'),
(124, 'accounting', 32, 'Pending Transaction', 'Upcoming PO 123', 'transaction_view.php?id=32', 1, '2026-03-05 15:37:11'),
(125, 'budget', 32, 'Pending Transaction', 'Upcoming PO 123', 'transaction_view.php?id=32', 1, '2026-03-05 16:02:44'),
(126, 'accounting', 32, 'Supply Completed', 'Supply marked PO 123 as Completed.', 'transaction_view.php?id=32', 0, '2026-03-05 16:03:06'),
(127, 'accounting', 32, 'Handoff Forwarded', 'SUPPLY forwarded PO 123 to ACCOUNTING_PRE. Please receive it.', 'transaction_view.php?id=32', 0, '2026-03-05 16:03:10'),
(128, 'supply', 32, 'Handoff Received', 'Transaction was successfully received for PO 123.', 'transaction_view.php?id=32', 0, '2026-03-05 16:03:22'),
(129, 'budget', 32, 'Accounting Completed', 'Accounting marked PO 123 as Completed.', 'transaction_view.php?id=32', 0, '2026-03-05 16:03:53'),
(130, 'budget', 32, 'Handoff Forwarded', 'ACCOUNTING_PRE forwarded PO 123 to BUDGET. Please receive it.', 'transaction_view.php?id=32', 0, '2026-03-05 16:03:58'),
(131, 'accounting', 32, 'Handoff Received', 'Transaction was successfully received for PO 123.', 'transaction_view.php?id=32', 0, '2026-03-05 16:04:01'),
(132, 'accounting', 32, 'Pending Transaction', 'Upcoming PO 123', 'transaction_view.php?id=32', 0, '2026-03-05 16:04:07'),
(133, 'accounting', 32, 'Budget Completed', 'Budget marked PO 123 as Completed.', 'transaction_view.php?id=32', 0, '2026-03-05 16:04:07'),
(134, 'accounting', 32, 'Handoff Forwarded', 'BUDGET forwarded PO 123 to ACCOUNTING_POST. Please receive it.', 'transaction_view.php?id=32', 0, '2026-03-05 16:04:22'),
(135, 'budget', 32, 'Handoff Received', 'Transaction was successfully received for PO 123.', 'transaction_view.php?id=32', 0, '2026-03-05 16:04:38'),
(136, 'cashier', 32, 'Pending Transaction', 'Upcoming PO 123', 'transaction_view.php?id=32', 0, '2026-03-05 16:04:41'),
(137, 'cashier', 32, 'Accounting Completed', 'Accounting marked PO 123 as Completed.', 'transaction_view.php?id=32', 1, '2026-03-05 16:04:41'),
(138, 'cashier', 32, 'Handoff Forwarded', 'ACCOUNTING_POST forwarded PO 123 to CASHIER. Please receive it.', 'transaction_view.php?id=32', 0, '2026-03-05 16:05:02'),
(139, 'supply', 33, 'Pending Transaction', 'Upcoming PO 12345678', 'transaction_view.php?id=33', 0, '2026-03-10 22:40:02'),
(140, 'supply', 33, 'Procurement Completed', 'Procurement marked PO 12345678 as Completed.', 'transaction_view.php?id=33', 0, '2026-03-10 22:40:24'),
(141, 'supply', 33, 'Handoff Forwarded', 'PROCUREMENT forwarded PO 12345678 to SUPPLY. Please receive it.', 'transaction_view.php?id=33', 0, '2026-03-10 22:40:49'),
(142, 'procurement', 33, 'Handoff Received', 'Transaction was successfully received for PO 12345678.', 'transaction_view.php?id=33', 0, '2026-03-10 22:40:53'),
(143, 'accounting', 33, 'Pending Transaction', 'Upcoming PO 12345678', 'transaction_view.php?id=33', 0, '2026-03-10 22:41:01'),
(144, 'accounting', 33, 'Supply Completed', 'Supply marked PO 12345678 as Completed.', 'transaction_view.php?id=33', 0, '2026-03-10 22:41:01'),
(145, 'accounting', 33, 'Handoff Forwarded', 'SUPPLY forwarded PO 12345678 to ACCOUNTING_PRE. Please receive it.', 'transaction_view.php?id=33', 0, '2026-03-10 22:41:05'),
(146, 'supply', 33, 'Handoff Received', 'Transaction was successfully received for PO 12345678.', 'transaction_view.php?id=33', 0, '2026-03-10 22:41:19'),
(147, 'budget', 33, 'Pending Transaction', 'Upcoming PO 12345678', 'transaction_view.php?id=33', 0, '2026-03-10 22:41:27'),
(148, 'budget', 33, 'Accounting Completed', 'Accounting marked PO 12345678 as Completed.', 'transaction_view.php?id=33', 0, '2026-03-10 22:41:27'),
(149, 'budget', 33, 'Handoff Forwarded', 'ACCOUNTING_PRE forwarded PO 12345678 to BUDGET. Please receive it.', 'transaction_view.php?id=33', 0, '2026-03-10 22:41:34'),
(150, 'accounting', 33, 'Handoff Received', 'Transaction was successfully received for PO 12345678.', 'transaction_view.php?id=33', 0, '2026-03-10 22:41:56'),
(151, 'accounting', 33, 'Budget Completed', 'Budget marked PO 12345678 as Completed.', 'transaction_view.php?id=33', 0, '2026-03-10 22:42:02'),
(152, 'accounting', 33, 'Handoff Forwarded', 'BUDGET forwarded PO 12345678 to ACCOUNTING_POST. Please receive it.', 'transaction_view.php?id=33', 0, '2026-03-10 22:42:05'),
(153, 'budget', 33, 'Handoff Received', 'Transaction was successfully received for PO 12345678.', 'transaction_view.php?id=33', 0, '2026-03-10 22:42:12'),
(154, 'cashier', 33, 'Pending Transaction', 'Upcoming PO 12345678', 'transaction_view.php?id=33', 0, '2026-03-10 22:42:38'),
(155, 'cashier', 33, 'Accounting Completed', 'Accounting marked PO 12345678 as Completed.', 'transaction_view.php?id=33', 0, '2026-03-10 22:42:38'),
(156, 'cashier', 33, 'Handoff Forwarded', 'ACCOUNTING_POST forwarded PO 12345678 to CASHIER. Please receive it.', 'transaction_view.php?id=33', 0, '2026-03-10 22:42:42'),
(157, 'supply', 34, 'Pending Transaction', 'Upcoming PO 1234567890', 'transaction_view.php?id=34', 0, '2026-03-11 09:10:54'),
(158, 'supply', 34, 'Procurement Completed', 'Procurement marked PO 1234567890 as Completed.', 'transaction_view.php?id=34', 0, '2026-03-11 09:10:54'),
(159, 'supply', 34, 'Handoff Forwarded', 'PROCUREMENT forwarded PO 1234567890 to SUPPLY. Please receive it.', 'transaction_view.php?id=34', 0, '2026-03-11 09:11:21'),
(160, 'procurement', 34, 'Handoff Received', 'Transaction was successfully received for PO 1234567890.', 'transaction_view.php?id=34', 0, '2026-03-11 09:12:02'),
(161, 'accounting', 34, 'Pending Transaction', 'Upcoming PO 1234567890', 'transaction_view.php?id=34', 0, '2026-03-11 09:12:18'),
(162, 'accounting', 34, 'Supply Completed', 'Supply marked PO 1234567890 as Completed.', 'transaction_view.php?id=34', 0, '2026-03-11 09:12:18'),
(163, 'accounting', 34, 'Handoff Forwarded', 'SUPPLY forwarded PO 1234567890 to ACCOUNTING_PRE. Please receive it.', 'transaction_view.php?id=34', 0, '2026-03-11 09:12:50'),
(164, 'supply', 34, 'Handoff Received', 'Transaction was successfully received for PO 1234567890.', 'transaction_view.php?id=34', 0, '2026-03-11 09:14:12'),
(165, 'budget', 34, 'Pending Transaction', 'Upcoming PO 1234567890', 'transaction_view.php?id=34', 0, '2026-03-11 09:14:23'),
(166, 'budget', 34, 'Accounting Completed', 'Accounting marked PO 1234567890 as Completed.', 'transaction_view.php?id=34', 0, '2026-03-11 09:14:23'),
(167, 'budget', 34, 'Handoff Forwarded', 'ACCOUNTING_PRE forwarded PO 1234567890 to BUDGET. Please receive it.', 'transaction_view.php?id=34', 0, '2026-03-11 09:14:28'),
(168, 'accounting', 34, 'Handoff Received', 'Transaction was successfully received for PO 1234567890.', 'transaction_view.php?id=34', 0, '2026-03-11 09:16:39'),
(169, 'accounting', 34, 'Pending Transaction', 'Upcoming PO 1234567890', 'transaction_view.php?id=34', 0, '2026-03-11 09:16:59'),
(170, 'accounting', 34, 'Budget Completed', 'Budget marked PO 1234567890 as Completed.', 'transaction_view.php?id=34', 0, '2026-03-11 09:16:59'),
(171, 'accounting', 34, 'Handoff Forwarded', 'BUDGET forwarded PO 1234567890 to ACCOUNTING_POST. Please receive it.', 'transaction_view.php?id=34', 0, '2026-03-11 09:17:13'),
(172, 'budget', 34, 'Handoff Received', 'Transaction was successfully received for PO 1234567890.', 'transaction_view.php?id=34', 0, '2026-03-11 09:17:24'),
(173, 'cashier', 34, 'Pending Transaction', 'Upcoming PO 1234567890', 'transaction_view.php?id=34', 0, '2026-03-11 09:20:10'),
(174, 'cashier', 34, 'Accounting Completed', 'Accounting marked PO 1234567890 as Completed.', 'transaction_view.php?id=34', 0, '2026-03-11 09:20:10'),
(175, 'cashier', 34, 'Handoff Forwarded', 'ACCOUNTING_POST forwarded PO 1234567890 to CASHIER. Please receive it.', 'transaction_view.php?id=34', 0, '2026-03-11 09:20:28'),
(176, 'accounting', 34, 'Handoff Received', 'Transaction was successfully received for PO 1234567890.', 'transaction_view.php?id=34', 0, '2026-03-11 09:20:33'),
(177, 'supply', 35, 'Pending Transaction', 'Upcoming PO 456789098765', 'transaction_view.php?id=35', 0, '2026-03-11 09:33:01'),
(178, 'supply', 35, 'Procurement Completed', 'Procurement marked PO 456789098765 as Completed.', 'transaction_view.php?id=35', 0, '2026-03-11 09:33:01'),
(179, 'supply', 35, 'Handoff Forwarded', 'PROCUREMENT forwarded PO 456789098765 to SUPPLY. Please receive it.', 'transaction_view.php?id=35', 0, '2026-03-11 09:33:07'),
(180, 'procurement', 35, 'Handoff Received', 'Transaction was successfully received for PO 456789098765.', 'transaction_view.php?id=35', 0, '2026-03-11 09:35:29'),
(181, 'accounting', 35, 'Pending Transaction', 'Upcoming PO 456789098765', 'transaction_view.php?id=35', 0, '2026-03-11 09:35:47'),
(182, 'accounting', 35, 'Supply Completed', 'Supply marked PO 456789098765 as Completed.', 'transaction_view.php?id=35', 0, '2026-03-11 09:35:47'),
(183, 'accounting', 35, 'Handoff Forwarded', 'SUPPLY forwarded PO 456789098765 to ACCOUNTING_PRE. Please receive it.', 'transaction_view.php?id=35', 0, '2026-03-11 09:37:13'),
(184, 'supply', 35, 'Handoff Received', 'Transaction was successfully received for PO 456789098765.', 'transaction_view.php?id=35', 0, '2026-03-11 09:37:36'),
(185, 'budget', 35, 'Pending Transaction', 'Upcoming PO 456789098765', 'transaction_view.php?id=35', 0, '2026-03-11 09:37:43'),
(186, 'budget', 35, 'Accounting Completed', 'Accounting marked PO 456789098765 as Completed.', 'transaction_view.php?id=35', 0, '2026-03-11 09:37:43'),
(187, 'budget', 35, 'Handoff Forwarded', 'ACCOUNTING_PRE forwarded PO 456789098765 to BUDGET. Please receive it.', 'transaction_view.php?id=35', 0, '2026-03-11 09:37:49'),
(188, 'accounting', 35, 'Handoff Received', 'Transaction was successfully received for PO 456789098765.', 'transaction_view.php?id=35', 0, '2026-03-11 09:39:30'),
(189, 'accounting', 35, 'Pending Transaction', 'Upcoming PO 456789098765', 'transaction_view.php?id=35', 0, '2026-03-11 09:40:04'),
(190, 'accounting', 35, 'Budget Completed', 'Budget marked PO 456789098765 as Completed.', 'transaction_view.php?id=35', 0, '2026-03-11 09:40:04'),
(191, 'accounting', 35, 'Handoff Forwarded', 'BUDGET forwarded PO 456789098765 to ACCOUNTING_POST. Please receive it.', 'transaction_view.php?id=35', 0, '2026-03-11 09:40:11'),
(192, 'budget', 35, 'Handoff Received', 'Transaction was successfully received for PO 456789098765.', 'transaction_view.php?id=35', 0, '2026-03-11 09:41:13'),
(193, 'cashier', 35, 'Pending Transaction', 'Upcoming PO 456789098765', 'transaction_view.php?id=35', 0, '2026-03-11 09:41:29'),
(194, 'cashier', 35, 'Accounting Completed', 'Accounting marked PO 456789098765 as Completed.', 'transaction_view.php?id=35', 0, '2026-03-11 09:41:29'),
(195, 'cashier', 35, 'Handoff Forwarded', 'ACCOUNTING_POST forwarded PO 456789098765 to CASHIER. Please receive it.', 'transaction_view.php?id=35', 0, '2026-03-11 09:42:29'),
(196, 'accounting', 35, 'Handoff Received', 'Transaction was successfully received for PO 456789098765.', 'transaction_view.php?id=35', 0, '2026-03-11 09:42:50'),
(197, 'supply', 36, 'Pending Transaction', 'Upcoming PO 123456789', 'transaction_view.php?id=36', 0, '2026-03-11 09:52:01'),
(198, 'supply', 36, 'Procurement Completed', 'Procurement marked PO 123456789 as Completed.', 'transaction_view.php?id=36', 0, '2026-03-11 09:52:01'),
(199, 'supply', 36, 'Handoff Forwarded', 'PROCUREMENT forwarded PO 123456789 to SUPPLY. Please receive it.', 'transaction_view.php?id=36', 0, '2026-03-11 09:54:01'),
(200, 'procurement', 36, 'Handoff Received', 'Transaction was successfully received for PO 123456789.', 'transaction_view.php?id=36', 0, '2026-03-11 09:54:57'),
(201, 'accounting', 36, 'Pending Transaction', 'Upcoming PO 123456789', 'transaction_view.php?id=36', 0, '2026-03-11 09:58:00'),
(202, 'accounting', 36, 'Supply Completed', 'Supply marked PO 123456789 as Completed.', 'transaction_view.php?id=36', 0, '2026-03-11 09:58:00'),
(203, 'supply', 37, 'Pending Transaction', 'Upcoming PO 1234567898765432', 'transaction_view.php?id=37', 0, '2026-03-11 10:56:01'),
(204, 'supply', 37, 'Procurement Completed', 'Procurement marked PO 1234567898765432 as Completed.', 'transaction_view.php?id=37', 0, '2026-03-11 10:56:01'),
(205, 'supply', 37, 'Handoff Forwarded', 'PROCUREMENT forwarded PO 1234567898765432 to SUPPLY. Please receive it.', 'transaction_view.php?id=37', 0, '2026-03-11 10:57:01'),
(206, 'procurement', 37, 'Handoff Received', 'Transaction was successfully received for PO 1234567898765432.', 'transaction_view.php?id=37', 0, '2026-03-11 10:57:42'),
(207, 'accounting', 37, 'Pending Transaction', 'Upcoming PO 1234567898765432', 'transaction_view.php?id=37', 0, '2026-03-11 11:00:01'),
(208, 'accounting', 37, 'Supply Completed', 'Supply marked PO 1234567898765432 as Completed.', 'transaction_view.php?id=37', 0, '2026-03-11 11:00:01'),
(209, 'accounting', 37, 'Handoff Forwarded', 'SUPPLY forwarded PO 1234567898765432 to ACCOUNTING_PRE. Please receive it.', 'transaction_view.php?id=37', 0, '2026-03-11 11:02:01'),
(210, 'supply', 37, 'Handoff Received', 'Transaction was successfully received for PO 1234567898765432.', 'transaction_view.php?id=37', 0, '2026-03-11 11:04:02'),
(211, 'budget', 37, 'Pending Transaction', 'Upcoming PO 1234567898765432', 'transaction_view.php?id=37', 0, '2026-03-11 11:05:01'),
(212, 'budget', 37, 'Accounting Completed', 'Accounting marked PO 1234567898765432 as Completed.', 'transaction_view.php?id=37', 0, '2026-03-11 11:05:01'),
(213, 'budget', 37, 'Handoff Forwarded', 'ACCOUNTING_PRE forwarded PO 1234567898765432 to BUDGET. Please receive it.', 'transaction_view.php?id=37', 0, '2026-03-11 11:09:01'),
(214, 'accounting', 37, 'Handoff Received', 'Transaction was successfully received for PO 1234567898765432.', 'transaction_view.php?id=37', 0, '2026-03-11 11:11:02'),
(215, 'accounting', 37, 'Pending Transaction', 'Upcoming PO 1234567898765432', 'transaction_view.php?id=37', 0, '2026-03-11 11:12:01'),
(216, 'accounting', 37, 'Budget Completed', 'Budget marked PO 1234567898765432 as Completed.', 'transaction_view.php?id=37', 0, '2026-03-11 11:12:01'),
(217, 'accounting', 37, 'Handoff Forwarded', 'BUDGET forwarded PO 1234567898765432 to ACCOUNTING_POST. Please receive it.', 'transaction_view.php?id=37', 0, '2026-03-11 13:19:30'),
(218, 'budget', 37, 'Handoff Received', 'Transaction was successfully received for PO 1234567898765432.', 'transaction_view.php?id=37', 0, '2026-03-11 13:21:25'),
(219, 'cashier', 37, 'Pending Transaction', 'Upcoming PO 1234567898765432', 'transaction_view.php?id=37', 1, '2026-03-11 13:22:42'),
(220, 'cashier', 37, 'Accounting Completed', 'Accounting marked PO 1234567898765432 as Completed.', 'transaction_view.php?id=37', 1, '2026-03-11 13:22:42'),
(221, 'cashier', 37, 'Handoff Forwarded', 'ACCOUNTING_POST forwarded PO 1234567898765432 to CASHIER. Please receive it.', 'transaction_view.php?id=37', 1, '2026-03-11 13:22:53'),
(222, 'accounting', 37, 'Handoff Received', 'Transaction was successfully received for PO 1234567898765432.', 'transaction_view.php?id=37', 0, '2026-03-11 13:24:25'),
(223, 'supply', 38, 'Pending Transaction', 'Upcoming PO 12345678765432', 'transaction_view.php?id=38', 0, '2026-03-11 13:51:01'),
(224, 'supply', 38, 'Procurement Completed', 'Procurement marked PO 12345678765432 as Completed.', 'transaction_view.php?id=38', 0, '2026-03-11 13:52:00'),
(225, 'supply', 38, 'Handoff Forwarded', 'PROCUREMENT forwarded PO 12345678765432 to SUPPLY. Please receive it.', 'transaction_view.php?id=38', 0, '2026-03-11 13:53:01'),
(226, 'procurement', 38, 'Handoff Received', 'Transaction was successfully received for PO 12345678765432.', 'transaction_view.php?id=38', 0, '2026-03-11 13:54:02'),
(227, 'accounting', 38, 'Pending Transaction', 'Upcoming PO 12345678765432', 'transaction_view.php?id=38', 0, '2026-03-11 13:56:01'),
(228, 'accounting', 38, 'Supply Completed', 'Supply marked PO 12345678765432 as Completed.', 'transaction_view.php?id=38', 0, '2026-03-11 13:56:01'),
(229, 'accounting', 38, 'Handoff Forwarded', 'SUPPLY forwarded PO 12345678765432 to ACCOUNTING_PRE. Please receive it.', 'transaction_view.php?id=38', 0, '2026-03-11 13:58:01'),
(230, 'supply', 38, 'Handoff Received', 'Transaction was successfully received for PO 12345678765432.', 'transaction_view.php?id=38', 0, '2026-03-11 14:00:02'),
(231, 'budget', 38, 'Pending Transaction', 'Upcoming PO 12345678765432', 'transaction_view.php?id=38', 0, '2026-03-11 14:03:01'),
(232, 'budget', 38, 'Accounting Completed', 'Accounting marked PO 12345678765432 as Completed.', 'transaction_view.php?id=38', 0, '2026-03-11 14:03:01'),
(233, 'budget', 38, 'Handoff Forwarded', 'ACCOUNTING_PRE forwarded PO 12345678765432 to BUDGET. Please receive it.', 'transaction_view.php?id=38', 0, '2026-03-11 14:05:02'),
(234, 'accounting', 38, 'Handoff Received', 'Transaction was successfully received for PO 12345678765432.', 'transaction_view.php?id=38', 0, '2026-03-11 14:09:03'),
(235, 'accounting', 38, 'Pending Transaction', 'Upcoming PO 12345678765432', 'transaction_view.php?id=38', 0, '2026-03-11 14:10:01'),
(236, 'accounting', 38, 'Budget Completed', 'Budget marked PO 12345678765432 as Completed.', 'transaction_view.php?id=38', 0, '2026-03-11 14:10:01'),
(237, 'supply', 39, 'Pending Transaction', 'Upcoming PO 23456763', 'transaction_view.php?id=39', 0, '2026-03-11 14:32:28'),
(238, 'supply', 39, 'Procurement Completed', 'Procurement marked PO 23456763 as Completed.', 'transaction_view.php?id=39', 0, '2026-03-11 14:32:28'),
(239, 'supply', 39, 'Handoff Forwarded', 'PROCUREMENT forwarded PO 23456763 to SUPPLY. Please receive it.', 'transaction_view.php?id=39', 0, '2026-03-11 14:32:30'),
(240, 'procurement', 39, 'Handoff Received', 'Transaction was successfully received for PO 23456763.', 'transaction_view.php?id=39', 0, '2026-03-11 14:34:36'),
(241, 'accounting', 39, 'Pending Transaction', 'Upcoming PO 23456763', 'transaction_view.php?id=39', 0, '2026-03-11 14:34:52'),
(242, 'accounting', 39, 'Supply Completed', 'Supply marked PO 23456763 as Completed.', 'transaction_view.php?id=39', 1, '2026-03-11 14:34:52'),
(243, 'accounting', 39, 'Handoff Forwarded', 'SUPPLY forwarded PO 23456763 to ACCOUNTING_PRE. Please receive it.', 'transaction_view.php?id=39', 0, '2026-03-11 14:36:24'),
(244, 'supply', 39, 'Handoff Received', 'Transaction was successfully received for PO 23456763.', 'transaction_view.php?id=39', 0, '2026-03-11 14:37:00'),
(245, 'supply', 40, 'Pending Transaction', 'Upcoming PO 20343409494', 'transaction_view.php?id=40', 0, '2026-03-12 09:46:29'),
(246, 'supply', 40, 'Procurement Completed', 'Procurement marked PO 20343409494 as Completed.', 'transaction_view.php?id=40', 0, '2026-03-12 09:46:29'),
(247, 'supply', 40, 'Handoff Forwarded', 'PROCUREMENT forwarded PO 20343409494 to SUPPLY. Please receive it.', 'transaction_view.php?id=40', 1, '2026-03-12 09:49:25'),
(248, 'procurement', 40, 'Handoff Received', 'Transaction was successfully received for PO 20343409494.', 'transaction_view.php?id=40', 0, '2026-03-12 09:53:15'),
(249, 'supply', 41, 'Pending Transaction', 'Upcoming PO 1235776543', 'transaction_view.php?id=41', 1, '2026-03-12 15:01:06'),
(250, 'supply', 41, 'Procurement Completed', 'Procurement marked PO 1235776543 as Completed.', 'transaction_view.php?id=41', 0, '2026-03-12 15:01:06'),
(251, 'supply', 41, 'Handoff Forwarded', 'PROCUREMENT forwarded PO 1235776543 to SUPPLY. Please receive it.', 'transaction_view.php?id=41', 0, '2026-03-12 15:02:58'),
(252, 'procurement', 41, 'Handoff Received', 'Transaction was successfully received for PO 1235776543.', 'transaction_view.php?id=41', 1, '2026-03-12 15:03:06'),
(253, 'accounting', 41, 'Pending Transaction', 'Upcoming PO 1235776543', 'transaction_view.php?id=41', 0, '2026-03-12 15:07:00'),
(254, 'accounting', 41, 'Supply Completed', 'Supply marked PO 1235776543 as Completed.', 'transaction_view.php?id=41', 0, '2026-03-12 15:07:00'),
(255, 'accounting', 41, 'Handoff Forwarded', 'SUPPLY forwarded PO 1235776543 to ACCOUNTING_PRE. Please receive it.', 'transaction_view.php?id=41', 0, '2026-03-12 15:12:57'),
(256, 'supply', 41, 'Handoff Received', 'Transaction was successfully received for PO 1235776543.', 'transaction_view.php?id=41', 1, '2026-03-12 15:13:04'),
(257, 'budget', 41, 'Pending Transaction', 'Upcoming PO 1235776543', 'transaction_view.php?id=41', 0, '2026-03-12 15:13:32'),
(258, 'budget', 41, 'Accounting Completed', 'Accounting marked PO 1235776543 as Completed.', 'transaction_view.php?id=41', 0, '2026-03-12 15:13:32'),
(259, 'budget', 41, 'Handoff Forwarded', 'ACCOUNTING_PRE forwarded PO 1235776543 to BUDGET. Please receive it.', 'transaction_view.php?id=41', 1, '2026-03-12 15:13:50'),
(260, 'accounting', 41, 'Handoff Received', 'Transaction was successfully received for PO 1235776543.', 'transaction_view.php?id=41', 0, '2026-03-12 15:13:54'),
(261, 'accounting', 41, 'Pending Transaction', 'Upcoming PO 1235776543', 'transaction_view.php?id=41', 1, '2026-03-13 10:11:23'),
(262, 'supply', 42, 'Pending Transaction', 'Upcoming PO 1234567654', 'transaction_view.php?id=42', 1, '2026-03-13 12:01:35'),
(263, 'supply', 42, 'Procurement Completed', 'Procurement marked PO 1234567654 as Completed.', 'transaction_view.php?id=42', 0, '2026-03-13 12:01:54'),
(264, 'supply', 42, 'Handoff Forwarded', 'PROCUREMENT forwarded PO 1234567654 to SUPPLY. Please receive it.', 'transaction_view.php?id=42', 1, '2026-03-13 12:01:58'),
(265, 'procurement', 42, 'Handoff Received', 'Transaction was successfully received for PO 1234567654.', 'transaction_view.php?id=42', 0, '2026-03-13 12:02:09'),
(266, 'accounting', 42, 'Pending Transaction', 'Upcoming PO 1234567654', 'transaction_view.php?id=42', 0, '2026-03-13 12:02:15'),
(267, 'accounting', 42, 'Supply Completed', 'Supply marked PO 1234567654 as Completed.', 'transaction_view.php?id=42', 0, '2026-03-13 13:06:31'),
(268, 'supply', 43, 'Pending Transaction', 'Upcoming PO 65678', 'transaction_view.php?id=43', 0, '2026-03-13 13:21:09'),
(269, 'supply', 43, 'Procurement Completed', 'Procurement marked PO 65678 as Completed.', 'transaction_view.php?id=43', 1, '2026-03-13 13:21:52'),
(270, 'supply', 43, 'Procurement Completed', 'Procurement marked PO 65678 as Completed.', 'transaction_view.php?id=43', 0, '2026-03-13 13:24:57'),
(271, 'supply', 43, 'Procurement Completed', 'Procurement marked PO 65678 as Completed.', 'transaction_view.php?id=43', 0, '2026-03-13 13:28:10'),
(272, 'supply', 43, 'Handoff Forwarded', 'PROCUREMENT forwarded PO 65678 to SUPPLY. Please receive it.', 'transaction_view.php?id=43', 0, '2026-03-13 13:28:28'),
(273, 'procurement', 43, 'Handoff Received', 'Transaction was successfully received for PO 65678.', 'transaction_view.php?id=43', 1, '2026-03-13 13:30:18'),
(274, 'accounting', 43, 'Pending Transaction', 'Upcoming PO 65678', 'transaction_view.php?id=43', 0, '2026-03-13 13:31:47'),
(275, 'accounting', 43, 'Supply Completed', 'Supply marked PO 65678 as Completed.', 'transaction_view.php?id=43', 0, '2026-03-14 13:26:16'),
(276, 'accounting', 43, 'Supply Completed', 'Supply marked PO 65678 as Completed.', 'transaction_view.php?id=43', 0, '2026-03-14 13:34:57'),
(277, 'accounting', 43, 'Handoff Forwarded', 'SUPPLY forwarded PO 65678 to ACCOUNTING_PRE. Please receive it.', 'transaction_view.php?id=43', 0, '2026-03-14 13:35:03'),
(278, 'supply', 44, 'Pending Transaction', 'Upcoming PO 876543456', 'transaction_view.php?id=44', 0, '2026-03-14 19:41:03'),
(279, 'supply', 44, 'Procurement Completed', 'Procurement marked PO 876543456 as Completed.', 'transaction_view.php?id=44', 1, '2026-03-14 19:43:13'),
(280, 'supply', 44, 'Handoff Forwarded', 'PROCUREMENT forwarded PO 876543456 to SUPPLY. Please receive it.', 'transaction_view.php?id=44', 0, '2026-03-14 19:44:30'),
(281, 'procurement', 44, 'Handoff Received', 'Transaction was successfully received for PO 876543456.', 'transaction_view.php?id=44', 0, '2026-03-14 19:44:38'),
(282, 'accounting', 44, 'Pending Transaction', 'Upcoming PO 876543456', 'transaction_view.php?id=44', 0, '2026-03-14 19:44:59'),
(283, 'accounting', 44, 'Supply Completed', 'Supply marked PO 876543456 as Completed.', 'transaction_view.php?id=44', 0, '2026-03-14 19:48:36'),
(284, 'accounting', 44, 'Handoff Forwarded', 'SUPPLY forwarded PO 876543456 to ACCOUNTING_PRE. Please receive it.', 'transaction_view.php?id=44', 0, '2026-03-14 19:48:59'),
(285, 'supply', 44, 'Handoff Received', 'Transaction was successfully received for PO 876543456.', 'transaction_view.php?id=44', 0, '2026-03-14 19:49:10'),
(286, 'supply', 45, 'Pending Transaction', 'Upcoming PO 0987678', 'transaction_view.php?id=45', 0, '2026-03-15 13:01:50'),
(287, 'supply', 45, 'Procurement Completed', 'Procurement marked PO 0987678 as Completed.', 'transaction_view.php?id=45', 0, '2026-03-15 13:02:03'),
(288, 'supply', 45, 'Handoff Forwarded', 'PROCUREMENT forwarded PO 0987678 to SUPPLY. Please receive it.', 'transaction_view.php?id=45', 0, '2026-03-15 13:02:06'),
(289, 'procurement', 45, 'Handoff Received', 'Transaction was successfully received for PO 0987678.', 'transaction_view.php?id=45', 0, '2026-03-15 13:02:17'),
(290, 'accounting', 45, 'Pending Transaction', 'Upcoming PO 0987678', 'transaction_view.php?id=45', 0, '2026-03-15 13:02:36'),
(291, 'accounting', 45, 'Supply Completed', 'Supply marked PO 0987678 as Completed.', 'transaction_view.php?id=45', 0, '2026-03-15 13:36:34'),
(292, 'accounting', 45, 'Handoff Forwarded', 'SUPPLY forwarded PO 0987678 to ACCOUNTING_PRE. Please receive it.', 'transaction_view.php?id=45', 0, '2026-03-15 13:37:15'),
(293, 'supply', 45, 'Handoff Received', 'Transaction was successfully received for PO 0987678.', 'transaction_view.php?id=45', 0, '2026-03-15 13:37:21'),
(294, 'budget', 45, 'Pending Transaction', 'Upcoming PO 0987678', 'transaction_view.php?id=45', 0, '2026-03-15 13:44:43'),
(295, 'budget', 45, 'Accounting Completed', 'Accounting marked PO 0987678 as Completed.', 'transaction_view.php?id=45', 0, '2026-03-15 13:44:43'),
(296, 'budget', 45, 'Handoff Forwarded', 'ACCOUNTING_PRE forwarded PO 0987678 to BUDGET. Please receive it.', 'transaction_view.php?id=45', 1, '2026-03-15 13:47:22'),
(297, 'accounting', 45, 'Handoff Received', 'Transaction was successfully received for PO 0987678.', 'transaction_view.php?id=45', 0, '2026-03-15 13:48:42'),
(298, 'accounting', 45, 'Pending Transaction', 'Upcoming PO 0987678', 'transaction_view.php?id=45', 0, '2026-03-15 13:49:21'),
(299, 'accounting', 45, 'Budget Completed', 'Budget marked PO 0987678 as Completed.', 'transaction_view.php?id=45', 0, '2026-03-15 13:50:08'),
(300, 'accounting', 45, 'Handoff Forwarded', 'BUDGET forwarded PO 0987678 to ACCOUNTING_POST. Please receive it.', 'transaction_view.php?id=45', 0, '2026-03-15 13:51:01'),
(301, 'budget', 45, 'Handoff Received', 'Transaction was successfully received for PO 0987678.', 'transaction_view.php?id=45', 0, '2026-03-15 13:51:25'),
(302, 'cashier', 45, 'Pending Transaction', 'Upcoming PO 0987678', 'transaction_view.php?id=45', 0, '2026-03-15 13:52:28'),
(303, 'cashier', 45, 'Accounting Completed', 'Accounting marked PO 0987678 as Completed.', 'transaction_view.php?id=45', 1, '2026-03-15 13:52:28'),
(304, 'cashier', 45, 'Handoff Forwarded', 'ACCOUNTING_POST forwarded PO 0987678 to CASHIER. Please receive it.', 'transaction_view.php?id=45', 0, '2026-03-15 13:52:47'),
(305, 'accounting', 45, 'Handoff Received', 'Transaction was successfully received for PO 0987678.', 'transaction_view.php?id=45', 0, '2026-03-15 13:53:14'),
(306, 'budget', 44, 'Pending Transaction', 'Upcoming PO 876543456', 'transaction_view.php?id=44', 0, '2026-03-15 15:32:38'),
(307, 'budget', 44, 'Accounting Completed', 'Accounting marked PO 876543456 as Completed.', 'transaction_view.php?id=44', 0, '2026-03-15 15:32:38'),
(308, 'budget', 44, 'Handoff Forwarded', 'ACCOUNTING_PRE forwarded PO 876543456 to BUDGET. Please receive it.', 'transaction_view.php?id=44', 0, '2026-03-15 15:32:51'),
(309, 'accounting', 44, 'Handoff Received', 'Transaction was successfully received for PO 876543456.', 'transaction_view.php?id=44', 0, '2026-03-15 15:46:51'),
(310, 'accounting', 44, 'Pending Transaction', 'Upcoming PO 876543456', 'transaction_view.php?id=44', 0, '2026-03-15 15:49:28'),
(311, 'accounting', 44, 'Budget Completed', 'Budget marked PO 876543456 as Completed.', 'transaction_view.php?id=44', 0, '2026-03-15 15:49:28'),
(312, 'accounting', 44, 'Handoff Forwarded', 'BUDGET forwarded PO 876543456 to ACCOUNTING_POST. Please receive it.', 'transaction_view.php?id=44', 0, '2026-03-15 15:49:31'),
(313, 'budget', 44, 'Handoff Received', 'Transaction was successfully received for PO 876543456.', 'transaction_view.php?id=44', 0, '2026-03-15 16:21:05'),
(314, 'cashier', 44, 'Pending Transaction', 'Upcoming PO 876543456', 'transaction_view.php?id=44', 0, '2026-03-15 16:22:15'),
(315, 'cashier', 44, 'Accounting Completed', 'Accounting marked PO 876543456 as Completed.', 'transaction_view.php?id=44', 0, '2026-03-15 16:22:15'),
(316, 'cashier', 44, 'Handoff Forwarded', 'ACCOUNTING_POST forwarded PO 876543456 to CASHIER. Please receive it.', 'transaction_view.php?id=44', 1, '2026-03-15 16:22:31'),
(317, 'accounting', 44, 'Handoff Received', 'Transaction was successfully received for PO 876543456.', 'transaction_view.php?id=44', 0, '2026-03-15 16:24:59'),
(318, 'supply', 43, 'Handoff Received', 'Transaction was successfully received for PO 65678.', 'transaction_view.php?id=43', 0, '2026-03-16 05:32:40'),
(319, 'supply', 43, 'Handoff Delay', 'Handoff delay exceeded grace period (1978 min) for PO 65678.', 'transaction_view.php?id=43', 0, '2026-03-16 05:32:40'),
(320, 'accounting', 43, 'Handoff Delay', 'Handoff delay exceeded grace period (1978 min) for PO 65678.', 'transaction_view.php?id=43', 0, '2026-03-16 05:32:40'),
(321, 'supply', 47, 'Pending Transaction', 'Upcoming PO 0987654894', 'transaction_view.php?id=47', 0, '2026-03-16 11:02:38'),
(322, 'supply', 47, 'Procurement Completed', 'Procurement marked PO 0987654894 as Completed.', 'transaction_view.php?id=47', 0, '2026-03-16 11:02:47'),
(323, 'supply', 47, 'Handoff Forwarded', 'PROCUREMENT forwarded PO 0987654894 to SUPPLY. Please receive it.', 'transaction_view.php?id=47', 0, '2026-03-16 11:05:58'),
(324, 'procurement', 47, 'Handoff Received', 'Transaction was successfully received for PO 0987654894.', 'transaction_view.php?id=47', 0, '2026-03-16 11:06:33'),
(325, 'accounting', 47, 'Pending Transaction', 'Upcoming PO 0987654894', 'transaction_view.php?id=47', 0, '2026-03-16 11:07:30'),
(326, 'accounting', 47, 'Supply Completed', 'Supply marked PO 0987654894 as Completed.', 'transaction_view.php?id=47', 0, '2026-03-16 11:08:14'),
(327, 'accounting', 47, 'Handoff Forwarded', 'SUPPLY forwarded PO 0987654894 to ACCOUNTING_PRE. Please receive it.', 'transaction_view.php?id=47', 0, '2026-03-16 11:09:30'),
(328, 'supply', 47, 'Handoff Received', 'Transaction was successfully received for PO 0987654894.', 'transaction_view.php?id=47', 0, '2026-03-16 11:09:34'),
(329, 'budget', 47, 'Pending Transaction', 'Upcoming PO 0987654894', 'transaction_view.php?id=47', 0, '2026-03-16 11:09:45'),
(330, 'budget', 47, 'Accounting Completed', 'Accounting marked PO 0987654894 as Completed.', 'transaction_view.php?id=47', 0, '2026-03-16 11:10:29'),
(331, 'budget', 47, 'Handoff Forwarded', 'ACCOUNTING_PRE forwarded PO 0987654894 to BUDGET. Please receive it.', 'transaction_view.php?id=47', 0, '2026-03-16 11:10:31'),
(332, 'accounting', 47, 'Handoff Received', 'Transaction was successfully received for PO 0987654894.', 'transaction_view.php?id=47', 0, '2026-03-16 11:10:36'),
(333, 'accounting', 47, 'Pending Transaction', 'Upcoming PO 0987654894', 'transaction_view.php?id=47', 0, '2026-03-16 11:11:26'),
(334, 'accounting', 47, 'Budget Completed', 'Budget marked PO 0987654894 as Completed.', 'transaction_view.php?id=47', 0, '2026-03-16 11:12:19'),
(335, 'accounting', 47, 'Handoff Forwarded', 'BUDGET forwarded PO 0987654894 to ACCOUNTING_POST. Please receive it.', 'transaction_view.php?id=47', 0, '2026-03-16 11:14:08'),
(336, 'budget', 47, 'Handoff Received', 'Transaction was successfully received for PO 0987654894.', 'transaction_view.php?id=47', 0, '2026-03-16 11:14:18'),
(337, 'cashier', 47, 'Pending Transaction', 'Upcoming PO 0987654894', 'transaction_view.php?id=47', 0, '2026-03-16 11:15:08'),
(338, 'cashier', 47, 'Accounting Completed', 'Accounting marked PO 0987654894 as Completed.', 'transaction_view.php?id=47', 0, '2026-03-16 11:15:08'),
(339, 'cashier', 47, 'Handoff Forwarded', 'ACCOUNTING_POST forwarded PO 0987654894 to CASHIER. Please receive it.', 'transaction_view.php?id=47', 0, '2026-03-16 11:15:18'),
(340, 'accounting', 47, 'Handoff Received', 'Transaction was successfully received for PO 0987654894.', 'transaction_view.php?id=47', 0, '2026-03-16 11:15:30'),
(341, 'supply', 48, 'Pending Transaction', 'Upcoming PO 345654', 'transaction_view.php?id=48', 0, '2026-03-17 14:55:56'),
(342, 'supply', 48, 'Procurement Completed', 'Procurement marked PO 345654 as Completed.', 'transaction_view.php?id=48', 0, '2026-03-17 14:55:56'),
(343, 'supply', 48, 'Handoff Forwarded', 'PROCUREMENT forwarded PO 345654 to SUPPLY. Please receive it.', 'transaction_view.php?id=48', 0, '2026-03-17 14:58:40');
INSERT INTO `department_notifications` (`id`, `role`, `transaction_id`, `title`, `message`, `link`, `is_read`, `created_at`) VALUES
(344, 'procurement', 48, 'Handoff Received', 'Transaction was successfully received for PO 345654.', 'transaction_view.php?id=48', 0, '2026-03-17 15:01:24'),
(345, 'accounting', 48, 'Pending Transaction', 'Upcoming PO 345654', 'transaction_view.php?id=48', 0, '2026-03-17 15:03:36'),
(346, 'accounting', 48, 'Supply Completed', 'Supply marked PO 345654 as Completed.', 'transaction_view.php?id=48', 0, '2026-03-17 15:03:36'),
(347, 'accounting', 48, 'Handoff Forwarded', 'SUPPLY forwarded PO 345654 to ACCOUNTING_PRE. Please receive it.', 'transaction_view.php?id=48', 0, '2026-03-17 15:03:59'),
(348, 'supply', 48, 'Handoff Received', 'Transaction was successfully received for PO 345654.', 'transaction_view.php?id=48', 0, '2026-03-17 15:05:17'),
(349, 'budget', 48, 'Pending Transaction', 'Upcoming PO 345654', 'transaction_view.php?id=48', 0, '2026-03-17 15:07:53'),
(350, 'budget', 48, 'Accounting Completed', 'Accounting marked PO 345654 as Completed.', 'transaction_view.php?id=48', 0, '2026-03-17 15:08:37'),
(351, 'budget', 48, 'Handoff Forwarded', 'ACCOUNTING_PRE forwarded PO 345654 to BUDGET. Please receive it.', 'transaction_view.php?id=48', 0, '2026-03-17 15:08:54'),
(352, 'accounting', 48, 'Handoff Received', 'Transaction was successfully received for PO 345654.', 'transaction_view.php?id=48', 0, '2026-03-17 15:09:01'),
(353, 'accounting', 48, 'Pending Transaction', 'Upcoming PO 345654', 'transaction_view.php?id=48', 0, '2026-03-17 15:10:01'),
(354, 'accounting', 48, 'Budget Completed', 'Budget marked PO 345654 as Completed.', 'transaction_view.php?id=48', 0, '2026-03-17 15:11:25'),
(355, 'accounting', 48, 'Handoff Forwarded', 'BUDGET forwarded PO 345654 to ACCOUNTING_POST. Please receive it.', 'transaction_view.php?id=48', 0, '2026-03-17 15:11:34'),
(356, 'budget', 48, 'Handoff Received', 'Transaction was successfully received for PO 345654.', 'transaction_view.php?id=48', 0, '2026-03-17 15:11:40'),
(357, 'cashier', 48, 'Pending Transaction', 'Upcoming PO 345654', 'transaction_view.php?id=48', 0, '2026-03-17 15:12:32'),
(358, 'cashier', 48, 'Accounting Completed', 'Accounting marked PO 345654 as Completed.', 'transaction_view.php?id=48', 0, '2026-03-17 15:12:32'),
(359, 'cashier', 48, 'Handoff Forwarded', 'ACCOUNTING_POST forwarded PO 345654 to CASHIER. Please receive it.', 'transaction_view.php?id=48', 0, '2026-03-17 15:12:49'),
(360, 'accounting', 48, 'Handoff Received', 'Transaction was successfully received for PO 345654.', 'transaction_view.php?id=48', 0, '2026-03-17 15:12:54'),
(361, 'supply', 49, 'Pending Transaction', 'Upcoming PO 8765678', 'transaction_view.php?id=49', 0, '2026-03-17 23:38:17'),
(362, 'supply', 49, 'Handoff Forwarded', 'PROCUREMENT forwarded PO 8765678 to SUPPLY. Please receive it.', 'transaction_view.php?id=49', 0, '2026-03-17 23:38:38'),
(363, 'procurement', 49, 'Handoff Received', 'Transaction was successfully received for PO 8765678.', 'transaction_view.php?id=49', 0, '2026-03-17 23:38:45'),
(364, 'accounting', 49, 'Pending Transaction', 'Upcoming PO 8765678', 'transaction_view.php?id=49', 0, '2026-03-18 00:47:45'),
(365, 'accounting', 49, 'Handoff Forwarded', 'SUPPLY forwarded PO 8765678 to ACCOUNTING_PRE. Please receive it.', 'transaction_view.php?id=49', 0, '2026-03-18 00:48:14'),
(366, 'supply', 49, 'Handoff Received', 'Transaction was successfully received for PO 8765678.', 'transaction_view.php?id=49', 0, '2026-03-18 01:06:39'),
(367, 'budget', 49, 'Pending Transaction', 'Upcoming PO 8765678', 'transaction_view.php?id=49', 0, '2026-03-18 01:29:43'),
(368, 'budget', 49, 'Handoff Forwarded', 'ACCOUNTING_PRE forwarded PO 8765678 to BUDGET. Please receive it.', 'transaction_view.php?id=49', 0, '2026-03-18 01:30:27'),
(369, 'accounting', 49, 'Handoff Received', 'Transaction was successfully received for PO 8765678.', 'transaction_view.php?id=49', 0, '2026-03-18 01:45:54'),
(370, 'accounting', 49, 'Pending Transaction', 'Upcoming PO 8765678', 'transaction_view.php?id=49', 0, '2026-03-18 01:46:39'),
(371, 'accounting', 49, 'Handoff Forwarded', 'BUDGET forwarded PO 8765678 to ACCOUNTING_POST. Please receive it.', 'transaction_view.php?id=49', 0, '2026-03-18 01:46:57'),
(372, 'budget', 49, 'Handoff Received', 'Transaction was successfully received for PO 8765678.', 'transaction_view.php?id=49', 0, '2026-03-18 01:47:17'),
(373, 'cashier', 49, 'Pending Transaction', 'Upcoming PO 8765678', 'transaction_view.php?id=49', 0, '2026-03-18 01:47:44'),
(374, 'cashier', 49, 'Handoff Forwarded', 'ACCOUNTING_POST forwarded PO 8765678 to CASHIER. Please receive it.', 'transaction_view.php?id=49', 0, '2026-03-18 01:48:25'),
(375, 'accounting', 49, 'Handoff Received', 'Transaction was successfully received for PO 8765678.', 'transaction_view.php?id=49', 0, '2026-03-18 01:49:12'),
(376, 'supply', 50, 'Handoff Forwarded', 'PROCUREMENT forwarded PO 5678987 to SUPPLY. Please receive it.', 'transaction_view.php?id=50', 0, '2026-03-18 01:50:14'),
(377, 'supply', 50, 'Pending Transaction', 'Upcoming PO 5678987', 'transaction_view.php?id=50', 0, '2026-03-18 01:50:21'),
(378, 'procurement', 50, 'Handoff Received', 'Transaction was successfully received for PO 5678987.', 'transaction_view.php?id=50', 0, '2026-03-18 01:50:28'),
(379, 'accounting', 50, 'Pending Transaction', 'Upcoming PO 5678987', 'transaction_view.php?id=50', 0, '2026-03-18 01:50:37'),
(380, 'accounting', 50, 'Supply Completed', 'Supply marked PO 5678987 as Completed.', 'transaction_view.php?id=50', 0, '2026-03-18 01:50:37'),
(381, 'accounting', 50, 'Handoff Forwarded', 'SUPPLY forwarded PO 5678987 to ACCOUNTING_PRE. Please receive it.', 'transaction_view.php?id=50', 0, '2026-03-18 01:50:40'),
(382, 'supply', 50, 'Handoff Received', 'Transaction was successfully received for PO 5678987.', 'transaction_view.php?id=50', 0, '2026-03-18 01:50:45'),
(383, 'budget', 50, 'Pending Transaction', 'Upcoming PO 5678987', 'transaction_view.php?id=50', 0, '2026-03-18 01:50:51'),
(384, 'budget', 50, 'Handoff Forwarded', 'ACCOUNTING_PRE forwarded PO 5678987 to BUDGET. Please receive it.', 'transaction_view.php?id=50', 0, '2026-03-18 01:50:56'),
(385, 'accounting', 50, 'Handoff Received', 'Transaction was successfully received for PO 5678987.', 'transaction_view.php?id=50', 0, '2026-03-18 01:51:06'),
(386, 'accounting', 50, 'Handoff Forwarded', 'BUDGET forwarded PO 5678987 to ACCOUNTING_POST. Please receive it.', 'transaction_view.php?id=50', 0, '2026-03-18 01:51:18'),
(387, 'budget', 50, 'Handoff Received', 'Transaction was successfully received for PO 5678987.', 'transaction_view.php?id=50', 0, '2026-03-18 01:51:30'),
(388, 'cashier', 50, 'Pending Transaction', 'Upcoming PO 5678987', 'transaction_view.php?id=50', 0, '2026-03-18 01:51:40'),
(389, 'cashier', 50, 'Handoff Forwarded', 'ACCOUNTING_POST forwarded PO 5678987 to CASHIER. Please receive it.', 'transaction_view.php?id=50', 0, '2026-03-18 01:51:43');

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

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `user_id`, `role`, `type`, `message`, `is_read`, `created_at`) VALUES
(1, 12, 'procurement', 'Other', 'sample2', 1, '2026-02-14 10:51:56'),
(2, 18, 'supplier', 'Suggestion', 'sample 3', 1, '2026-02-14 10:52:29'),
(3, 16, 'cashier', 'Other', 'okay', 1, '2026-02-28 03:21:24'),
(4, 21, 'supplier', 'Bug', 'sample again', 1, '2026-02-28 03:24:54'),
(5, 21, 'supplier', 'Suggestion', 'asdqwert', 1, '2026-02-28 03:25:50'),
(6, 16, 'cashier', 'Other', 'sample6', 1, '2026-03-13 07:41:57'),
(7, 12, 'procurement', 'Suggestion', 'iop', 1, '2026-03-13 07:54:14'),
(8, 12, 'procurement', 'Other', 'okay', 1, '2026-03-13 07:54:43'),
(9, 12, 'procurement', 'Suggestion', 'nop', 1, '2026-03-13 07:57:03'),
(10, 12, 'procurement', 'Suggestion', 'nop', 1, '2026-03-13 07:57:03'),
(11, 12, 'procurement', 'Suggestion', 'nope', 1, '2026-03-13 07:57:04'),
(12, 15, 'budget', 'Suggestion', 'okay', 1, '2026-03-16 03:35:40'),
(13, 12, 'procurement', 'Other', 'Sample local server', 1, '2026-03-17 19:22:15');

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
(1, 6, 20, 'Payment status update', 'Your PO asd has been updated by Cashier.', 'transaction_view.php?id=20', 0, 0, '2026-02-28 07:36:50'),
(2, 6, 20, 'Payment status update', 'Your PO asd has been updated by Cashier.', 'transaction_view.php?id=20', 1, 0, '2026-02-28 07:37:20'),
(3, 6, 20, 'Payment status update', 'Your PO asd has been updated by Cashier.', 'transaction_view.php?id=20', 1, 0, '2026-02-28 07:43:01'),
(4, 6, 20, 'Payment status update', 'Your PO asd has been updated by Cashier.', 'transaction_view.php?id=20', 0, 0, '2026-02-28 07:56:49'),
(5, 6, 20, 'Payment status update', 'Your PO asd has been updated by Cashier.', 'transaction_view.php?id=20', 0, 0, '2026-02-28 07:57:01'),
(6, 6, 20, 'Payment status update', 'Your PO asd has been updated by Cashier.', 'transaction_view.php?id=20', 1, 0, '2026-02-28 07:57:09'),
(7, 3, 16, 'Payment status update', 'Your PO asdkj123 has been updated by Cashier.', 'transaction_view.php?id=16', 1, 0, '2026-02-28 08:14:05'),
(8, 3, 14, 'Payment status update', 'Your PO anskdjk1n23 has been updated by Cashier.', 'transaction_view.php?id=14', 1, 0, '2026-02-28 08:14:48'),
(9, 6, 20, 'Payment status update', 'Your PO asd has been updated by Cashier.', 'transaction_view.php?id=20', 1, 0, '2026-02-28 08:30:28'),
(10, 6, 20, 'Payment status update', 'Your PO asd has been updated by Cashier.', 'transaction_view.php?id=20', 0, 0, '2026-02-28 10:02:05'),
(11, 6, 20, 'Payment status update', 'Your PO asd has been updated by Cashier.', 'transaction_view.php?id=20', 0, 0, '2026-02-28 11:05:20'),
(12, 6, 20, 'Payment status update', 'Your PO asd has been updated by Cashier.', 'transaction_view.php?id=20', 0, 0, '2026-03-03 08:38:11'),
(13, 3, 22, 'Transaction completed', 'Your PO 12345 has been completed.', 'transaction_view.php?id=22', 1, 0, '2026-03-04 22:43:19'),
(14, 3, 23, 'Transaction completed', 'Your PO 4567 has been completed.', 'transaction_view.php?id=23', 0, 0, '2026-03-04 22:58:12'),
(15, 3, 24, 'Transaction completed', 'Your PO 123 has been completed.', 'transaction_view.php?id=24', 1, 0, '2026-03-05 02:00:14'),
(16, 3, 24, 'Payment status update', 'Your PO 123 has been updated by Cashier.', 'transaction_view.php?id=24', 0, 0, '2026-03-05 03:16:15'),
(17, 6, 20, 'Payment status update', 'Your PO asd has been updated by Cashier.', 'transaction_view.php?id=20', 0, 0, '2026-03-05 03:21:49'),
(18, 3, 32, 'Transaction completed', 'Your PO 123 has been completed.', 'transaction_view.php?id=32', 0, 0, '2026-03-05 16:09:42'),
(19, 7, 33, 'Transaction completed', 'Your PO 12345678 has been completed.', 'transaction_view.php?id=33', 0, 0, '2026-03-10 22:43:03'),
(20, 7, 33, 'Payment status update', 'Your PO 12345678 has been updated by Cashier.', 'transaction_view.php?id=33', 0, 0, '2026-03-10 22:43:23'),
(21, 7, 33, 'Payment status update', 'okay', 'transaction_view.php?id=33', 0, 0, '2026-03-11 08:44:43'),
(22, 7, 33, 'Payment status update', 'Your PO 12345678 is now marked as COMPLETED. Please check the portal for details.', 'transaction_view.php?id=33', 0, 0, '2026-03-11 08:45:18'),
(23, 7, 34, 'Payment status update', 'Your PO 1234567890 is now marked as COMPLETED. Please check the portal for details.', 'transaction_view.php?id=34', 0, 0, '2026-03-11 09:26:03'),
(24, 7, 35, 'Transaction completed', 'Your PO 456789098765 has been completed.', 'transaction_view.php?id=35', 0, 0, '2026-03-11 09:43:02'),
(25, 7, 44, 'Transaction completed', 'Your PO 876543456 has been completed.', 'transaction_view.php?id=44', 0, 0, '2026-03-15 16:25:19'),
(26, 7, 48, 'Transaction completed', 'Your PO 345654 has been completed.', 'transaction_view.php?id=48', 0, 0, '2026-03-17 15:13:49'),
(27, 7, 48, 'Payment status update', 'Your PO 345654 is now marked as COMPLETED. Please check the portal for details.', 'transaction_view.php?id=48', 0, 0, '2026-03-17 15:13:58'),
(28, 7, 49, 'Transaction completed', 'Your PO 8765678 has been completed.', 'transaction_view.php?id=49', 0, 0, '2026-03-18 01:49:21');

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
(3, 'supplier-1', '2026-02-10 07:05:35', NULL),
(6, 'alpha num', '2026-02-27 20:44:26', 'alphanum0001@gmail.com'),
(7, 'alpha num2\'', '2026-03-03 00:37:19', 'alphanum0002@gmail.com');

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

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `supplier_id`, `po_number`, `program_title`, `po_type`, `proponent`, `coverage_start`, `coverage_end`, `expected_date`, `amount`, `created_at`, `proc_status`, `proc_remarks`, `proc_date`, `supply_status`, `supply_delivery_receipt`, `supply_sales_invoice`, `supply_remarks`, `supply_date`, `acct_pre_status`, `acct_pre_remarks`, `acct_pre_date`, `budget_dv_number`, `budget_dv_date`, `budget_status`, `budget_demandability`, `budget_remarks`, `acct_post_status`, `acct_post_remarks`, `acct_post_date`, `cashier_status`, `cashier_remarks`, `cashier_or_number`, `cashier_or_date`, `cashier_landbank_ref`, `cashier_payment_date`, `supply_partial_delivery_date`, `supply_delivery_date`) VALUES
(10, 3, '2026-01-03', 'qwerty', 'Transpo/venue', 'jeff', '2026-02-18', '2026-02-18', '2026-02-19', 40000.00, '2026-02-19 01:53:16', 'FOR SUPPLY REVIEW', 'brrt', '2026-02-20', 'COMPLETED', 'asdasdqwe', 'asd', 'sample', '2026-02-20', 'PRE-BUDGET FOR VOUCHER', 'Completed', '2026-02-19', '2026-01-04', '2026-02-11', 'FOR ORS', 'DUE DEMANDABLE', '', 'POST BUDGET FOR VOUCHER', 'completed', '2026-02-19', 'FOR OR INSUANCE', '', '', '0000-00-00', '', '0000-00-00', NULL, NULL),
(11, 3, 'asdasd', 'asdasdasdasd', 'Supplies', 'qweqweqwe', '2026-02-19', '2026-02-26', NULL, 333.00, '2026-02-20 03:05:34', 'COMPLETED', 'okay 123', '2026-02-20', 'PARTIAL DELIVER', 'asd', 'asd', 'sample', '2026-02-20', 'PRE-BUDGET FOR VOUCHER', 'desc1', '2026-02-23', '123', '0000-00-00', 'FOR PAYMENT', 'Not Yet Due and Demandable', 'asd', 'POST BUDGET FOR VOUCHER', 'desc2\nDV Amount: 111111', '2026-02-23', 'For ACIC', 'asdasdasd', 'asdqwe', '0000-00-00', '1000', '0000-00-00', NULL, NULL),
(13, 3, 'asdlklk123', 'okay1', 'Transpo/venue', 'qwertyqqwe', '2026-02-24', '2026-02-27', 'bukas', 69696.00, '2026-02-23 07:19:04', 'COMPLETED', '', '2026-03-04', 'COMPLETED', '', '', '', '2026-03-04', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 3, 'asd', 'as', 'Transpo/venue', 'asd', '2026-02-19', '2026-02-28', NULL, 1111.00, '2026-02-27 02:47:14', 'COMPLETED', '', '2026-03-04', 'COMPLETED', '', '', '', '2026-03-04', 'FOR ORS', '', '2026-03-05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 6, 'asd', 'asdasd', 'Transpo/venue', 'asldmkasm', '2026-02-18', '2026-02-26', NULL, 3030303.00, '2026-02-27 21:05:32', 'COMPLETED', 'okay', '2026-03-04', 'COMPLETED', 'sample receipt', 'sample sales invoice', 'okay', '2026-02-28', '', 'okay', '2026-02-28', 'asd', '2026-02-26', 'ACCOUNTS PAYABLE', 'Not Yet Due and Demandable', 'again', 'FOR VOUCHER', 'okay3\nDV Amount: 5000', '2026-02-28', 'For OR Issuance', 'sample', 'dfgfgd5', '2026-02-25', '2000', '2026-03-07', NULL, NULL),
(21, 3, 'sample 5', 'sample 5 title', 'Transpo/venue', 'qwerty', '2026-03-05', '2026-03-13', NULL, 40000.00, '2026-03-04 03:00:38', 'FOR SUPPLY REVIEW', 'sampelee', '2026-03-04', 'PARTIAL DELIVER', '', '', 'apwem', '2026-03-04', '', 'receive from proponent', '2026-03-04', '1234 aswq', '2026-03-12', 'ACCOUNTS PAYABLE', 'Due and Demandable', 'asdqwe', 'FOR VOUCHER', 'sample\nDV Amount: 1000', '2026-03-04', 'For ACIC', 'sample remaks', 'asdklkj1 23', '2026-03-11', '1000', '2026-03-12', NULL, NULL),
(22, 3, '12345', 'sample6', 'Meals', 'samlpeproponent', NULL, NULL, 'next week daw', 1000.00, '2026-03-04 13:38:41', 'COMPLETED', 'sample', '2026-03-04', 'COMPLETED', '', '', '', '2026-03-04', 'COMPLETED', '', '2026-03-04', '123141', '0000-00-00', 'COMPLETED', '', '', 'COMPLETED', '', '2026-03-04', 'COMPLETED', '', '', '0000-00-00', '', '0000-00-00', NULL, NULL),
(23, 3, '4567', 'qwerty', 'Meals', 'asdfgh', NULL, NULL, 'bukas', 12345.00, '2026-03-04 14:44:07', 'COMPLETED', 'okay', '2026-03-04', 'PARTIAL DELIVER', '', '', '', '2026-03-04', 'FOR VOUCHER', '', '2026-03-04', '', '0000-00-00', 'FOR PAYMENT', '', '', 'COMPLETED', '', '2026-03-04', 'COMPLETED', '', '', '0000-00-00', '', '0000-00-00', NULL, NULL),
(24, 3, '123', 'sample7', 'Supplies', 'qwert', NULL, NULL, 'ngayon', 10000.00, '2026-03-04 17:55:30', 'COMPLETED', '', '2026-03-05', 'COMPLETED', '', '', '', '2026-03-05', 'FOR VOUCHER', '', '2026-03-05', '432', '0000-00-00', 'COMPLETED', '', '', 'COMPLETED', '', '2026-03-05', 'COMPLETED', '', '', '0000-00-00', '', '0000-00-00', NULL, NULL),
(25, 3, '565', 'sample8', 'Supplies', 'hjkl', NULL, NULL, 'today', 5000.00, '2026-03-04 18:55:50', 'COMPLETED', '', '2026-03-05', 'COMPLETED', '1233', '4564', '', '2026-03-05', 'FOR ORS', '', '2026-03-05', '123441', '0000-00-00', 'FOR PAYMENT', '', '', 'COMPLETED', '', '2026-03-05', 'For ACIC', '', '98789', '0000-00-00', '', '0000-00-00', NULL, NULL),
(32, 3, '123', '123', '123', '123', NULL, NULL, '123', 123.00, '2026-03-05 07:16:35', 'COMPLETED', '', '2026-03-05', 'COMPLETED', '1234', '12334', '', '2026-03-05', 'COMPLETED', '', '2026-03-05', '', '0000-00-00', 'COMPLETED', '', '', 'COMPLETED', '', '2026-03-05', 'COMPLETED', '', '', '0000-00-00', '', '0000-00-00', NULL, NULL),
(33, 7, '12345678', 'sample10', 'Services', 'qwerty', NULL, NULL, 'ngayon', 10000.00, '2026-03-10 14:25:49', 'COMPLETED', '', '2026-03-10', 'COMPLETED', '', '', '', '2026-03-10', 'COMPLETED', '', '2026-03-10', '', '0000-00-00', 'COMPLETED', '', '', 'COMPLETED', '', '2026-03-10', 'COMPLETED', '', '', '0000-00-00', '', '0000-00-00', NULL, NULL),
(34, 7, '1234567890', 'sample 11', 'okay', 'qwerty', '2026-03-13', '2026-03-18', 'ngayon', 40000.00, '2026-03-11 01:08:22', 'COMPLETED', '', '2026-03-11', 'COMPLETED', '', '', '', '2026-03-11', 'COMPLETED', '', '2026-03-11', '', '0000-00-00', 'COMPLETED', '', '', 'COMPLETED', '', '2026-03-11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(35, 7, '456789098765', 'sample12', 'Transpo/venue', 'qwerty', '2026-03-12', '2026-03-20', NULL, 7890.00, '2026-03-11 01:30:48', 'COMPLETED', '', '2026-03-11', 'COMPLETED', '', '', '', '2026-03-11', 'COMPLETED', '', '2026-03-11', '', '0000-00-00', 'COMPLETED', '', '', 'COMPLETED', '', '2026-03-11', 'COMPLETED', '', '', '0000-00-00', '', '0000-00-00', NULL, NULL),
(36, 7, '123456789', 'sample 13', 'Transpo/venue', 'qwerty', NULL, NULL, 'ngayun', 234567890.00, '2026-03-11 01:50:02', 'COMPLETED', '', '2026-03-11', 'COMPLETED', '', '', '', '2026-03-11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(37, 7, '1234567898765432', 'sample 14', 'Supplies', 'qwerty', NULL, NULL, 'ngayon', 100101010.00, '2026-03-11 02:55:05', 'COMPLETED', '', '2026-03-11', 'COMPLETED', '', '', '', '2026-03-11', 'COMPLETED', '', '2026-03-11', '', '0000-00-00', 'COMPLETED', '', '', 'COMPLETED', '', '2026-03-11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(38, 7, '12345678765432', 'sample 15', 'Supplies', 'qwerty', '2026-03-12', '2026-03-21', NULL, 12345678.00, '2026-03-11 05:50:01', 'COMPLETED', '', '2026-03-11', 'COMPLETED', '', '', '', '2026-03-11', 'COMPLETED', '', '2026-03-11', '', '0000-00-00', 'COMPLETED', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(39, 7, '23456763', 'sample16', 'Transpo/venue', 'qwerty', NULL, NULL, 'npoe', 12345.00, '2026-03-11 06:32:16', 'COMPLETED', '', '2026-03-11', 'COMPLETED', '', '', '', '2026-03-11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(40, 7, '20343409494', 'josharatry', 'Supplies', 'jeeeeff', NULL, NULL, NULL, 979898.00, '2026-03-12 01:45:57', 'COMPLETED', '', '2026-03-12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(41, 7, '1235776543', 'sample17', 'Supplies', 'qweyuy', NULL, NULL, NULL, 40000.00, '2026-03-12 07:00:45', 'COMPLETED', '', '2026-03-12', 'COMPLETED', '', '', '', '2026-03-12', 'COMPLETED', '', '2026-03-12', '', '0000-00-00', 'FOR PAYMENT', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(42, 7, '1234567654', 'sample 18', 'Transpo/venue', 'qwerty', NULL, NULL, 'asd', 145.00, '2026-03-13 04:01:25', 'COMPLETED', '', '2026-03-13', 'COMPLETED', '', '', '', '2026-03-13', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(43, 7, '65678', 'sample 18', 'Supplies', 'qwerty', NULL, NULL, NULL, 9000.00, '2026-03-13 05:20:52', 'COMPLETED', '', '2026-03-13', 'PARTIAL DELIVER', '1234', '1234543', 'okay', '2026-03-14', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-21', '2026-03-28'),
(44, 7, '876543456', 'sample19', 'qwer', 'qwerty', NULL, NULL, 'bukas', 909090.00, '2026-03-14 11:39:32', 'COMPLETED', '', '2026-03-14', 'COMPLETED', '234', '345', 'okay', '2026-03-14', 'COMPLETED', '', '2026-03-15', '', '0000-00-00', 'COMPLETED', '', '', 'COMPLETED', '', '2026-03-15', 'COMPLETED', '', '', '0000-00-00', '', '0000-00-00', '2026-03-20', '2026-03-28'),
(45, 7, '0987678', 'sample20', 'Transpo/venue', 'qwerty', NULL, NULL, 'vukds', 80800800.00, '2026-03-15 05:01:08', 'FOR SUPPLY REVIEW', 'nop', '2026-03-15', 'COMPLETED', '767', '786', '', '2026-03-15', 'COMPLETED', 'DV Amount: 8000', '2026-03-15', '87678', '2026-03-10', 'COMPLETED', '', '', 'COMPLETED', 'DV Amount: 1000', '2026-03-15', 'For OR Issuance', '', '2234', '2026-03-06', '100', '2026-03-17', '2026-03-15', '2026-03-28'),
(46, 7, '1234567823', 'sample21', 'Transpo/venue', 'qwerty', NULL, NULL, 'as', 1234.00, '2026-03-15 20:46:14', 'FOR SUPPLY REVIEW', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(47, 7, '0987654894', 'sample22', 'sample', 'qwerty', NULL, NULL, 'ngayon', 90000.00, '2026-03-16 03:02:19', 'COMPLETED', '', '2026-03-16', 'COMPLETED', '3454325', '6543', '', '2026-03-16', 'COMPLETED', '', '2026-03-16', '34442', '2026-03-10', 'COMPLETED', 'Due and Demandable', '', 'COMPLETED', 'DV Amount: 9000', '2026-03-16', 'For ACIC', '', '876789', '2026-03-11', '7890', '2026-03-27', '2026-03-19', '2026-03-27'),
(48, 7, '345654', 'lorese canteen', 'Meals', 'ann', NULL, NULL, NULL, 30000.00, '2026-03-17 06:55:17', 'COMPLETED', '', '2026-03-17', 'COMPLETED', '34564', '76567', '', '2026-03-17', 'COMPLETED', '', '2026-03-17', '876', '0000-00-00', 'COMPLETED', 'Due and Demandable', '', 'COMPLETED', '', '2026-03-17', 'COMPLETED', '', '', '0000-00-00', '', '0000-00-00', NULL, '2026-03-25'),
(49, 7, '8765678', 'sample23', 'j', 'qwerty', NULL, NULL, 'okay', 9000.00, '2026-03-17 13:13:41', 'FOR SUPPLY REVIEW', '', '2026-03-17', 'PARTIAL DELIVERY', '', '', '', '2026-03-18', 'FOR ORS', 'DV Amount: 8000', '2026-03-18', '', '0000-00-00', 'ACCOUNTS PAYABLE', '', '', 'FOR VOUCHER', '', '2026-03-18', 'COMPLETED', '', '', '0000-00-00', '', '0000-00-00', '2026-03-17', NULL),
(50, 7, '5678987', '24', 'ghjh', 'qwert', NULL, NULL, NULL, 90000.00, '2026-03-17 17:50:00', 'FOR SUPPLY REVIEW', '', '2026-03-18', 'COMPLETED', '', '', '', '2026-03-18', 'FOR ORS', '', '2026-03-18', '', '0000-00-00', 'FOR PAYMENT', '', '', 'FOR VOUCHER', '', '2026-03-18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-17');

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
(10, 27, 'procurement', 'supply', '2026-03-05 13:57:59', '2026-03-05 13:58:07', 0, 0, 12, 13),
(11, 27, 'procurement', 'supply', '2026-03-05 13:58:29', '2026-03-05 13:59:43', 0, 0, 12, 13),
(12, 27, 'procurement', 'supply', '2026-03-05 14:03:33', '2026-03-05 14:04:05', 0, 0, 12, 13),
(13, 27, 'supply', 'accounting_pre', '2026-03-05 14:09:45', '2026-03-05 14:11:10', 0, 0, 13, 14),
(14, 28, 'procurement', 'supply', '2026-03-05 14:18:23', '2026-03-05 14:18:48', 0, 0, 12, 13),
(15, 28, 'supply', 'accounting_pre', '2026-03-05 14:21:52', '2026-03-05 14:22:48', 0, 0, 13, 14),
(16, 29, 'procurement', 'supply', '2026-03-05 14:47:23', '2026-03-05 14:47:45', 0, 0, 12, 13),
(17, 29, 'supply', 'accounting_pre', '2026-03-05 15:03:27', '2026-03-05 15:03:39', 0, 0, 13, 14),
(18, 29, 'accounting_pre', 'budget', '2026-03-05 15:04:47', '2026-03-05 15:05:08', 0, 0, 14, 1),
(19, 29, 'budget', 'accounting_post', '2026-03-05 15:07:03', '2026-03-05 15:07:06', 0, 0, 1, 14),
(20, 29, 'accounting_post', 'cashier', '2026-03-05 15:07:24', '2026-03-05 15:07:49', 0, 0, 14, 16),
(21, 32, 'procurement', 'supply', '2026-03-05 15:36:09', '2026-03-05 15:36:18', 0, 0, 12, 13),
(22, 32, 'supply', 'accounting_pre', '2026-03-05 16:03:10', '2026-03-05 16:03:22', 0, 0, 13, 14),
(23, 32, 'accounting_pre', 'budget', '2026-03-05 16:03:58', '2026-03-05 16:04:01', 0, 0, 14, 1),
(24, 32, 'budget', 'accounting_post', '2026-03-05 16:04:22', '2026-03-05 16:04:38', 0, 0, 1, 14),
(25, 32, 'accounting_post', 'cashier', '2026-03-05 16:05:02', '2026-03-05 16:05:54', 0, 0, 14, 16),
(26, 33, 'procurement', 'supply', '2026-03-10 22:40:49', '2026-03-10 22:40:53', 0, 0, 12, 13),
(27, 33, 'supply', 'accounting_pre', '2026-03-10 22:41:05', '2026-03-10 22:41:19', 0, 0, 13, 14),
(28, 33, 'accounting_pre', 'budget', '2026-03-10 22:41:34', '2026-03-10 22:41:56', 0, 0, 14, 15),
(29, 33, 'budget', 'accounting_post', '2026-03-10 22:42:05', '2026-03-10 22:42:12', 0, 0, 15, 14),
(30, 33, 'accounting_post', 'cashier', '2026-03-10 22:42:42', '2026-03-10 22:42:52', 0, 0, 14, 16),
(31, 34, 'procurement', 'supply', '2026-03-11 09:11:21', '2026-03-11 09:12:02', 0, 0, 12, 13),
(32, 34, 'supply', 'accounting_pre', '2026-03-11 09:12:50', '2026-03-11 09:14:12', 0, 0, 13, 14),
(33, 34, 'accounting_pre', 'budget', '2026-03-11 09:14:28', '2026-03-11 09:16:39', 0, 0, 14, 15),
(34, 34, 'budget', 'accounting_post', '2026-03-11 09:17:13', '2026-03-11 09:17:24', 0, 0, 15, 14),
(35, 34, 'accounting_post', 'cashier', '2026-03-11 09:20:28', '2026-03-11 09:20:33', 0, 0, 14, 16),
(36, 35, 'procurement', 'supply', '2026-03-11 09:33:07', '2026-03-11 09:35:29', 0, 0, 12, 13),
(37, 35, 'supply', 'accounting_pre', '2026-03-11 09:37:13', '2026-03-11 09:37:36', 0, 0, 13, 14),
(38, 35, 'accounting_pre', 'budget', '2026-03-11 09:37:49', '2026-03-11 09:39:30', 0, 0, 14, 15),
(39, 35, 'budget', 'accounting_post', '2026-03-11 09:40:11', '2026-03-11 09:41:13', 0, 0, 15, 14),
(40, 35, 'accounting_post', 'cashier', '2026-03-11 09:42:29', '2026-03-11 09:42:50', 0, 0, 14, 16),
(41, 36, 'procurement', 'supply', '2026-03-11 09:54:01', '2026-03-11 09:54:57', 0, 0, 12, 13),
(42, 37, 'procurement', 'supply', '2026-03-11 10:57:01', '2026-03-11 10:57:42', 0, 0, 12, 13),
(43, 37, 'supply', 'accounting_pre', '2026-03-11 11:02:01', '2026-03-11 11:04:02', 0, 0, 13, 14),
(44, 37, 'accounting_pre', 'budget', '2026-03-11 11:09:01', '2026-03-11 11:11:02', 0, 0, 14, 15),
(45, 37, 'budget', 'accounting_post', '2026-03-11 13:19:30', '2026-03-11 13:21:25', 0, 0, 15, 14),
(46, 37, 'accounting_post', 'cashier', '2026-03-11 13:22:53', '2026-03-11 13:24:25', 0, 0, 14, 16),
(47, 38, 'procurement', 'supply', '2026-03-11 13:53:01', '2026-03-11 13:54:02', 0, 0, 12, 13),
(48, 38, 'supply', 'accounting_pre', '2026-03-11 13:58:01', '2026-03-11 14:00:02', 0, 0, 13, 14),
(49, 38, 'accounting_pre', 'budget', '2026-03-11 14:05:02', '2026-03-11 14:09:03', 0, 0, 14, 15),
(50, 39, 'procurement', 'supply', '2026-03-11 14:32:30', '2026-03-11 14:34:36', 0, 0, 12, 13),
(51, 39, 'supply', 'accounting_pre', '2026-03-11 14:36:24', '2026-03-11 14:37:00', 0, 0, 13, 14),
(52, 40, 'procurement', 'supply', '2026-03-12 09:49:25', '2026-03-12 09:53:15', 0, 0, 12, 13),
(53, 41, 'procurement', 'supply', '2026-03-12 15:02:58', '2026-03-12 15:03:06', 0, 0, 12, 13),
(54, 41, 'supply', 'accounting_pre', '2026-03-12 15:12:57', '2026-03-12 15:13:04', 0, 0, 13, 14),
(55, 41, 'accounting_pre', 'budget', '2026-03-12 15:13:50', '2026-03-12 15:13:54', 0, 0, 14, 15),
(56, 42, 'procurement', 'supply', '2026-03-13 12:01:58', '2026-03-13 12:02:09', 0, 0, 12, 13),
(57, 43, 'procurement', 'supply', '2026-03-13 13:28:28', '2026-03-13 13:30:18', 0, 0, 12, 13),
(58, 43, 'supply', 'accounting_pre', '2026-03-14 13:35:03', '2026-03-16 05:32:40', 118657, 1, 13, 14),
(59, 44, 'procurement', 'supply', '2026-03-14 19:44:30', '2026-03-14 19:44:38', 0, 0, 12, 13),
(60, 44, 'supply', 'accounting_pre', '2026-03-14 19:48:59', '2026-03-14 19:49:10', 0, 0, 13, 14),
(61, 45, 'procurement', 'supply', '2026-03-15 13:02:06', '2026-03-15 13:02:17', 0, 0, 12, 13),
(62, 45, 'supply', 'accounting_pre', '2026-03-15 13:37:15', '2026-03-15 13:37:21', 0, 0, 13, 14),
(63, 45, 'accounting_pre', 'budget', '2026-03-15 13:47:22', '2026-03-15 13:48:42', 0, 0, 14, 15),
(64, 45, 'budget', 'accounting_post', '2026-03-15 13:51:01', '2026-03-15 13:51:25', 0, 0, 15, 14),
(65, 45, 'accounting_post', 'cashier', '2026-03-15 13:52:47', '2026-03-15 13:53:14', 0, 0, 14, 16),
(66, 44, 'accounting_pre', 'budget', '2026-03-15 15:32:51', '2026-03-15 15:46:51', 0, 0, 14, 15),
(67, 44, 'budget', 'accounting_post', '2026-03-15 15:49:31', '2026-03-15 16:21:05', 0, 0, 15, 14),
(68, 44, 'accounting_post', 'cashier', '2026-03-15 16:22:31', '2026-03-15 16:24:59', 0, 0, 14, 16),
(69, 47, 'procurement', 'supply', '2026-03-16 11:05:58', '2026-03-16 11:06:33', 0, 0, 12, 13),
(70, 47, 'supply', 'accounting_pre', '2026-03-16 11:09:30', '2026-03-16 11:09:34', 0, 0, 13, 14),
(71, 47, 'accounting_pre', 'budget', '2026-03-16 11:10:31', '2026-03-16 11:10:36', 0, 0, 14, 15),
(72, 47, 'budget', 'accounting_post', '2026-03-16 11:14:08', '2026-03-16 11:14:18', 0, 0, 15, 14),
(73, 47, 'accounting_post', 'cashier', '2026-03-16 11:15:18', '2026-03-16 11:15:30', 0, 0, 14, 16),
(74, 48, 'procurement', 'supply', '2026-03-17 14:58:40', '2026-03-17 15:01:24', 0, 0, 12, 13),
(75, 48, 'supply', 'accounting_pre', '2026-03-17 15:03:59', '2026-03-17 15:05:17', 0, 0, 13, 14),
(76, 48, 'accounting_pre', 'budget', '2026-03-17 15:08:54', '2026-03-17 15:09:01', 0, 0, 14, 15),
(77, 48, 'budget', 'accounting_post', '2026-03-17 15:11:34', '2026-03-17 15:11:40', 0, 0, 15, 14),
(78, 48, 'accounting_post', 'cashier', '2026-03-17 15:12:49', '2026-03-17 15:12:54', 0, 0, 14, 16),
(79, 49, 'procurement', 'supply', '2026-03-17 23:38:38', '2026-03-17 23:38:45', 0, 0, 12, 13),
(80, 49, 'supply', 'accounting_pre', '2026-03-18 00:48:14', '2026-03-18 01:06:39', 0, 0, 13, 14),
(81, 49, 'accounting_pre', 'budget', '2026-03-18 01:30:27', '2026-03-18 01:45:54', 0, 0, 14, 15),
(82, 49, 'budget', 'accounting_post', '2026-03-18 01:46:57', '2026-03-18 01:47:17', 0, 0, 15, 14),
(83, 49, 'accounting_post', 'cashier', '2026-03-18 01:48:25', '2026-03-18 01:49:12', 0, 0, 14, 16),
(84, 50, 'procurement', 'supply', '2026-03-18 01:50:14', '2026-03-18 01:50:28', 0, 0, 12, 13),
(85, 50, 'supply', 'accounting_pre', '2026-03-18 01:50:40', '2026-03-18 01:50:45', 0, 0, 13, 14),
(86, 50, 'accounting_pre', 'budget', '2026-03-18 01:50:56', '2026-03-18 01:51:06', 0, 0, 14, 15),
(87, 50, 'budget', 'accounting_post', '2026-03-18 01:51:18', '2026-03-18 01:51:30', 0, 0, 15, 14),
(88, 50, 'accounting_post', 'cashier', '2026-03-18 01:51:43', '2026-03-18 01:51:49', 0, 0, 14, 16);

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
(1, 10, 'procurement', 'FOR SUPPLY REVIEW', 'okayy', '2026-02-20 02:58:12'),
(2, 10, 'procurement', 'FOR SUPPLY REVIEW', 'okayy123', '2026-02-20 02:58:16'),
(3, 10, 'procurement', 'FOR SUPPLY REVIEW', 'brrt', '2026-02-20 02:58:28'),
(4, 10, 'supply', 'COMPLETED', 'okay', '2026-02-20 03:01:07'),
(5, 10, 'supply', 'PARTIAL DELIVER', 'no', '2026-02-20 03:01:18'),
(6, 10, 'supply', 'COMPLETED', 'sample', '2026-02-20 03:01:32'),
(7, 11, 'procurement', 'FOR SUPPLY REVIEW', 'okay', '2026-02-20 03:06:03'),
(8, 11, 'procurement', 'COMPLETED', 'okay 123', '2026-02-20 03:07:25'),
(9, 11, 'supply', 'PARTIAL DELIVER', 'sample', '2026-02-20 03:32:45'),
(10, 11, 'supply', 'PARTIAL DELIVER', 'sample', '2026-02-20 03:35:50'),
(11, 11, 'supply', 'PARTIAL DELIVER', 'sample', '2026-02-20 03:39:32'),
(12, 11, 'supply', 'PARTIAL DELIVER', 'sample', '2026-02-20 03:41:30'),
(13, 11, 'supply', 'PARTIAL DELIVER', 'sample', '2026-02-20 07:49:56'),
(14, 11, 'accounting_pre', 'PRE-BUDGET FOR VOUCHER', 'desc1', '2026-02-23 03:35:05'),
(15, 11, 'budget', 'FOR PAYMENT', 'asd', '2026-02-23 04:21:29'),
(16, 11, 'accounting_pre', 'PRE-BUDGET FOR VOUCHER', 'desc1', '2026-02-23 04:21:48'),
(17, 11, 'accounting_pre', 'PRE-BUDGET FOR VOUCHER', 'desc1', '2026-02-23 04:24:42'),
(18, 11, 'accounting_post', 'POST BUDGET FOR VOUCHER', 'desc2', '2026-02-23 04:25:03'),
(19, 11, 'accounting_post', 'POST BUDGET FOR VOUCHER', 'desc2', '2026-02-23 04:29:17'),
(20, 11, 'accounting_post', 'POST BUDGET FOR VOUCHER', 'desc2', '2026-02-23 04:34:51'),
(21, 11, 'accounting_post', 'POST BUDGET FOR VOUCHER', 'desc2', '2026-02-23 04:43:59'),
(22, 11, 'accounting_post', 'POST BUDGET FOR VOUCHER', 'desc2', '2026-02-23 04:44:20'),
(23, 11, 'accounting_post', 'POST BUDGET FOR VOUCHER', 'desc2', '2026-02-23 04:45:19'),
(24, 11, 'accounting_post', 'POST BUDGET FOR VOUCHER', 'desc2', '2026-02-23 04:45:28'),
(25, 11, 'budget', 'FOR PAYMENT', 'asd', '2026-02-23 04:59:47'),
(26, 11, 'accounting_post', 'POST BUDGET FOR VOUCHER', 'desc2', '2026-02-23 05:08:07'),
(27, 11, 'budget', 'FOR PAYMENT', 'asd', '2026-02-23 05:10:22'),
(28, 11, 'accounting_post', 'POST BUDGET FOR VOUCHER', 'desc2', '2026-02-23 05:11:31'),
(29, 11, 'accounting_post', 'POST BUDGET FOR VOUCHER', 'desc2', '2026-02-23 05:11:35'),
(30, 11, 'accounting_post', 'POST BUDGET FOR VOUCHER', 'desc2', '2026-02-23 05:13:46'),
(31, 11, 'accounting_post', 'POST BUDGET FOR VOUCHER', 'desc2', '2026-02-23 05:13:52'),
(32, 11, 'accounting_pre', 'PRE-BUDGET FOR VOUCHER', 'desc1', '2026-02-23 05:13:55'),
(33, 11, 'accounting_pre', 'PRE-BUDGET FOR VOUCHER', 'desc1', '2026-02-23 05:21:12'),
(34, 11, 'accounting_post', 'POST BUDGET FOR VOUCHER', 'desc2\nDV Amount: 111111', '2026-02-23 05:29:25'),
(35, 11, 'cashier', 'For ACIC', 'asd', '2026-02-23 05:50:18'),
(36, 11, 'cashier', 'For ACIC', 'asdasdasd', '2026-02-23 05:52:03'),
(37, 11, 'cashier', 'For ACIC', 'asdasdasd', '2026-02-23 05:52:08'),
(38, 11, 'cashier', 'For ACIC', 'asdasdasd\nAmount: 1000', '2026-02-23 05:54:47'),
(39, 11, 'cashier', 'For ACIC', 'asdasdasd', '2026-02-23 06:01:17'),
(40, 11, 'cashier', 'For ACIC', 'asdasdasd', '2026-02-23 06:08:34'),
(41, 11, 'cashier', 'For ACIC', 'asdasdasd', '2026-02-23 06:08:42'),
(42, 11, 'cashier', 'For ACIC', 'asdasdasd', '2026-02-23 06:09:17'),
(43, 11, 'cashier', 'For ACIC', 'asdasdasd', '2026-02-23 06:09:30'),
(44, 11, 'cashier', 'For ACIC', 'asdasdasd', '2026-02-23 06:12:18'),
(45, 11, 'cashier', 'For ACIC', 'asdasdasd', '2026-02-23 06:12:24'),
(46, 11, 'cashier', 'For ACIC', 'asdasdasd\nAmount: 1000', '2026-02-23 06:14:52'),
(47, 11, 'budget', 'FOR PAYMENT', 'asd', '2026-02-23 06:22:43'),
(76, 20, 'procurement', 'FOR SUPPLY REVIEW', 'sample remarks', '2026-02-27 21:15:54'),
(77, 20, 'procurement', 'COMPLETED', 'okay', '2026-02-27 21:20:00'),
(78, 20, 'supply', 'PARTIAL DELIVER', 'wait', '2026-02-27 21:20:31'),
(79, 20, 'supply', 'COMPLETED', 'okay', '2026-02-27 21:20:57'),
(80, 20, 'accounting_pre', '', 'okay', '2026-02-27 21:36:42'),
(81, 20, 'budget', 'FOR PAYMENT', 'samleee', '2026-02-27 21:39:29'),
(82, 20, 'accounting_post', 'FOR VOUCHER', 'okay3\nDV Amount: 5000', '2026-02-27 21:41:17'),
(83, 20, 'budget', 'ACCOUNTS PAYABLE', 'samleee', '2026-02-27 21:44:11'),
(84, 20, 'budget', 'ACCOUNTS PAYABLE', 'samleee', '2026-02-27 21:44:55'),
(85, 20, 'budget', 'FOR ORS', 'samleee', '2026-02-27 21:45:02'),
(86, 20, 'cashier', 'For ACIC', 'okay\nAmount: 2000', '2026-02-27 22:05:04'),
(87, 20, 'cashier', 'For OR Issuance', 'sample\nAmount: 2000', '2026-02-27 22:05:16'),
(88, 20, 'cashier', 'For SDS/ASDS Approval', 'sample\nAmount: 2000', '2026-02-27 22:05:25'),
(89, 20, 'budget', 'ACCOUNTS PAYABLE', 'samleee', '2026-02-27 23:12:40'),
(90, 20, 'budget', 'ACCOUNTS PAYABLE', 'again', '2026-02-27 23:42:37'),
(91, 20, 'cashier', 'For OR Issuance', 'sample\nAmount: 2000', '2026-02-28 02:01:44'),
(92, 20, 'procurement', 'COMPLETED', 'okay', '2026-03-04 02:40:53'),
(93, 21, 'procurement', 'FOR SUPPLY REVIEW', 'sampelee', '2026-03-04 03:02:06'),
(94, 21, 'supply', 'PARTIAL DELIVER', 'apwem', '2026-03-04 03:02:35'),
(95, 21, 'accounting_pre', '', 'sample remarks', '2026-03-04 03:10:44'),
(96, 21, 'accounting_pre', '', 'receive from proponent', '2026-03-04 03:11:20'),
(97, 21, 'budget', 'ACCOUNTS PAYABLE', 'asdqwe', '2026-03-04 03:12:29'),
(98, 21, 'accounting_post', 'FOR VOUCHER', 'sample\nDV Amount: 1000', '2026-03-04 03:12:47'),
(99, 21, 'cashier', 'For ACIC', 'sample remaks\nAmount: 1000', '2026-03-04 03:14:11'),
(100, 22, 'procurement', 'FOR SUPPLY REVIEW', '', '2026-03-04 14:10:42'),
(101, 22, 'procurement', 'FOR SUPPLY REVIEW', 'sample', '2026-03-04 14:11:07'),
(102, 22, 'procurement', 'COMPLETED', 'sample', '2026-03-04 14:12:03'),
(103, 22, 'supply', 'PARTIAL DELIVER', '', '2026-03-04 14:14:14'),
(104, 22, 'supply', 'COMPLETED', '', '2026-03-04 14:14:42'),
(105, 22, 'accounting_pre', '', 'ok', '2026-03-04 14:16:07'),
(106, 22, 'accounting_pre', '', 'jkl', '2026-03-04 14:17:41'),
(107, 22, 'accounting_pre', 'COMPLETED', '', '2026-03-04 14:18:08'),
(108, 22, 'budget', 'FOR PAYMENT', '', '2026-03-04 14:31:56'),
(109, 22, 'budget', 'COMPLETED', '', '2026-03-04 14:33:06'),
(110, 22, 'accounting_post', 'FOR VOUCHER', '', '2026-03-04 14:34:52'),
(111, 22, 'accounting_post', 'FOR VOUCHER', '', '2026-03-04 14:41:49'),
(112, 22, 'accounting_post', 'COMPLETED', '', '2026-03-04 14:42:05'),
(113, 22, 'cashier', 'COMPLETED', '', '2026-03-04 14:43:19'),
(114, 23, 'procurement', 'FOR SUPPLY REVIEW', '', '2026-03-04 14:45:23'),
(115, 23, 'procurement', 'FOR SUPPLY REVIEW', 'okay', '2026-03-04 14:46:04'),
(116, 23, 'procurement', 'COMPLETED', 'okay', '2026-03-04 14:48:50'),
(117, 23, 'supply', 'PARTIAL DELIVER', '', '2026-03-04 14:49:18'),
(118, 23, 'accounting_pre', '', 'nop', '2026-03-04 14:54:08'),
(119, 23, 'accounting_pre', 'FOR VOUCHER', '', '2026-03-04 14:55:33'),
(120, 23, 'budget', 'FOR PAYMENT', '', '2026-03-04 14:56:31'),
(121, 23, 'accounting_post', 'COMPLETED', '', '2026-03-04 14:57:00'),
(122, 23, 'cashier', 'COMPLETED', '', '2026-03-04 14:58:12'),
(123, 19, 'procurement', 'COMPLETED', '', '2026-03-04 15:09:06'),
(124, 13, 'procurement', 'FOR SUPPLY REVIEW', '', '2026-03-04 15:10:25'),
(125, 13, 'procurement', 'COMPLETED', '', '2026-03-04 15:10:41'),
(126, 19, 'supply', 'COMPLETED', '', '2026-03-04 15:13:01'),
(127, 13, 'supply', 'COMPLETED', '', '2026-03-04 15:13:12'),
(128, 24, 'procurement', 'FOR SUPPLY REVIEW', '', '2026-03-04 17:56:05'),
(129, 24, 'procurement', 'COMPLETED', '', '2026-03-04 17:56:31'),
(130, 24, 'supply', 'COMPLETED', '', '2026-03-04 17:57:00'),
(131, 24, 'accounting_pre', '', 'asd', '2026-03-04 17:57:34'),
(132, 24, 'accounting_pre', 'FOR VOUCHER', '', '2026-03-04 17:58:39'),
(133, 24, 'budget', 'FOR PAYMENT', '', '2026-03-04 17:59:09'),
(134, 24, 'budget', 'COMPLETED', '', '2026-03-04 17:59:33'),
(135, 24, 'accounting_post', 'COMPLETED', '', '2026-03-04 17:59:50'),
(136, 24, 'cashier', 'COMPLETED', '', '2026-03-04 18:00:14'),
(137, 25, 'procurement', 'FOR SUPPLY REVIEW', '', '2026-03-04 18:56:06'),
(138, 25, 'procurement', 'COMPLETED', '', '2026-03-04 18:56:11'),
(139, 25, 'supply', 'PARTIAL DELIVER', '', '2026-03-04 18:56:26'),
(140, 25, 'supply', 'PARTIAL DELIVER', '', '2026-03-04 19:27:10'),
(141, 25, 'supply', 'COMPLETED', '', '2026-03-04 19:27:34'),
(142, 25, 'accounting_pre', 'FOR ORS', '', '2026-03-04 19:30:09'),
(143, 25, 'budget', 'FOR PAYMENT', '', '2026-03-04 19:36:27'),
(144, 25, 'accounting_post', 'FOR VOUCHER', '', '2026-03-04 19:37:08'),
(145, 25, 'accounting_post', 'COMPLETED', '', '2026-03-04 19:37:15'),
(146, 25, 'cashier', 'For ACIC', '', '2026-03-04 19:45:07'),
(147, 19, 'accounting_pre', 'FOR ORS', '', '2026-03-04 22:35:08'),
(175, 32, 'procurement', 'FOR SUPPLY REVIEW', '', '2026-03-05 07:17:20'),
(176, 32, 'procurement', 'COMPLETED', '', '2026-03-05 07:17:39'),
(177, 32, 'supply', 'PARTIAL DELIVER', '', '2026-03-05 07:37:11'),
(178, 32, 'accounting_pre', 'OVERRIDE EDIT', 'Force enabled update to allow processing.', '2026-03-05 08:02:44'),
(179, 32, 'accounting_pre', 'FOR ORS', '', '2026-03-05 08:02:44'),
(180, 32, 'supply', 'COMPLETED', '', '2026-03-05 08:03:06'),
(181, 32, 'accounting_pre', 'COMPLETED', '', '2026-03-05 08:03:53'),
(182, 32, 'budget', 'COMPLETED', '', '2026-03-05 08:04:07'),
(183, 32, 'accounting_post', 'COMPLETED', '', '2026-03-05 08:04:41'),
(184, 32, 'cashier', 'COMPLETED', '', '2026-03-05 08:09:42'),
(185, 33, 'procurement', 'FOR SUPPLY REVIEW', '', '2026-03-10 14:40:02'),
(186, 33, 'procurement', 'COMPLETED', '', '2026-03-10 14:40:24'),
(187, 33, 'supply', 'COMPLETED', '', '2026-03-10 14:41:01'),
(188, 33, 'accounting_pre', 'COMPLETED', '', '2026-03-10 14:41:27'),
(189, 33, 'budget', 'COMPLETED', '', '2026-03-10 14:42:02'),
(190, 33, 'accounting_post', 'COMPLETED', '', '2026-03-10 14:42:38'),
(191, 33, 'cashier', 'COMPLETED', '', '2026-03-10 14:43:03'),
(192, 34, 'procurement', 'COMPLETED', '', '2026-03-11 01:10:54'),
(193, 34, 'supply', 'COMPLETED', '', '2026-03-11 01:12:18'),
(194, 34, 'accounting_pre', 'COMPLETED', '', '2026-03-11 01:14:23'),
(195, 34, 'budget', 'COMPLETED', '', '2026-03-11 01:16:59'),
(196, 34, 'accounting_post', 'COMPLETED', '', '2026-03-11 01:20:09'),
(197, 35, 'procurement', 'COMPLETED', '', '2026-03-11 01:33:01'),
(198, 35, 'supply', 'COMPLETED', '', '2026-03-11 01:35:47'),
(199, 35, 'accounting_pre', 'COMPLETED', '', '2026-03-11 01:37:43'),
(200, 35, 'budget', 'COMPLETED', '', '2026-03-11 01:40:04'),
(201, 35, 'accounting_post', 'COMPLETED', '', '2026-03-11 01:41:29'),
(202, 35, 'cashier', 'COMPLETED', '', '2026-03-11 01:43:02'),
(203, 36, 'procurement', 'COMPLETED', '', '2026-03-11 01:52:01'),
(204, 36, 'supply', 'COMPLETED', '', '2026-03-11 01:58:00'),
(205, 37, 'procurement', 'COMPLETED', '', '2026-03-11 02:56:01'),
(206, 37, 'supply', 'COMPLETED', '', '2026-03-11 03:00:01'),
(207, 37, 'accounting_pre', 'COMPLETED', '', '2026-03-11 03:05:01'),
(208, 37, 'budget', 'COMPLETED', '', '2026-03-11 03:12:01'),
(209, 37, 'accounting_post', 'COMPLETED', '', '2026-03-11 05:22:42'),
(210, 38, 'procurement', 'FOR SUPPLY REVIEW', '', '2026-03-11 05:51:01'),
(211, 38, 'procurement', 'COMPLETED', '', '2026-03-11 05:52:00'),
(212, 38, 'supply', 'COMPLETED', '', '2026-03-11 05:56:01'),
(213, 38, 'accounting_pre', 'COMPLETED', '', '2026-03-11 06:03:01'),
(214, 38, 'budget', 'COMPLETED', '', '2026-03-11 06:10:01'),
(215, 39, 'procurement', 'COMPLETED', '', '2026-03-11 06:32:28'),
(216, 39, 'supply', 'COMPLETED', '', '2026-03-11 06:34:52'),
(217, 40, 'procurement', 'COMPLETED', '', '2026-03-12 01:46:29'),
(218, 41, 'procurement', 'COMPLETED', '', '2026-03-12 07:01:06'),
(219, 41, 'procurement', 'FOR SUPPLY REVIEW', '', '2026-03-12 07:01:13'),
(220, 41, 'procurement', 'COMPLETED', '', '2026-03-12 07:02:29'),
(221, 41, 'supply', 'COMPLETED', '', '2026-03-12 07:07:00'),
(222, 41, 'accounting_pre', 'COMPLETED', '', '2026-03-12 07:13:32'),
(223, 41, 'budget', 'FOR PAYMENT', '', '2026-03-13 02:11:23'),
(224, 42, 'procurement', 'FOR SUPPLY REVIEW', '', '2026-03-13 04:01:35'),
(225, 42, 'procurement', 'COMPLETED', '', '2026-03-13 04:01:54'),
(226, 42, 'supply', 'PARTIAL DELIVER', '', '2026-03-13 04:02:15'),
(227, 42, 'supply', 'COMPLETED', '', '2026-03-13 05:06:31'),
(228, 43, 'procurement', 'FOR SUPPLY REVIEW', '', '2026-03-13 05:21:09'),
(229, 43, 'procurement', 'COMPLETED', '', '2026-03-13 05:21:52'),
(230, 43, 'procurement', 'COMPLETED', '', '2026-03-13 05:24:57'),
(231, 43, 'procurement', 'COMPLETED', '', '2026-03-13 05:25:23'),
(232, 43, 'procurement', 'COMPLETED', '', '2026-03-13 05:26:18'),
(233, 43, 'procurement', 'FOR SUPPLY REVIEW', '', '2026-03-13 05:27:02'),
(234, 43, 'procurement', 'COMPLETED', '', '2026-03-13 05:28:10'),
(235, 43, 'supply', 'PARTIAL DELIVER', '', '2026-03-13 05:31:47'),
(236, 43, 'supply', 'PARTIAL DELIVER', '', '2026-03-13 05:33:12'),
(237, 43, 'supply', 'PARTIAL DELIVER', '', '2026-03-14 05:04:53'),
(238, 43, 'supply', 'PARTIAL DELIVER', 'waiting', '2026-03-14 05:25:51'),
(239, 43, 'supply', 'COMPLETED', 'okay', '2026-03-14 05:26:16'),
(240, 43, 'supply', 'PARTIAL DELIVER', 'okay', '2026-03-14 05:32:20'),
(241, 43, 'supply', 'COMPLETED', 'okay', '2026-03-14 05:34:57'),
(242, 43, 'supply', 'PARTIAL DELIVER', 'okay', '2026-03-14 05:35:49'),
(243, 44, 'procurement', 'FOR SUPPLY REVIEW', '', '2026-03-14 11:41:03'),
(244, 44, 'procurement', 'COMPLETED', '', '2026-03-14 11:43:13'),
(245, 44, 'supply', 'PARTIAL DELIVER', '', '2026-03-14 11:44:59'),
(246, 44, 'supply', 'PARTIAL DELIVER', '', '2026-03-14 11:46:20'),
(247, 44, 'supply', 'PARTIAL DELIVER', 'note', '2026-03-14 11:46:43'),
(248, 44, 'supply', 'COMPLETED', 'okay', '2026-03-14 11:48:36'),
(249, 45, 'procurement', 'FOR SUPPLY REVIEW', '', '2026-03-15 05:01:50'),
(250, 45, 'procurement', 'COMPLETED', '', '2026-03-15 05:02:03'),
(251, 45, 'supply', 'PARTIAL DELIVER', '', '2026-03-15 05:02:36'),
(252, 45, 'procurement', 'COMPLETED', 'okay', '2026-03-15 05:02:53'),
(253, 45, 'procurement', 'FOR SUPPLY REVIEW', 'okay', '2026-03-15 05:08:55'),
(254, 45, 'supply', 'PARTIAL DELIVER', '', '2026-03-15 05:34:36'),
(255, 45, 'procurement', 'FOR SUPPLY REVIEW', 'nop', '2026-03-15 05:35:15'),
(256, 45, 'supply', 'COMPLETED', '', '2026-03-15 05:36:34'),
(257, 45, 'accounting_pre', 'COMPLETED', 'DV Amount: 8000', '2026-03-15 05:44:43'),
(258, 45, 'budget', 'ACCOUNTS PAYABLE', '', '2026-03-15 05:49:21'),
(259, 45, 'budget', 'COMPLETED', '', '2026-03-15 05:50:08'),
(260, 45, 'accounting_post', 'COMPLETED', 'DV Amount: 1000', '2026-03-15 05:52:28'),
(261, 45, 'cashier', 'For OR Issuance', 'Amount: 100', '2026-03-15 05:53:57'),
(262, 44, 'accounting_pre', 'COMPLETED', '', '2026-03-15 07:32:38'),
(263, 44, 'budget', 'COMPLETED', '', '2026-03-15 07:49:28'),
(264, 44, 'accounting_post', 'COMPLETED', '', '2026-03-15 08:22:15'),
(265, 44, 'cashier', 'COMPLETED', '', '2026-03-15 08:25:19'),
(266, 47, 'procurement', 'FOR SUPPLY REVIEW', '', '2026-03-16 03:02:38'),
(267, 47, 'procurement', 'COMPLETED', '', '2026-03-16 03:02:47'),
(268, 47, 'supply', 'PARTIAL DELIVER', '', '2026-03-16 03:07:30'),
(269, 47, 'supply', 'COMPLETED', '', '2026-03-16 03:08:14'),
(270, 47, 'accounting_pre', 'FOR ORS', '', '2026-03-16 03:09:45'),
(271, 47, 'accounting_pre', 'FOR VOUCHER', '', '2026-03-16 03:09:49'),
(272, 47, 'accounting_pre', 'COMPLETED', '', '2026-03-16 03:10:29'),
(273, 47, 'budget', 'ACCOUNTS PAYABLE', '', '2026-03-16 03:11:26'),
(274, 47, 'budget', 'COMPLETED', '', '2026-03-16 03:12:19'),
(275, 47, 'accounting_post', 'COMPLETED', 'DV Amount: 9000', '2026-03-16 03:15:08'),
(276, 47, 'cashier', 'For ACIC', 'Amount: 7890', '2026-03-16 03:16:33'),
(277, 48, 'procurement', 'COMPLETED', '', '2026-03-17 06:55:56'),
(278, 48, 'supply', 'COMPLETED', '', '2026-03-17 07:03:36'),
(279, 48, 'accounting_pre', 'FOR ORS', 'DV Amount: 600', '2026-03-17 07:07:53'),
(280, 48, 'accounting_pre', 'COMPLETED', '', '2026-03-17 07:08:37'),
(281, 48, 'budget', 'ACCOUNTS PAYABLE', '', '2026-03-17 07:10:01'),
(282, 48, 'budget', 'ACCOUNTS PAYABLE', '', '2026-03-17 07:10:53'),
(283, 48, 'budget', 'COMPLETED', '', '2026-03-17 07:11:25'),
(284, 48, 'accounting_post', 'COMPLETED', '', '2026-03-17 07:12:32'),
(285, 48, 'cashier', 'COMPLETED', '', '2026-03-17 07:13:49'),
(286, 49, 'procurement', 'FOR SUPPLY REVIEW', '', '2026-03-17 15:38:17'),
(287, 49, 'supply', 'PARTIAL DELIVERY', '', '2026-03-17 16:47:45'),
(288, 49, 'accounting_pre', 'FOR ORS', '', '2026-03-17 17:29:43'),
(289, 49, 'accounting_pre', 'FOR ORS', 'DV Amount: 8000', '2026-03-17 17:30:01'),
(290, 49, 'budget', 'FOR PAYMENT', '', '2026-03-17 17:46:39'),
(291, 49, 'budget', 'ACCOUNTS PAYABLE', '', '2026-03-17 17:47:32'),
(292, 49, 'accounting_post', 'FOR VOUCHER', '', '2026-03-17 17:47:44'),
(293, 49, 'cashier', 'COMPLETED', '', '2026-03-17 17:49:21'),
(294, 50, 'procurement', 'FOR SUPPLY REVIEW', '', '2026-03-17 17:50:21'),
(295, 50, 'supply', 'COMPLETED', '', '2026-03-17 17:50:37'),
(296, 50, 'accounting_pre', 'FOR ORS', '', '2026-03-17 17:50:51'),
(297, 50, 'budget', 'FOR PAYMENT', '', '2026-03-17 17:51:13'),
(298, 50, 'accounting_post', 'FOR VOUCHER', '', '2026-03-17 17:51:40');

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
(10, 'admin', '$2a$12$XhSdO3SPRj0NL2b2Ln1v8uzgYAQEopTZbNSEl/z7BQvud/YtrqWeS', 1, NULL, NULL, NULL, '2026-03-18 03:20:59', '192.168.100.8', '2026-02-10 06:04:17', NULL),
(12, 'procurement', '$2y$10$QclzMbbCTh0V3CoawlNNKOKV4SwirQglkHhk6t2DaKyJRsEGKw9Vi', 3, NULL, 'voi1h8b3fqg8kga50qna8i34ko', '2026-03-05 09:51:03', '2026-03-18 03:21:11', '192.168.100.105', '2026-02-10 06:04:17', NULL),
(13, 'supply', '$2a$12$XhSdO3SPRj0NL2b2Ln1v8uzgYAQEopTZbNSEl/z7BQvud/YtrqWeS', 4, NULL, 'ekrk5vsvck4g5u4qq32hffcp2e', '2026-03-05 09:51:03', '2026-03-17 23:38:03', '::1', '2026-02-10 06:04:17', NULL),
(14, 'accounting', '$2a$12$XhSdO3SPRj0NL2b2Ln1v8uzgYAQEopTZbNSEl/z7BQvud/YtrqWeS', 5, NULL, 'mm7dcl78j23eb67dav77vm290p', '2026-03-05 09:51:03', '2026-03-18 00:47:55', '::1', '2026-02-10 06:04:17', NULL),
(15, 'budget', '$2a$12$XhSdO3SPRj0NL2b2Ln1v8uzgYAQEopTZbNSEl/z7BQvud/YtrqWeS', 6, NULL, 'is20s2gsenns7n8dgjp437e4mg', '2026-03-05 09:05:49', '2026-03-18 01:31:42', '::1', '2026-02-10 06:04:17', NULL),
(16, 'cashier', '$2y$10$dyGEN9guUrlc2mUANfKlouzI7yx2gdG5SOyfsCgKPBuJwMrjwgp6S', 7, NULL, 'oiv0qv5qvmjf84o4co8334ghap', '2026-03-05 09:51:03', '2026-03-18 03:24:52', '192.168.100.8', '2026-02-10 06:04:17', NULL),
(18, 'supplier', '$2y$10$Mx9fWEWzt0gLz5Sn/bD9KOxf3lGiSXVjeOPNjDQY2vizxI93HVQiu', 2, 3, NULL, NULL, '2026-03-05 14:29:12', '::1', '2026-02-10 07:05:35', NULL),
(21, 'alphanum0001', '$2y$10$tpzyX0cWN3t51knWrTMbruq6QGW8CySC3vJV1W45g0BFoL0aBlUpe', 2, 6, 'k5fq9ue9tlfmaar6mq1i9fb6cv', '2026-03-05 09:49:04', '2026-03-05 03:20:52', '::1', '2026-02-27 20:44:26', NULL),
(23, 'sample cashier', '$2y$10$FWnyLpEBowxNhXNpBcYpm..0XLNQCJK0NjkUtAEdVsb0kVpK2e.0G', 7, NULL, NULL, NULL, NULL, NULL, '2026-03-03 00:47:47', NULL),
(25, 'alphanum0002', '$2y$10$DiU4vmizH1ERMzoJ3uAxqeJrrndNbnk6Tr73y9yew3vdyD5VNswnC', 2, 7, NULL, NULL, '2026-03-18 00:38:25', '::1', '2026-03-10 14:23:45', NULL);

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
(10, 1, '2026-03-17 06:44:42'),
(12, 0, '2026-03-14 11:39:45'),
(13, 0, '2026-03-14 11:39:51'),
(14, 0, '2026-03-14 11:40:02'),
(15, 0, '2026-03-14 11:40:08'),
(16, 0, '2026-03-14 11:40:20');

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
(1, 16, 'rs3qhkfrui9n9riahs30h4ueus', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-05 02:03:48', '2026-03-05 02:58:06', NULL),
(2, 14, 'pi06vbqf898ks814b9lln4fl1s', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-05 02:03:48', '2026-03-05 13:59:15', NULL),
(3, 13, 'upld1efivriqi4jt9cgd84kgv1', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-05 02:03:48', '2026-03-05 12:42:52', '2026-03-12 05:11:25'),
(4, 10, '8f1d9ia7859rhh7snjmb1bkt4n', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-05 02:03:49', '2026-03-05 02:03:52', '2026-03-05 02:03:53'),
(5, 1, 'hsvdo9e4kvnh01n488v56gbj1a', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-05 02:03:49', '2026-03-05 02:11:07', NULL),
(23, 18, 'tqv4bkfnrdtr86vdohbfcj6jfr', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-05 02:05:33', '2026-03-05 02:50:14', '2026-03-05 02:50:16'),
(335, 1, 's5c8mrcctuhdnlhj7t2jljh5mb', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-05 02:11:49', '2026-03-05 02:11:51', '2026-03-05 02:12:01'),
(419, 1, 'rpg20ebd5q22tpvu44dgugh9r6', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-05 02:13:10', '2026-03-05 12:37:20', NULL),
(2481, 10, 'iefnk2ika8513c2ddp5rgl3ovc', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-05 02:49:55', '2026-03-05 05:53:22', NULL),
(2609, 18, '657osa001i4r9fhko3u40fu6ka', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-05 02:50:30', '2026-03-05 03:01:21', '2026-03-05 03:01:22'),
(3152, 18, 'fs50cpvgl8q37nkppjmfajve2e', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-05 03:01:31', '2026-03-05 05:55:34', NULL),
(21161, 18, 'eumabcpruum1el613d0e2feg6v', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-05 06:29:12', '2026-03-05 08:11:43', NULL),
(29235, 16, '08obv445gh82f76jqf80grlcss', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-05 07:07:37', '2026-03-06 01:49:07', NULL),
(51762, 10, 'bsaik3npdv898rbr9377blejae', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-05 08:16:57', '2026-03-06 01:15:35', NULL),
(77354, 12, '6bthttjegu5tm95cgsdg4uvbot', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-10 14:23:00', '2026-03-10 23:29:39', NULL),
(77395, 25, 'edj49c1ufgaqsr3ffjcrn3j937', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-10 14:23:45', '2026-03-10 23:30:48', '2026-03-10 23:30:51'),
(77509, 13, '0fl1omg201bcjtl8cjfbb70pee', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-10 14:26:05', '2026-03-10 23:29:42', '2026-03-12 05:12:40'),
(77550, 14, 'uq39rjl1v6mpaoh19patnr9lei', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-10 14:26:32', '2026-03-10 23:29:48', '2026-03-10 23:29:51'),
(77572, 15, 'u64nv3v8k1o2g6jf30gm6sgh1t', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-10 14:26:44', '2026-03-10 23:29:59', '2026-03-10 23:30:01'),
(77622, 16, 'f9jar3pkbsemm7lpvgnoggk5bf', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-10 14:27:11', '2026-03-10 23:30:08', '2026-03-10 23:30:08'),
(81195, 12, '6hm90gho6si872i15ps7bo690i', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-11 00:07:35', '2026-03-11 06:51:01', NULL),
(81255, 13, '45n3on0up2of9k0f0q56onsb75', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-11 00:08:22', '2026-03-11 06:50:57', NULL),
(81279, 14, 'j8hrjr6r5mc0ondbno5nup95kd', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-11 00:08:36', '2026-03-11 06:51:01', NULL),
(81313, 15, '46e7oukpfkg83pplvbe71d36j9', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-11 00:08:53', '2026-03-11 00:21:45', NULL),
(81330, 16, '4gk74es7efs14shk8465n2q2rq', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-11 00:08:59', '2026-03-11 06:51:02', NULL),
(81434, 25, '0tr3eocpeq3v7mg4v8cs6re4hg', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-11 00:09:34', '2026-03-12 01:40:32', NULL),
(86671, 10, '15lmnheddhjtbm2vuojhpq4cuv', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-11 00:53:25', '2026-03-11 05:19:01', NULL),
(89477, 16, 'el3l2236shq1edphlio8ki58lj', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-11 01:14:53', '2026-03-11 01:15:52', '2026-03-11 01:15:52'),
(89817, 15, '1gh9cp5ijmvth1riibg2drimig', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-11 01:15:58', '2026-03-11 06:50:49', NULL),
(146961, 12, '0bh8nl19qe62bqoehpc11c055i', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-12 01:28:36', '2026-03-13 05:56:57', NULL),
(147100, 13, 'j16ihunu6b8sl915mrltr9bjha', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-12 01:29:52', '2026-03-13 06:04:18', NULL),
(147132, 14, 'ahmrjjfght70khnnvq2j206s4j', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-12 01:30:06', '2026-03-13 06:07:00', NULL),
(147156, 15, 'l72jqoib13jtsq7d3sejdno2fc', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-12 01:30:14', '2026-03-12 09:48:25', NULL),
(147178, 16, 'r9pehqjp70vupkffg3cqm2fgo9', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-12 01:30:21', '2026-03-12 10:39:43', NULL),
(158906, 10, 'ln0sikp6fu04bvjjfn0klpetca', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-12 02:18:31', '2026-03-12 12:17:11', NULL),
(160852, 25, 'o8ruia9ldqkt1q2ro9f3hcb4qk', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-12 02:38:37', '2026-03-12 07:12:38', NULL),
(205326, 15, 'eturfj5qngj5366j1cd3p9ep37', NULL, NULL, 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36', '2026-03-12 11:55:46', '2026-03-13 07:31:07', NULL),
(206123, 16, 'j5ipaepjpf7t8a5cuhusi9mgrv', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-12 12:15:55', '2026-03-12 12:17:12', NULL),
(207625, 15, 'pgf07llp7ogj7j2552i929o3sm', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-13 00:28:04', '2026-03-13 00:47:19', NULL),
(225576, 16, 'rt7b0th7mppf6ph5o5l4e124ea', NULL, NULL, 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36', '2026-03-13 05:54:15', '2026-03-13 07:53:10', NULL),
(225583, 25, '3ar8k891qv4riubfsgrjj2cbf9', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-13 05:54:31', '2026-03-13 08:32:07', '2026-03-13 08:32:10'),
(225606, 10, 'b56g6fdf1fsas260rcn97ukj3u', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-13 05:55:08', '2026-03-13 06:08:44', NULL),
(225716, 12, 'aj6rpb3uk59s5ngcc4rnh3knp4', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-13 05:58:57', '2026-03-13 05:59:10', NULL),
(225842, 12, 'et01lah4fsh38svbvghhtkc3mo', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-13 06:03:56', '2026-03-13 06:04:14', NULL),
(226128, 12, 'rq791qi2s53erk82i0ljjb1v1t', NULL, NULL, 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36', '2026-03-13 07:53:43', '2026-03-13 07:57:04', NULL),
(226172, 12, 'qn46pcv7d3gp6d4a3efmec62tc', NULL, NULL, 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36', '2026-03-13 08:04:08', '2026-03-13 08:13:01', NULL),
(226317, 12, 'ukpfgtqme4la4gkremhbhi69ga', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-13 08:13:30', '2026-03-13 08:29:59', NULL),
(226628, 10, 'sujfjh66q4u0fuj5kurj0t3a66', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-13 12:45:48', '2026-03-13 12:59:30', NULL),
(226782, 12, '0ql22fkk87ndutvdao6a7pvqmg', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-14 00:50:16', '2026-03-14 01:20:27', '2026-03-14 01:20:28'),
(226935, 10, 'cdd0mou9ej3358g81j2d4ctu67', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-14 01:05:06', '2026-03-14 01:25:25', NULL),
(227690, 12, 'p4p3qglst5k6e2iv5293kmurad', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-14 01:24:08', '2026-03-14 01:25:06', NULL),
(227705, 12, '9448q2hk4ajt5fhk0iknu0tu6q', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-14 04:49:39', '2026-03-14 05:03:50', '2026-03-14 05:03:50'),
(228067, 13, 'hf842v91vafqlj1hbv7nm06qmc', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-14 05:04:01', '2026-03-14 05:32:35', NULL),
(228333, 12, '2i1m30t98nj3ftoar7fmgrkuj9', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-14 05:32:46', '2026-03-14 05:34:36', '2026-03-14 05:34:37'),
(228486, 13, 'rh3b6t0qfg9ru0i5cmuq88aemi', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-14 05:34:44', '2026-03-14 06:36:53', NULL),
(228617, 25, '3tksaipoklkv9814gisamke82b', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-14 10:34:51', '2026-03-14 10:34:56', NULL),
(228626, 12, '8jqoj5r43degi7ptl42i2l4d3v', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-14 10:35:26', '2026-03-14 12:19:00', NULL),
(228636, 13, 'jbfu3r643sei89lr2eai177j7o', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-14 10:35:33', '2026-03-15 00:44:14', NULL),
(228646, 14, 'j7t1e5ouiibfncp1i2l0jn1g8i', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-14 10:35:37', '2026-03-14 23:47:51', NULL),
(228656, 15, 'k27gk352tkiuq6vj7hi1qa0u7h', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-14 10:35:42', '2026-03-15 00:44:10', NULL),
(228677, 16, '6v56e8us52nggb9rdu8ufllq4n', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-14 10:36:03', '2026-03-15 01:18:18', NULL),
(228690, 10, 'dkulhqhu7ciqr5e0rp7hle97pc', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-14 10:36:20', '2026-03-14 10:36:24', NULL),
(239514, 12, 'u2d59ele6cpfb7vp1huc2forba', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-14 12:19:10', '2026-03-14 23:47:53', NULL),
(295632, 12, '75dc3f5qjdokubldi1m025mloo', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-15 04:25:04', '2026-03-15 08:28:29', NULL),
(295652, 13, 'ddi5uv66s7pshhc867r7m6nu9j', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-15 04:25:17', '2026-03-15 08:28:30', NULL),
(295685, 14, 'cvs5lsv5htko6jdgtavra1omlt', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-15 04:25:31', '2026-03-15 07:33:58', NULL),
(295710, 15, '71ao3i55a0rbse1e3evuknfglf', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-15 04:25:37', '2026-03-15 08:28:15', NULL),
(295771, 16, '7920cogs4t8el5c0urps9372l4', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-15 04:25:51', '2026-03-15 08:28:25', NULL),
(322343, 14, 'qrn9p8fqd4dkklbbansa616m6m', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-15 07:46:35', '2026-03-15 08:28:29', NULL),
(332048, 12, '204j52o3nqolibhjrn9dicskpg', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-15 20:45:46', '2026-03-15 21:29:29', '2026-03-15 21:29:31'),
(332180, 13, 'qlfdjmeiakggr71la2p133k5fd', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-15 20:47:21', '2026-03-15 21:29:57', '2026-03-15 21:29:59'),
(333436, 12, '82n7as9o50ovfgn5nv7r0f71ad', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-15 21:31:01', '2026-03-15 23:45:13', NULL),
(333455, 12, 'get5qe5mgktgnohr9r8l4eced2', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-15 21:31:13', '2026-03-15 21:31:38', '2026-03-15 21:31:40'),
(333499, 14, 'fcrad5dar8mceuep39nlgcav7b', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-15 21:31:34', '2026-03-15 23:45:10', NULL),
(333536, 13, 'o5or1nngtg9lk4cio4nlcuu16l', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-15 21:31:45', '2026-03-16 06:35:42', NULL),
(333594, 15, 'k792gkk1vhthpkqhhavi4b9f2d', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-15 21:32:02', '2026-03-15 23:45:18', NULL),
(333631, 16, 'hne4lj0q0aqv16co7qb78hdiv6', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-15 21:32:11', '2026-03-16 02:32:36', NULL),
(343525, 16, 'fcgebfg63d86skobur79r9dou6', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-16 02:54:14', '2026-03-17 06:41:14', NULL),
(343626, 10, '9l54haorp6ctuupue958ht67aq', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-16 02:54:57', '2026-03-16 02:55:56', NULL),
(343805, 25, 'fv8ln92ti72rcti79nf4lchdg7', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-16 02:56:10', '2026-03-16 02:56:14', NULL),
(343886, 12, '6gsnivp8b5c911258sbahdbkis', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-16 02:56:38', '2026-03-17 09:06:22', NULL),
(343933, 14, 'q1nrth1vsv40v7c2gmf6c8hq6o', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-16 02:56:49', '2026-03-16 04:07:57', NULL),
(344145, 15, 'a8mji4rk3ulridktmf261qa422', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-16 02:57:32', '2026-03-16 07:17:58', NULL),
(357594, 274, '6bgti5pvjgsg7ion9t5msfv8da', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-16 03:35:08', '2026-03-16 05:43:31', NULL),
(371652, 10, '8h6jbve2j0a0jjb8249rt1i10o', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-17 06:41:48', '2026-03-17 06:47:02', NULL),
(372603, 13, 'g5i6n2h8b0apc25k046stforlg', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-17 06:58:21', '2026-03-17 09:06:27', NULL),
(372618, 15, 'q0m0ku0hmhisrhib19bns2mo4v', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-17 06:58:27', '2026-03-17 10:48:41', NULL),
(373949, 14, 'mbg9s61jpa3g13si5mog2j0bk2', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-17 07:04:24', '2026-03-17 09:06:29', NULL),
(376338, 16, '5gclihtceakg88ac7if31msll1', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-17 07:12:02', '2026-03-17 09:05:46', NULL),
(376945, 25, '5uk613e26n28ahcbkb82kfad2j', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-17 07:13:32', '2026-03-17 07:14:21', NULL),
(379762, 12, 'nv16krp8hgn8g2qldn8ie8djhq', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-17 12:51:36', '2026-03-17 13:39:12', NULL),
(379775, 13, '20f2018j6f471b3l94r1evkbdj', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-17 12:51:41', '2026-03-17 13:39:29', NULL),
(379811, 14, 'l4g0adijmcqpu0q8od3rafeg18', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-17 12:51:58', '2026-03-17 13:39:16', NULL),
(379836, 15, '6mfts9am9oik870dlbs27t0nnk', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-17 12:52:04', '2026-03-17 13:39:27', NULL),
(379871, 16, '1fhheb03ntd7etdeo66qjb708v', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-17 12:52:12', '2026-03-18 00:54:47', NULL),
(379970, 25, 'hmqhuuam231ujmev5u2ph7tfmo', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-17 12:52:30', '2026-03-17 13:36:49', NULL),
(381089, 10, 'jv3m6nlud3734mm66uk59j6b6s', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-17 12:58:11', '2026-03-17 12:58:27', NULL),
(390846, 12, 'pjqdq2ojg127d9qi0jntvg28rd', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-17 15:37:33', '2026-03-18 00:54:27', NULL),
(390886, 13, '5go9akaqtumqoie100uv064srs', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-17 15:38:03', '2026-03-18 00:29:02', NULL),
(391744, 25, 'oednj6i2jkgbu1ck387o8ga8hu', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-17 16:38:11', '2026-03-17 16:38:11', NULL),
(391781, 25, 'jd0rae4cetv0n548i7b6qc9m9v', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-17 16:38:25', '2026-03-17 16:38:29', NULL),
(392042, 10, 'pbksmt0lllegand4lta7s7a0of', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-17 16:42:18', '2026-03-17 16:46:46', NULL),
(392839, 14, '4ddrbe94km28vtimi5u7p00jqs', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-17 16:47:55', '2026-03-18 00:54:46', NULL),
(398522, 15, 'nv0u80eh5cd5n1b3fnbacqksb7', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-17 17:31:42', '2026-03-18 00:54:35', NULL),
(406637, 10, 'r40r8b6mhrdqudv982fspq51es', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-17 18:07:19', '2026-03-17 18:08:08', NULL),
(414901, 10, 'uf6nllhlplpjn3is428mqbjeb0', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-17 19:20:59', '2026-03-17 19:24:33', '2026-03-17 19:24:33'),
(414921, 12, 'li2h6jqgp1mms9gvrb0082r9ab', NULL, NULL, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2026-03-17 19:21:11', '2026-03-17 19:22:34', NULL),
(415195, 16, 'mk1h7gbc3c17u2ioevar1nt6gb', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-17 19:24:52', '2026-03-17 19:26:15', NULL);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=403;

--
-- AUTO_INCREMENT for table `department_notifications`
--
ALTER TABLE `department_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=390;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `transaction_updates`
--
ALTER TABLE `transaction_updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=299;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=417934;

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
