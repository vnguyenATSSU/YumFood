-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 11, 2025 at 08:55 PM
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

CREATE TABLE `cart` (
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`user_id`, `item_id`, `quantity`) VALUES
(2, 3, 1),
(2, 10, 2);

-- --------------------------------------------------------

--
-- Table structure for table `menu_item`
--

CREATE TABLE `menu_item` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(50) NOT NULL,
  `item_description` varchar(255) NOT NULL,
  `item_category` varchar(15) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `item_photo` varchar(200) DEFAULT NULL,
  `thumbs_up` int(11) DEFAULT 0,
  `thumbs_down` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_item`
--

INSERT INTO `menu_item` (`item_id`, `item_name`, `item_description`, `item_category`, `unit_price`, `item_photo`, `thumbs_up`, `thumbs_down`) VALUES
(1, 'Banhmi', 'Crispy baguette, savory meats, fresh herbs, and pickled veggies', 'main course', 4.99, './images/Banhmi.jpg', 1, 1),
(2, 'Pancake', 'Crispy crepe-like layer made of a mix of turmeric powder and rice flour, stuffed with various ingredients, but most common are veggies, mung beans and meat, sometimes seafood or pork', 'Main Course', 2.99, './images/pancake.jpeg', 1, 1),
(3, 'Spring Rolls', 'Rice vermicelli, lettuce, shredded carrots, a variety of herbs (the best combination is basil, mint and cilantro), and pork or shrimp or both', 'Appetizer', 0.99, './images/SpringRolls.jpg', 1, 1),
(4, 'Flan', 'Classic Vietnamese dessert that\'s as delicious as it is simple. Think of it like a silky, smooth custard topped with a rich, golden caramel sauce', 'dessert', 4.99, './images/Flan.jpg', 2, 0),
(6, 'Iced Coffee', 'Earthy and bitter flavor, making it well-suited to be paired with sweet, creamy condensed milk.', 'Drink', 3.99, './images/IcedCoffee.jpg', 1, 1),
(7, 'Noodle soup ( Bun rieu)', '\"\"', 'Main Course', 10.99, './images/Bun rieu.jpeg', 0, 0),
(10, 'Grilled Lamp Leg', '\"\"', 'Main Course', 15.99, './images/Grilled Lamb leg.jpeg', 0, 0),
(11, 'Vietnamese Mini Savory Pancake', '\"\"', 'Appetizer', 9.99, './images/Vietnamese Mini Savory Pancake.jpeg', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `order_detail`
--

CREATE TABLE `order_detail` (
  `detail_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_detail`
--

INSERT INTO `order_detail` (`detail_id`, `order_id`, `item_id`, `quantity`) VALUES
(1, 1, 1, 2),
(2, 2, 4, 1),
(3, 2, 6, 2),
(4, 2, 7, 1),
(5, 2, 11, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `first_name`, `last_name`, `email`, `password`, `is_admin`) VALUES
(1, 'admin', '1', 'admin@gmail.com', '$2y$10$lSvfodZ10z.sjft6LEPSPu5ocqCWosjG2k/brc7tR/3zVML04QZ7G', 1),
(2, 'Vu', 'N', 'quocanhnguyen2462002@gmail.com', '$2y$10$TcNCAwVbUvMsXS.ZF.HYJ.UpCY6zgFjM9xdvENYheYuJdQjMUn8.i', 0),
(3, 'V', 'N', 'tony2462002@gmail.com', '$2y$10$a3DkAHjsrTNtvjA5RkvCLet.uNR8hVLKz9htteZ.sBfpfwr/8b25G', 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_order`
--

CREATE TABLE `user_order` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_datetime` datetime DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_order`
--

INSERT INTO `user_order` (`order_id`, `user_id`, `order_datetime`, `total_price`) VALUES
(1, 2, '2025-03-26 12:12:14', 11.98),
(2, 2, '2025-04-11 13:39:51', 33.95);

-- --------------------------------------------------------

--
-- Table structure for table `user_review_food`
--

CREATE TABLE `user_review_food` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `review` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_votes`
--

CREATE TABLE `user_votes` (
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `vote_type` enum('up','down') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_votes`
--

INSERT INTO `user_votes` (`user_id`, `item_id`, `vote_type`) VALUES
(2, 1, 'down'),
(2, 2, 'down'),
(2, 3, 'up'),
(2, 4, 'up'),
(2, 6, 'down'),
(3, 1, 'up'),
(3, 2, 'up'),
(3, 3, 'down'),
(3, 4, 'up'),
(3, 6, 'up');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`user_id`,`item_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `menu_item`
--
ALTER TABLE `menu_item`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD PRIMARY KEY (`detail_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_order`
--
ALTER TABLE `user_order`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_review_food`
--
ALTER TABLE `user_review_food`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `user_votes`
--
ALTER TABLE `user_votes`
  ADD PRIMARY KEY (`user_id`,`item_id`),
  ADD KEY `item_id` (`item_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `menu_item`
--
ALTER TABLE `menu_item`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `order_detail`
--
ALTER TABLE `order_detail`
  MODIFY `detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_order`
--
ALTER TABLE `user_order`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_review_food`
--
ALTER TABLE `user_review_food`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

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
  ADD CONSTRAINT `user_votes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `user_votes_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `menu_item` (`item_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
