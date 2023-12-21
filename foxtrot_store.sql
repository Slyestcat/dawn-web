-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 24, 2020 at 12:00 PM
-- Server version: 5.7.27-0ubuntu0.16.04.1
-- PHP Version: 7.2.22-1+ubuntu16.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `foxtrot_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `title` varchar(20) NOT NULL,
  `zindex` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `title`, `zindex`) VALUES
(7, 'Rares', 0),
(8, 'Weapons', 0),
(9, 'Armor', 0),
(10, 'Holiday', 0),
(11, 'Accessories', 0),
(13, 'yeet', 0);

-- --------------------------------------------------------

--
-- Table structure for table `discount_codes`
--

CREATE TABLE `discount_codes` (
  `id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `percentage` int(11) NOT NULL DEFAULT '0',
  `expires` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `discount_codes`
--

INSERT INTO `discount_codes` (`id`, `code`, `percentage`, `expires`) VALUES
(9, 'test', 25, 1593097720);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_number` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `paid` double NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '0',
  `currency` varchar(255) NOT NULL,
  `buyer` varchar(255) DEFAULT NULL,
  `dateline` bigint(20) DEFAULT '0',
  `player_name` varchar(255) DEFAULT NULL,
  `claimed` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `item_name`, `item_number`, `status`, `paid`, `quantity`, `currency`, `buyer`, `dateline`, `player_name`, `claimed`) VALUES
(1, 'Yellow Partyhat', 10, 'complete', 50, 1, 'USD', 'rune.evo2012@gmail.com', 1593007993, 'og kingfox', 0);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_id` int(11) NOT NULL DEFAULT '-1',
  `category` int(11) NOT NULL DEFAULT '0',
  `price` double NOT NULL,
  `max_qty` int(11) NOT NULL DEFAULT '-1',
  `image_url` varchar(255) DEFAULT NULL,
  `summary` varchar(255) DEFAULT NULL,
  `description` longtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `item_name`, `item_id`, `category`, `price`, `max_qty`, `image_url`, `summary`, `description`) VALUES
(10, 'Yellow Partyhat', 1040, 7, 2500, 5, 'public/img/donate/1040.png', NULL, NULL),
(11, 'Eagle Kite', 18361, 9, 40, -1, 'public/img/donate/eagle.png', NULL, NULL),
(17, 'Chaotic Crossbow', 18357, 8, 35, -1, 'public/img/donate/ccb.png', 'yeet', NULL),
(18, 'Red Partyhat', 1048, 7, 25, 5, 'public/img/donate/1038.png', 'A rare cosmetic item received during the christmas event.', '<p>This is an example info box.</p>'),
(1020, 'Blue Partyhat', 1048, 7, 25, -1, 'public/img/donate/1042.png', 'A rare cosmetic item received during the christmas event.', '<p>This is an example info box.</p>'),
(1021, 'Rainbow Partyhat', 11863, 7, 100, 1, 'public/img/donate/11863.png', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `sess_id` varchar(255) NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `created` bigint(20) NOT NULL,
  `expires` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `username`, `sess_id`, `ip_address`, `created`, `expires`) VALUES
(6, 'admin', '0y1cxP&$qJtn05lBe9wMUD5Y4w$#&x', '::1', 1593011988, 1593098388);

-- --------------------------------------------------------

--
-- Table structure for table `users_cart`
--

CREATE TABLE `users_cart` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `discount_codes`
--
ALTER TABLE `discount_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_cart`
--
ALTER TABLE `users_cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`,`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `discount_codes`
--
ALTER TABLE `discount_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1023;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users_cart`
--
ALTER TABLE `users_cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
