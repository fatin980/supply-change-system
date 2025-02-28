-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 28, 2025 at 04:34 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `supply_change_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `deliver_to`
--

CREATE TABLE `deliver_to` (
  `id` int(11) NOT NULL,
  `date_created` datetime DEFAULT current_timestamp(),
  `name` varchar(255) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `zip` varchar(20) NOT NULL,
  `contact` varchar(50) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `deliver_to`
--

INSERT INTO `deliver_to` (`id`, `date_created`, `name`, `company_name`, `address`, `city`, `zip`, `contact`, `status`) VALUES
(20, '2025-02-27 17:07:09', 'Ng Jing Wen', 'Junzo Sdn Bhd', 'D\'Piazza', 'Bayan Lepas', '11900', '0164710660', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `po_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('unread','read') NOT NULL DEFAULT 'unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `product_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `currency` enum('RM','USD','INR') NOT NULL,
  `status` enum('Inactive','Active') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `date_created`, `product_name`, `description`, `unit_price`, `currency`, `status`) VALUES
(1, '2025-02-26 22:16:39', 'UX100', 'mmb', 2000.00, 'RM', 'Active'),
(2, '2025-02-26 22:16:53', 'BW410', 'aa', 1000.00, 'RM', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `p_order`
--

CREATE TABLE `p_order` (
  `p_id` int(11) NOT NULL,
  `po_no` varchar(50) NOT NULL,
  `quotation_no` varchar(50) NOT NULL,
  `project_code` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `payment_terms` varchar(100) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `remark` text DEFAULT NULL,
  `status` enum('pending','approved','denied','cancelled') NOT NULL DEFAULT 'pending',
  `invoice_no` varchar(50) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_status` enum('pending','processing','paid') NOT NULL DEFAULT 'pending',
  `po_file` varchar(255) DEFAULT NULL,
  `invoice_file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(255) NOT NULL,
  `setting_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`) VALUES
(1, 'system_name', 'Supply Chain System'),
(2, 'system_short_name', 'PO'),
(3, 'company_name', 'Junzo Sdn Bhd'),
(4, 'company_email', 'trainings@junzo.my'),
(5, 'company_address', '17-10, Stellar Suites, Jalan Puteri 4/7, Bandar Puteri, 47100 Puchong Selangor'),
(6, 'company_contact', '(+603) 8603 2585 / (+604) 611 5278'),
(7, 'system_logo', 'uploads/Junzo_logo.png');

-- --------------------------------------------------------

--
-- Table structure for table `shipping_terms`
--

CREATE TABLE `shipping_terms` (
  `shipping_id` int(11) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `requisitioners` varchar(255) NOT NULL,
  `shipping_terms` enum('FOB','CIF','NET 30','NET 14','NET 60') NOT NULL,
  `deliver_via` enum('Virtual Live Classroom','Face to Face','Hybrid','E-Learning','Certification','Onsite Client Location') NOT NULL,
  `status` enum('Inactive','Active') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `shipping_terms`
--

INSERT INTO `shipping_terms` (`shipping_id`, `date_created`, `last_updated`, `requisitioners`, `shipping_terms`, `deliver_via`, `status`) VALUES
(1, '2025-02-27 01:45:19', '2025-02-27 08:45:19', 'Iffah', 'NET 30', 'Virtual Live Classroom', 'Active'),
(2, '2025-02-27 01:45:32', '2025-02-27 08:45:32', 'Fatehah', 'NET 14', 'Virtual Live Classroom', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$GSn0vrCR/ps3Q1nmDxm3j.T9fy0mgIX4qkcgIVfF2HpNBbmk.H03e', 'admin', '2025-02-26 11:04:32'),
(2, 'user', '$2y$10$.T.p3xlp99XDnnlr8ubtZuf50PIPeSyIbbkCppu92O3XE/Mt0fToC', 'user', '2025-02-26 11:04:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `deliver_to`
--
ALTER TABLE `deliver_to`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `po_id` (`po_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `p_order`
--
ALTER TABLE `p_order`
  ADD PRIMARY KEY (`p_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `shipping_terms`
--
ALTER TABLE `shipping_terms`
  ADD PRIMARY KEY (`shipping_id`);

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
-- AUTO_INCREMENT for table `deliver_to`
--
ALTER TABLE `deliver_to`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `p_order`
--
ALTER TABLE `p_order`
  MODIFY `p_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `shipping_terms`
--
ALTER TABLE `shipping_terms`
  MODIFY `shipping_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id`);

--
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
