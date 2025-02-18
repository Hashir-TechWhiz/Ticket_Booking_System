-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 18, 2025 at 10:25 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ticket_booking_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `phone`, `email`, `password`, `created_at`) VALUES
(5, 'Purple queen', '0812235960', 'hashir@gmail.com', '$2y$10$wpJeHuvMyhWby0w4WvUIeeMkoISnAk42UVm.aw5msg5xYkvuMMquK', '2025-02-05 09:09:24'),
(8, 'Suranganavi', '0214578965', 'asadhahamed51@gmail.com', '$2y$10$S8LqhQl0ewe0MqBNlAXrle2.DEbupgsximTzepI3D/XUAJZJHFL7K', '2025-02-07 02:45:10');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `bus_id` int(11) DEFAULT NULL,
  `seat_number` int(11) DEFAULT NULL,
  `journey_date` date DEFAULT NULL,
  `payment_status` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `bus_id`, `seat_number`, `journey_date`, `payment_status`, `created_at`) VALUES
(6, 6, 8, 52, '2025-02-19', 'Confirmed', '2025-02-17 16:03:37'),
(7, 6, 8, 54, '2025-02-19', 'Confirmed', '2025-02-17 16:03:37');

-- --------------------------------------------------------

--
-- Table structure for table `buses`
--

CREATE TABLE `buses` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `bus_number` varchar(50) DEFAULT NULL,
  `bus_name` varchar(100) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `seats` int(11) DEFAULT NULL,
  `route_from` varchar(100) DEFAULT NULL,
  `route_to` varchar(100) DEFAULT NULL,
  `time` varchar(20) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `bus_type` enum('A/C','Non-A/C') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buses`
--

INSERT INTO `buses` (`id`, `admin_id`, `bus_number`, `bus_name`, `contact_number`, `seats`, `route_from`, `route_to`, `time`, `price`, `bus_type`, `created_at`) VALUES
(5, 5, 'NA 2345', 'Purple queen', '0214457896', 44, 'Kandy', 'Colombo', '10.00 PM', 500.00, 'Non-A/C', '2025-02-05 15:20:54'),
(6, 5, 'NA 2346', 'Suranganavi', '8754489653', 64, 'Kandy', 'Colombo', '05.45 AM', 1500.00, 'Non-A/C', '2025-02-05 16:21:50'),
(7, 8, 'NA 2345', 'Suranganavi', '8754489653', 64, 'Kandy', 'Colombo', '10.15 AM', 500.00, 'Non-A/C', '2025-02-07 02:45:46'),
(8, 5, 'NB 1234', 'Purple King', '0214457896', 54, 'Kandy', 'Jaffna', '05.45 PM', 2000.00, 'Non-A/C', '2025-02-17 16:02:39');

-- --------------------------------------------------------

--
-- Table structure for table `trip_buses`
--

CREATE TABLE `trip_buses` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `bus_name` varchar(255) NOT NULL,
  `bus_number` varchar(50) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `seats` int(11) NOT NULL,
  `bus_type` enum('AC','Non-AC') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trip_buses`
--

INSERT INTO `trip_buses` (`id`, `admin_id`, `bus_name`, `bus_number`, `contact_number`, `seats`, `bus_type`) VALUES
(2, 5, 'Purple queen', 'NA 2345', '0214457896', 64, 'Non-AC'),
(3, 8, 'Suranganavi', 'NA 2347', '0214457896', 55, 'AC');

-- --------------------------------------------------------

--
-- Table structure for table `trip_requests`
--

CREATE TABLE `trip_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `trip_bus_id` int(11) NOT NULL,
  `route_from` varchar(255) NOT NULL,
  `route_to` varchar(255) NOT NULL,
  `days` int(11) NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `request_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trip_requests`
--

INSERT INTO `trip_requests` (`id`, `user_id`, `trip_bus_id`, `route_from`, `route_to`, `days`, `status`, `request_date`, `date_from`, `date_to`) VALUES
(8, 1, 2, 'Kandy', 'Colombo', 2, 'Approved', '2025-02-12 15:34:02', '2025-02-14', '2025-02-15'),
(9, 6, 2, 'Colombo', 'Kilinochchi', 5, 'Rejected', '2025-02-12 15:34:41', '2025-02-21', '2025-02-25'),
(10, 6, 3, 'Kandy', 'Colombo', 8, 'Approved', '2025-02-12 15:35:55', '2025-02-14', '2025-02-21'),
(11, 6, 3, 'Kandy', 'Nuwara Eliya', 2, 'Rejected', '2025-02-12 15:39:24', '2025-02-13', '2025-02-14'),
(12, 6, 3, 'Kandy', 'Jaffna', 3, 'Approved', '2025-02-12 15:44:09', '2025-02-13', '2025-02-15'),
(13, 6, 2, 'Kandy', 'Nuwara Eliya', 9, 'Approved', '2025-02-12 18:27:24', '2025-02-13', '2025-02-21'),
(14, 6, 3, 'Colombo', 'Nuwara Eliya', 9, 'Approved', '2025-02-12 18:41:45', '2025-02-14', '2025-02-22'),
(15, 6, 2, 'Kandy', 'Jaffna', 4, 'Approved', '2025-02-17 13:45:18', '2025-02-18', '2025-02-21');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `phone`, `email`, `password`, `created_at`) VALUES
(1, 'Mohamed Hashir', '8451518151', 'hashir@gmail.com', '$2y$10$BFn6P3.vQ47TemS0Da1q/.mE.uTeGTKsiOYv469qzXsY5x5Mw0Wdq', '2025-02-05 15:58:12'),
(2, 'Hashir', '0812235960', 'asadhahamed51@gmail.com', '$2y$10$mInAbl34BFBBZZl7lVEc/eRoaQ0MX8CAodQaFTW9YvIijbhOZYMuG', '2025-02-05 16:15:39'),
(6, 'Hashir', '0812235960', 'hashirmohamed04@gmail.com', '$2y$10$/quwFOiW7ks0j8GLAfMlAOUaL09WcYLLDZkktA5JEhRdPkXCSKq/C', '2025-02-12 15:34:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `bus_id` (`bus_id`);

--
-- Indexes for table `buses`
--
ALTER TABLE `buses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `trip_buses`
--
ALTER TABLE `trip_buses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `trip_requests`
--
ALTER TABLE `trip_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `trip_bus_id` (`trip_bus_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `buses`
--
ALTER TABLE `buses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `trip_buses`
--
ALTER TABLE `trip_buses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `trip_requests`
--
ALTER TABLE `trip_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`bus_id`) REFERENCES `buses` (`id`);

--
-- Constraints for table `buses`
--
ALTER TABLE `buses`
  ADD CONSTRAINT `buses_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`);

--
-- Constraints for table `trip_buses`
--
ALTER TABLE `trip_buses`
  ADD CONSTRAINT `trip_buses_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `trip_requests`
--
ALTER TABLE `trip_requests`
  ADD CONSTRAINT `trip_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trip_requests_ibfk_2` FOREIGN KEY (`trip_bus_id`) REFERENCES `trip_buses` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
