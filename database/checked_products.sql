-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 08, 2022 at 10:57 AM
-- Server version: 10.4.25-MariaDB
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shopping`
--

-- --------------------------------------------------------

--
-- Table structure for table `checked_products`
--

CREATE TABLE `checked_products` (
  `id` int(11) NOT NULL,
  `productID` int(11) NOT NULL,
  `sellerID` int(11) NOT NULL,
  `customerID` int(11) NOT NULL,
  `productname` varchar(15) NOT NULL,
  `total_price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `img` varchar(45) NOT NULL,
  `date_checked` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `checked_products`
--

INSERT INTO `checked_products` (`id`, `productID`, `sellerID`, `customerID`, `productname`, `total_price`, `quantity`, `img`, `date_checked`) VALUES
(22, 6, 4, 4, 'dickies S', 2500, 5, '/public/images/2.jpg', '2022-12-08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `checked_products`
--
ALTER TABLE `checked_products`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `checked_products`
--
ALTER TABLE `checked_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
