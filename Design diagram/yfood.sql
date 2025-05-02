-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 16, 2025 at 04:41 PM
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
-- Database: `yfood`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
CREATE TABLE IF NOT EXISTS `cart` (
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`item_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu_item`
--

DROP TABLE IF EXISTS `menu_item`;
CREATE TABLE IF NOT EXISTS `menu_item` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_name` varchar(50) NOT NULL,
  `item_description` varchar(255) NOT NULL,
  `item_category` varchar(15) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `item_photo` varchar(200) DEFAULT NULL,
  `thumbs_up` int(11) DEFAULT 0,
  `thumbs_down` int(11) DEFAULT 0,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_detail`
--

DROP TABLE IF EXISTS `order_detail`;
CREATE TABLE IF NOT EXISTS `order_detail` (
  `detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`detail_id`),
  KEY `order_id` (`order_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `first_name`, `last_name`, `email`, `password`, `is_admin`) VALUES
(1, 'Vu', 'Nguyen', 'quocanhnguyen2462002@gmail.com', '$2y$10$Tlxbbb0Lvs14v/80NAJFXeA8dbh2bZRbC4KqJbSXD5C9Q.mMgxMPG', 0),
(2, 'admin', '1', 'admin@gmail.com', '$2y$10$59T6bbiZft2ADBN3t0cXZ.ahE2IaY.HdqQmBE6DlTF1Gg1nOxAz5K', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_order`
--

DROP TABLE IF EXISTS `user_order`;
CREATE TABLE IF NOT EXISTS `user_order` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `order_datetime` datetime DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`order_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_review_food`
--

DROP TABLE IF EXISTS `user_review_food`;
CREATE TABLE IF NOT EXISTS `user_review_food` (
  `review_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `review` text DEFAULT NULL,
  PRIMARY KEY (`review_id`),
  KEY `user_id` (`user_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_votes`
--

DROP TABLE IF EXISTS `user_votes`;
CREATE TABLE IF NOT EXISTS `user_votes` (
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `vote_type` enum('up','down') NOT NULL,
  PRIMARY KEY (`user_id`,`item_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `menu_item` (`item_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD CONSTRAINT `order_detail_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `user_order` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_detail_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `menu_item` (`item_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_order`
--
ALTER TABLE `user_order`
  ADD CONSTRAINT `user_order_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_review_food`
--
ALTER TABLE `user_review_food`
  ADD CONSTRAINT `user_review_food_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_review_food_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `menu_item` (`item_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_votes`
--
ALTER TABLE `user_votes`
  ADD CONSTRAINT `user_votes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_votes_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `menu_item` (`item_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
