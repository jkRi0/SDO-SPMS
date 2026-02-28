-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 28, 2026 at 04:16 AM
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
  `ip` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `target_type`, `target_id`, `details`, `ip`, `created_at`) VALUES
(1, 18, 'login', 'user', 18, 'Successful login', '::1', '2026-02-23 01:53:03'),
(2, 18, 'login', 'user', 18, 'Successful login', '::1', '2026-02-23 01:56:47'),
(3, 18, 'login', 'user', 18, 'Successful login', '::1', '2026-02-23 02:51:28'),
(4, 10, 'update_user', 'user', 12, '{\"username\":\"procurement@deped.gov\",\"role_id\":3,\"supplier_id\":null}', '::1', '2026-02-23 02:56:54'),
(5, 12, 'login', 'user', 12, 'Successful login', '::1', '2026-02-23 02:57:18'),
(6, 10, 'update_user', 'user', 12, '{\"username\":\"procurement@deped.gov\",\"role_id\":3,\"supplier_id\":null}', '::1', '2026-02-23 02:58:06'),
(7, 18, 'login', 'user', 18, 'Successful login', '::1', '2026-02-23 02:59:05'),
(8, 15, 'login', 'user', 15, 'Successful login', '::1', '2026-02-23 03:36:15'),
(9, 16, 'login', 'user', 16, 'Successful login', '::1', '2026-02-23 04:32:47'),
(10, 12, 'login', 'user', 12, 'Successful login', '::1', '2026-02-23 04:34:13'),
(11, 18, 'login', 'user', 18, 'Successful login', '::1', '2026-02-23 05:47:31'),
(12, 10, 'login', 'user', 10, 'Successful login', '::1', '2026-02-24 00:47:17'),
(13, 18, 'login', 'user', 18, 'Successful login', '::1', '2026-02-24 00:53:09'),
(14, 10, 'login', 'user', 10, 'Successful login', '::1', '2026-02-24 00:53:23'),
(15, 10, 'login', 'user', 10, 'Successful login', '::1', '2026-02-24 01:33:30'),
(16, 18, 'login', 'user', 18, 'Successful login', '::1', '2026-02-24 01:33:42'),
(17, 12, 'login', 'user', 12, 'Successful login', '::1', '2026-02-24 01:34:33'),
(18, 13, 'login', 'user', 13, 'Successful login', '::1', '2026-02-24 01:34:48'),
(19, 14, 'login', 'user', 14, 'Successful login', '::1', '2026-02-24 01:35:04'),
(20, 16, 'login', 'user', 16, 'Successful login', '::1', '2026-02-24 01:35:42'),
(21, 18, 'login', 'user', 18, 'Successful login', '::1', '2026-02-24 18:52:11'),
(22, 12, 'login', 'user', 12, 'Successful login', '::1', '2026-02-24 23:30:29'),
(23, 15, 'login', 'user', 15, 'Successful login', '::1', '2026-02-24 23:56:12'),
(24, 10, 'login', 'user', 10, 'Successful login', '::1', '2026-02-26 01:06:31'),
(25, 18, 'login', 'user', 18, 'Successful login', '::1', '2026-02-26 01:06:44'),
(26, 12, 'login', 'user', 12, 'Successful login', '::1', '2026-02-26 01:06:56'),
(27, 13, 'login', 'user', 13, 'Successful login', '::1', '2026-02-26 01:07:10'),
(28, 14, 'login', 'user', 14, 'Successful login', '::1', '2026-02-26 01:07:32'),
(29, 15, 'login', 'user', 15, 'Successful login', '::1', '2026-02-26 01:07:54'),
(30, 16, 'login', 'user', 16, 'Successful login', '::1', '2026-02-26 01:08:36'),
(31, 18, 'login', 'user', 18, 'Successful login', '::1', '2026-02-26 22:28:24'),
(32, 12, 'login', 'user', 12, 'Successful login', '::1', '2026-02-26 22:28:43'),
(33, 14, 'login', 'user', 14, 'Successful login', '::1', '2026-02-26 22:29:25'),
(34, 12, 'login', 'user', 12, 'Successful login', '::1', '2026-02-26 22:29:41'),
(35, 14, 'login', 'user', 14, 'Successful login', '::1', '2026-02-26 22:30:02'),
(36, 16, 'login', 'user', 16, 'Successful login', '::1', '2026-02-26 22:30:27'),
(37, 16, 'login', 'user', 16, 'Successful login', '::1', '2026-02-27 02:27:43'),
(38, 18, 'login', 'user', 18, 'Successful login', '::1', '2026-02-27 20:49:26'),
(39, 12, 'login', 'user', 12, 'Successful login', '::1', '2026-02-27 21:04:59'),
(40, 13, 'login', 'user', 13, 'Successful login', '::1', '2026-02-27 21:16:19'),
(41, 14, 'login', 'user', 14, 'Successful login', '::1', '2026-02-27 21:22:22'),
(42, 15, 'login', 'user', 15, 'Successful login', '::1', '2026-02-27 21:37:24'),
(43, 15, 'login', 'user', 15, 'Successful login', '::1', '2026-02-27 21:38:20'),
(44, 16, 'login', 'user', 16, 'Successful login', '::1', '2026-02-27 21:39:59'),
(45, 16, 'login', 'user', 16, 'Successful login', '::1', '2026-02-27 23:17:31'),
(46, 18, 'login', 'user', 18, 'Successful login', '::1', '2026-02-28 00:13:32'),
(47, 10, 'login', 'user', 10, 'Successful login', '::1', '2026-02-28 02:59:44');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `user_id`, `role`, `type`, `message`, `created_at`) VALUES
(1, 12, 'procurement', 'Other', 'sample2', '2026-02-14 10:51:56'),
(2, 18, 'supplier', 'Suggestion', 'sample 3', '2026-02-14 10:52:29');

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
(11, 6, 20, 'Payment status update', 'Your PO asd has been updated by Cashier.', 'transaction_view.php?id=20', 0, 0, '2026-02-28 11:05:20');

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
(6, 'alpha num', '2026-02-27 20:44:26', 'alphanum0001@gmail.com');

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
  `cashier_payment_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `supplier_id`, `po_number`, `program_title`, `po_type`, `proponent`, `coverage_start`, `coverage_end`, `expected_date`, `amount`, `created_at`, `proc_status`, `proc_remarks`, `proc_date`, `supply_status`, `supply_delivery_receipt`, `supply_sales_invoice`, `supply_remarks`, `supply_date`, `acct_pre_status`, `acct_pre_remarks`, `acct_pre_date`, `budget_dv_number`, `budget_dv_date`, `budget_status`, `budget_demandability`, `budget_remarks`, `acct_post_status`, `acct_post_remarks`, `acct_post_date`, `cashier_status`, `cashier_remarks`, `cashier_or_number`, `cashier_or_date`, `cashier_landbank_ref`, `cashier_payment_date`) VALUES
(10, 3, '2026-01-03', 'qwerty', 'Transpo/venue', 'jeff', '2026-02-18', '2026-02-18', '2026-02-19', 40000.00, '2026-02-19 01:53:16', 'FOR SUPPLY REVIEW', 'brrt', '2026-02-20', 'COMPLETED', 'asdasdqwe', 'asd', 'sample', '2026-02-20', 'PRE-BUDGET FOR VOUCHER', 'Completed', '2026-02-19', '2026-01-04', '2026-02-11', 'FOR ORS', 'DUE DEMANDABLE', '', 'POST BUDGET FOR VOUCHER', 'completed', '2026-02-19', 'FOR OR INSUANCE', '', '', '0000-00-00', '', '0000-00-00'),
(11, 3, 'asdasd', 'asdasdasdasd', 'Supplies', 'qweqweqwe', '2026-02-19', '2026-02-26', NULL, 333.00, '2026-02-20 03:05:34', 'COMPLETED', 'okay 123', '2026-02-20', 'PARTIAL DELIVER', 'asd', 'asd', 'sample', '2026-02-20', 'PRE-BUDGET FOR VOUCHER', 'desc1', '2026-02-23', '123', '0000-00-00', 'FOR PAYMENT', 'Not Yet Due and Demandable', 'asd', 'POST BUDGET FOR VOUCHER', 'desc2\nDV Amount: 111111', '2026-02-23', 'For ACIC', 'asdasdasd', 'asdqwe', '0000-00-00', '1000', '0000-00-00'),
(13, 3, 'asdlklk123', 'okay1', 'Transpo/venue', 'qwertyqqwe', '2026-02-24', '2026-02-27', 'bukas', 69696.00, '2026-02-23 07:19:04', 'FOR SUPPLY REVIEW', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 3, 'anskdjk1n23', 'sample1', 'Supplies', 'sampledesc', '2026-02-19', '2026-02-26', 'bukas', 19898.00, '2026-02-24 01:38:39', 'FOR SUPPLY REVIEW', 'okay', '2026-02-25', 'PARTIAL DELIVER', 'sampel\r\nasdlkals', 'askdmlas\r\nmalksdlkqwe', 'asample', '2026-02-24', 'PRE-BUDGET FOR VOUCHER', 'sample', '2026-02-24', 'aasdjk123', '2026-02-18', 'ACCOUNTS PAYABLE', 'Due and Demandable', 'sanoke', 'POST BUDGET FOR VOUCHER', 'dsaplme\nDV Amount: 1000', '2026-02-24', '', '', '', '0000-00-00', '', '0000-00-00'),
(16, 3, 'asdkj123', 'asldkllnlk', 'Transpo/venue', 'asdklnaskldnla', '2026-02-18', '2026-02-28', NULL, 2000.00, '2026-02-27 01:38:23', 'FOR SUPPLY REVIEW', 'okay', '2026-02-27', 'PARTIAL DELIVER', 'na', 'na', 'okay', '2026-02-27', '', 'wait', '2026-02-27', 'asdjkjn28198', '2026-02-28', 'FOR PAYMENT', 'Due and Demandable', 'waitArek', 'FOR VOUCHER', 'okay po aasd\nDV Amount: 300', '2026-02-27', NULL, NULL, NULL, NULL, NULL, NULL),
(19, 3, 'asd', 'as', 'Transpo/venue', 'asd', '2026-02-19', '2026-02-28', NULL, 1111.00, '2026-02-27 02:47:14', 'FOR SUPPLY REVIEW', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 6, 'asd', 'asdasd', 'Transpo/venue', 'asldmkasm', '2026-02-18', '2026-02-26', NULL, 3030303.00, '2026-02-27 21:05:32', 'COMPLETED', 'okay', '2026-02-28', 'COMPLETED', 'sample receipt', 'sample sales invoice', 'okay', '2026-02-28', '', 'okay', '2026-02-28', 'asd', '2026-02-26', 'ACCOUNTS PAYABLE', 'Not Yet Due and Demandable', 'again', 'FOR VOUCHER', 'okay3\nDV Amount: 5000', '2026-02-28', 'For OR Issuance', 'sample', 'dfgfgd5', '2026-02-25', '2000', '2026-03-07');

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
(49, 14, 'procurement', 'FOR SUPPLY REVIEW', '', '2026-02-24 01:40:00'),
(50, 14, 'supply', 'PARTIAL DELIVER', 'asample', '2026-02-24 01:44:47'),
(51, 14, 'accounting_pre', 'PRE-BUDGET FOR VOUCHER', 'sample', '2026-02-24 02:03:15'),
(52, 14, 'accounting_pre', 'PRE-BUDGET FOR VOUCHER', 'sample', '2026-02-24 02:03:23'),
(53, 14, 'accounting_pre', 'PRE-BUDGET FOR VOUCHER', 'sample', '2026-02-24 02:04:42'),
(54, 14, 'budget', 'ACCOUNTS PAYABLE', 'sanoke', '2026-02-24 02:13:24'),
(55, 14, 'accounting_pre', 'PRE-BUDGET FOR VOUCHER', 'sample', '2026-02-24 02:13:40'),
(56, 14, 'procurement', 'FOR SUPPLY REVIEW', '', '2026-02-24 02:15:13'),
(57, 14, 'accounting_post', 'POST BUDGET FOR VOUCHER', 'dsaplme\nDV Amount: 1000', '2026-02-24 02:15:52'),
(58, 14, 'accounting_post', 'POST BUDGET FOR VOUCHER', 'dsaplme\nDV Amount: 1000', '2026-02-24 02:23:57'),
(59, 14, 'procurement', 'FOR SUPPLY REVIEW', '', '2026-02-24 02:25:03'),
(60, 14, 'procurement', 'FOR SUPPLY REVIEW', 'idk', '2026-02-24 23:51:29'),
(61, 14, 'procurement', 'FOR SUPPLY REVIEW', 'okay', '2026-02-24 23:54:23'),
(69, 16, 'procurement', 'FOR SUPPLY REVIEW', 'okay', '2026-02-27 01:39:19'),
(70, 16, 'supply', 'PARTIAL DELIVER', 'okay', '2026-02-27 01:48:28'),
(71, 16, 'accounting_pre', '', 'okay i suppose', '2026-02-27 01:49:02'),
(72, 16, 'accounting_pre', '', 'wait', '2026-02-27 01:50:48'),
(73, 16, 'budget', 'FOR PAYMENT', 'waitArek', '2026-02-27 01:51:43'),
(74, 16, 'accounting_post', 'FOR VOUCHER', 'okay po aasd\nDV Amount: 300', '2026-02-27 01:52:37'),
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
(91, 20, 'cashier', 'For OR Issuance', 'sample\nAmount: 2000', '2026-02-28 02:01:44');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `role_id`, `supplier_id`, `created_at`) VALUES
(10, 'admin', '$2a$12$XhSdO3SPRj0NL2b2Ln1v8uzgYAQEopTZbNSEl/z7BQvud/YtrqWeS', 1, NULL, '2026-02-10 06:04:17'),
(12, 'procurement@deped.gov', '$2y$10$WIr.aqoARYqxmmuPbolaSupT4CkeSo3kZ8FaNhgzPTiDk/4GL1w3W', 3, NULL, '2026-02-10 06:04:17'),
(13, 'supply@deped.gov', '$2a$12$XhSdO3SPRj0NL2b2Ln1v8uzgYAQEopTZbNSEl/z7BQvud/YtrqWeS', 4, NULL, '2026-02-10 06:04:17'),
(14, 'accounting@deped.gov', '$2a$12$XhSdO3SPRj0NL2b2Ln1v8uzgYAQEopTZbNSEl/z7BQvud/YtrqWeS', 5, NULL, '2026-02-10 06:04:17'),
(15, 'budget@deped.gov', '$2a$12$XhSdO3SPRj0NL2b2Ln1v8uzgYAQEopTZbNSEl/z7BQvud/YtrqWeS', 6, NULL, '2026-02-10 06:04:17'),
(16, 'cashier@deped.gov', '$2a$12$XhSdO3SPRj0NL2b2Ln1v8uzgYAQEopTZbNSEl/z7BQvud/YtrqWeS', 7, NULL, '2026-02-10 06:04:17'),
(18, 'supplier1', '$2y$10$653ItFpv6OYpY.rtFwll3Ov0QEZVKM7Klk.S24QP9hnXanPe6x7X6', 2, 3, '2026-02-10 07:05:35'),
(21, 'alphanum0001', '$2y$10$tpzyX0cWN3t51knWrTMbruq6QGW8CySC3vJV1W45g0BFoL0aBlUpe', 2, 6, '2026-02-27 20:44:26');

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `transaction_updates`
--
ALTER TABLE `transaction_updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

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
