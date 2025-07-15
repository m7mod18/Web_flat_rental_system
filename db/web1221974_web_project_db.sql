-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 13, 2025 at 09:19 PM
-- Server version: 8.0.42
-- PHP Version: 8.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `web1221974_web_project_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int NOT NULL,
  `flat_id` int DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `appointment_date` date DEFAULT NULL,
  `appointment_time` time DEFAULT NULL,
  `status` varchar(10) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `flat_id`, `customer_id`, `appointment_date`, `appointment_time`, `status`) VALUES
(4, 16, 5, '2025-06-15', '13:52:00', ''),
(5, 16, 5, '2025-06-22', '13:52:00', 'rejected'),
(6, 16, 5, '2025-06-25', '16:53:00', 'pending'),
(7, 16, 5, '2025-06-18', '16:53:00', 'pending'),
(8, 17, 5, '2025-06-15', '03:24:00', 'rejected'),
(9, 17, 5, '2025-06-22', '03:24:00', 'rejected'),
(10, 19, 5, '2025-06-24', '13:40:00', 'rejected'),
(11, 19, 5, '2025-06-17', '13:40:00', 'approved'),
(12, 22, 5, '2025-06-21', '20:23:00', 'rejected'),
(13, 22, 5, '2025-06-17', '20:21:00', 'approved'),
(14, 19, 5, '2025-06-13', '23:40:00', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `national_id` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mobile` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `user_id`, `national_id`, `name`, `address`, `dob`, `email`, `mobile`, `telephone`) VALUES
(1, 1, '1221974', 'Mahmoud Kafafi', NULL, NULL, NULL, NULL, NULL),
(2, 6, '342', 'Mahmoud Kafafi ههههههه', NULL, NULL, NULL, NULL, NULL),
(3, 8, '123124', 'mar Mahmoud Kafafi', NULL, NULL, NULL, NULL, NULL),
(4, 10, '234235235', 'Emran', NULL, NULL, NULL, NULL, NULL),
(5, 11, '122197444', 'Mahmoud Ahmad Abd', '14b, askar, Nablus , 43564', '2004-03-31', 'mm2015-kafafi@hotmail.com', '0568548606', '984548688'),
(6, 20, '420347689', 'maryam kfafi', '2, al ahram street, ramallah, 08765', '2004-04-30', 'maryammahameed2@gmail.com', '0569604194', '067895437'),
(7, 21, '234567890', 'Fatima Hani', '4073, الناصره الحي الشرقي, الحارة الشرقية الناصرة, 90052', '2004-12-16', '1221785@student.birzeit.edu', '0542363234', '059986889');

-- --------------------------------------------------------

--
-- Table structure for table `flats`
--

CREATE TABLE `flats` (
  `flat_id` int NOT NULL,
  `owner_id` int DEFAULT NULL,
  `ref_number` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `location` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_general_ci,
  `monthly_cost` decimal(10,2) DEFAULT NULL,
  `available_from` date DEFAULT NULL,
  `available_to` date DEFAULT NULL,
  `bedrooms` int DEFAULT NULL,
  `bathrooms` int DEFAULT NULL,
  `size_sqm` int DEFAULT NULL,
  `backyard` enum('none','individual','shared') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `approved` tinyint(1) DEFAULT '0',
  `is_furnished` tinyint(1) DEFAULT '0',
  `has_heating` tinyint(1) DEFAULT '0',
  `has_air_conditioning` tinyint(1) DEFAULT '0',
  `access_control` tinyint(1) DEFAULT '0',
  `car_parking` tinyint(1) DEFAULT '0',
  `playground` tinyint(1) DEFAULT '0',
  `storage` tinyint(1) DEFAULT '0',
  `is_rented` tinyint(1) DEFAULT '0',
  `rental_conditions` text COLLATE utf8mb4_general_ci,
  `furnished` varchar(10) COLLATE utf8mb4_general_ci DEFAULT 'no'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `flats`
--

INSERT INTO `flats` (`flat_id`, `owner_id`, `ref_number`, `location`, `address`, `monthly_cost`, `available_from`, `available_to`, `bedrooms`, `bathrooms`, `size_sqm`, `backyard`, `approved`, `is_furnished`, `has_heating`, `has_air_conditioning`, `access_control`, `car_parking`, `playground`, `storage`, `is_rented`, `rental_conditions`, `furnished`) VALUES
(5, 3, '12', 'birzeit', 'Ramallah ق', 3.00, '2025-05-31', '2025-06-04', 3, 2, 34, 'individual', 1, 0, 0, 0, 0, 0, 0, 0, 1, NULL, 'no'),
(6, 3, '0568', 'Nablus', 'Nablus-Rafedea', 3000.00, '2025-06-01', '2025-06-03', 3, 2, 120, 'shared', 1, 0, 0, 0, 0, 0, 0, 0, 1, NULL, 'no'),
(7, 3, 'REF-00', 'birzeit', 'Nablus-Rafedea', 4444.00, '2025-06-01', '2025-06-11', 3, 2, 333, 'individual', 1, 0, 0, 0, 0, 0, 0, 0, 1, NULL, 'no'),
(12, 3, 'REF-00012', 'Nablus', 'Nablus-Rafedea', 34343.00, '2025-06-03', '2025-07-04', 34, 23, 4342, 'shared', 1, 0, 0, 0, 0, 0, 0, 0, 1, NULL, 'no'),
(13, 3, 'REF-00013', 'birzeit', 'Ramallah', 435.00, '2025-06-11', '2025-06-06', 23, 34, 234, 'shared', 1, 0, 0, 0, 0, 0, 0, 0, 1, NULL, 'no'),
(14, 900952175, NULL, 'Askaf', 'behind the the boys school ', 1500.00, '2025-06-12', '2026-03-11', 3, 2, 200, 'shared', 1, 0, 0, 0, 0, 1, 0, 0, 1, NULL, 'yes'),
(15, 900952175, 'F000015', 'birzeit', 'Ramallah', 2000.00, '2025-06-13', '2025-12-13', 3, 3, 150, 'shared', 1, 0, 0, 1, 0, 0, 1, 0, 1, NULL, 'yes'),
(16, 900952175, 'F000016', 'Nablus', 'Rafedea', 2500.00, '2025-06-13', '2026-03-20', 4, 3, 345, 'shared', 1, 0, 1, 0, 0, 0, 0, 0, 1, NULL, 'yes'),
(17, 527443622, 'F000017', 'Ramallah', 'Ersal', 20000.00, '2025-06-13', '2025-06-20', 2, 3, 180, 'individual', 1, 0, 1, 1, 1, 0, 1, 1, 1, NULL, 'yes'),
(18, 527443622, 'F000018', 'Ramallah', 'ersal street', 2000.00, '2025-07-05', '2025-06-28', 2, 3, 180, 'shared', 1, 0, 1, 1, 1, 1, 1, 1, 0, NULL, 'no'),
(19, 527443622, 'F000019', 'Ramallah', 'ersal street', 2000.00, '2025-07-05', '2025-06-28', 2, 3, 180, 'individual', 1, 0, 1, 1, 0, 0, 0, 0, 0, NULL, 'yes'),
(20, 527443622, 'F000020', 'Ramallah', 'ersal street', 2000.00, '2025-06-01', '2025-06-28', 2, 3, 180, 'shared', 1, 0, 1, 1, 1, 1, 0, 0, 0, NULL, 'no'),
(21, 527443622, 'F000021', 'Ramallah', 'ersal street', 2000.00, '2025-06-01', '2025-06-28', 2, 3, 180, 'shared', 1, 0, 1, 1, 1, 1, 0, 0, 1, NULL, 'no'),
(22, 527443622, 'F000022', 'Ramallah', 'ersal street', 2000.00, '2025-06-29', '2025-07-02', 2, 3, 181, 'shared', 1, 0, 0, 0, 0, 0, 0, 0, 1, NULL, 'no'),
(23, 900952175, 'F000023', 'birzeit', 'Ramallah', 233.00, '2025-06-20', '2025-07-17', 2, 3, 23, 'shared', 1, 0, 0, 0, 0, 0, 0, 0, 1, NULL, 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `flat_availability`
--

CREATE TABLE `flat_availability` (
  `availability_id` int NOT NULL,
  `flat_id` int NOT NULL,
  `day_of_week` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `time_slot` varchar(10) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `flat_availability`
--

INSERT INTO `flat_availability` (`availability_id`, `flat_id`, `day_of_week`, `time_slot`) VALUES
(1, 16, 'Sunday', '13:52'),
(2, 16, 'Wednesday', '16:53'),
(3, 16, 'Friday', '17:10'),
(4, 17, 'Sunday', '03:24'),
(5, 17, 'Monday', '03:24'),
(6, 17, 'Wednesday', '03:24'),
(7, 18, 'Monday', '12:41'),
(8, 18, 'Wednesday', '12:36'),
(9, 18, 'Tuesday', '22:40'),
(10, 19, 'Tuesday', '13:40'),
(11, 19, 'Thursday', '23:38'),
(12, 19, 'Friday', '23:40'),
(13, 20, 'Thursday', '19:50'),
(14, 20, 'Thursday', '12:50'),
(15, 20, 'Thursday', '21:50'),
(16, 21, 'Thursday', '19:50'),
(17, 21, 'Thursday', '12:50'),
(18, 21, 'Thursday', '21:50'),
(19, 22, 'Tuesday', '20:21'),
(20, 22, 'Saturday', '20:23'),
(21, 22, 'Wednesday', '12:18'),
(22, 23, 'Sunday', '21:57'),
(23, 23, 'Wednesday', '21:58'),
(24, 23, 'Saturday', '21:59');

-- --------------------------------------------------------

--
-- Table structure for table `flat_marketing`
--

CREATE TABLE `flat_marketing` (
  `id` int NOT NULL,
  `flat_id` int NOT NULL,
  `title` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `flat_marketing`
--

INSERT INTO `flat_marketing` (`id`, `flat_id`, `title`, `description`, `url`) VALUES
(3, 5, 'Nearby School', 'Close to Al-Quds School', 'https://example.com/school'),
(4, 6, 'Supermarket', '2-minute walk to FreshMart', NULL),
(5, 7, 'Public Park', 'Beautiful green park across the street', 'https://example.com/park');

-- --------------------------------------------------------

--
-- Table structure for table `flat_marketing_info`
--

CREATE TABLE `flat_marketing_info` (
  `info_id` int NOT NULL,
  `flat_id` int DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `flat_photos`
--

CREATE TABLE `flat_photos` (
  `photo_id` int NOT NULL,
  `flat_id` int DEFAULT NULL,
  `photo_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `caption` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `flat_photos`
--

INSERT INTO `flat_photos` (`photo_id`, `flat_id`, `photo_url`, `caption`) VALUES
(1, 5, 'uploads/1748687527_download.jpg', NULL),
(2, 6, 'uploads/1748702908_flat1.1.jpg', NULL),
(3, 7, 'uploads/1748795561_flat1.jpg', NULL),
(4, 12, 'uploads/1748820285_Screenshot 2025-05-30 183528.png', NULL),
(5, 13, 'uploads/1748820315_Screenshot 2025-05-30 183528.png', NULL),
(6, 14, 'uploads/flat_14_0.jpg', NULL),
(7, 15, 'uploads/flat_15_0.jpg', NULL),
(8, 16, 'uploads/flat_16_0.jpg', NULL),
(9, 17, 'uploads/flat_17_0.jpg', NULL),
(10, 17, 'uploads/flat_17_1.jpg', NULL),
(11, 17, 'uploads/flat_17_2.jpg', NULL),
(12, 18, 'uploads/flat_18_0.png', NULL),
(13, 18, 'uploads/flat_18_1.png', NULL),
(14, 18, 'uploads/flat_18_2.png', NULL),
(15, 18, 'uploads/flat_18_3.png', NULL),
(16, 19, 'uploads/flat_19_0.jpg', NULL),
(17, 19, 'uploads/flat_19_1.jpg', NULL),
(18, 19, 'uploads/flat_19_2.jpg', NULL),
(19, 20, 'uploads/flat_20_0.jpg', NULL),
(20, 20, 'uploads/flat_20_1.jpg', NULL),
(21, 20, 'uploads/flat_20_2.jpg', NULL),
(22, 21, 'uploads/flat_21_0.jpg', NULL),
(23, 21, 'uploads/flat_21_1.jpg', NULL),
(24, 21, 'uploads/flat_21_2.jpg', NULL),
(25, 22, 'uploads/flat_22_0.jpg', NULL),
(26, 22, 'uploads/flat_22_1.jpg', NULL),
(27, 22, 'uploads/flat_22_2.jpg', NULL),
(28, 23, 'uploads/flat_23_0.jpg', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `marketing_info`
--

CREATE TABLE `marketing_info` (
  `marketing_id` int NOT NULL,
  `flat_id` int DEFAULT NULL,
  `title` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int NOT NULL,
  `receiver_id` int DEFAULT NULL,
  `sender_role` enum('manager','owner','customer','system') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `message_body` text COLLATE utf8mb4_general_ci,
  `sent_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_read` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `receiver_id`, `sender_role`, `title`, `message_body`, `sent_at`, `is_read`) VALUES
(1, 8, 'customer', 'Test', 'Welcome Pro wasfddddddddddddddddddddddddddddddddddddddddddsssssssaaaaaal,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,dddddddddddddd', '2025-06-01 18:38:44', 1),
(2, 5, 'manager', 'Test 2 ', 'rrrrrrrrrrrrrwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwrrrrrrrrrrrrrwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwrrrrrrrrrrrrrwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwrrrrrrrrrrrrrwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwrrrrrrrrrrrrrwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwrrrrrrrrrrrrrwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwrrrrrrrrrrrrrwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwrrrrrrrrrrrrrwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwrrrrrrrrrrrrrwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwvrrrrrrrrrrrrrwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww', '2025-06-01 18:47:27', 0),
(3, 5, 'manager', 'Test', 'mahmoud Hello ', '2025-06-01 19:36:27', 0),
(4, 8, 'owner', 'Test23', '322234234', '2025-06-01 23:33:28', 1),
(5, 900952175, 'customer', 'New Flat Rental', 'New rental by 1221974@stbzu.birzeit.edu (Phone: 0568548606) for Flat Ref: F000016.', '2025-06-12 01:36:54', 0),
(6, 3, 'customer', 'New Flat Rental', 'New rental confirmed by 1221974@stbzu.birzeit.edu (Phone: 0568548606). Flat Ref: 0568.', '2025-06-12 02:03:00', 0),
(7, 900952175, 'customer', 'New Flat Rental', 'New rental confirmed by 1221974@stbzu.birzeit.edu (Phone: 0568548606). Flat Ref: F000015.', '2025-06-12 14:47:33', 0),
(8, 11, '', 'Rental Confirmed', 'Your flat has been rented successfully. Please collect the key from Mahmoud@gmail.com. Contact: .', '2025-06-12 14:47:33', 1),
(9, 1, '', 'Flat Rented', 'Flat Ref: F000015 rented by 1221974@stbzu.birzeit.edu from 2025-06-19 to 2025-09-24. Owner: Mahmoud@gmail.com.', '2025-06-12 14:47:33', 0),
(10, 3, 'customer', 'New Flat Rental', 'New rental confirmed by 1221974@stbzu.birzeit.edu (Phone: 0568548606). Flat Ref: REF-00012.', '2025-06-12 14:48:56', 0),
(11, 11, '', 'Rental Confirmed', 'Your flat has been rented successfully. Please collect the key from Mahmoud. Contact: .', '2025-06-12 14:48:56', 1),
(12, 1, '', 'Flat Rented', 'Flat Ref: REF-00012 rented by 1221974@stbzu.birzeit.edu from 2025-06-12 to 2025-07-01. Owner: Mahmoud.', '2025-06-12 14:48:56', 0),
(13, 17, 'customer', 'Appointment Request', 'Customer 1221974@stbzu.birzeit.edu requested a flat viewing on 2025-06-15 at 13:52.', '2025-06-12 15:27:20', 1),
(14, 17, 'customer', 'Appointment Request', 'Customer 1221974@stbzu.birzeit.edu requested a flat viewing on 2025-06-22 at 13:52.', '2025-06-12 15:51:51', 1),
(15, 11, 'owner', 'Appointment Rejected', 'Your flat preview appointment has been rejected by the owner.', '2025-06-12 15:52:31', 1),
(16, 17, 'customer', 'New Flat Rental', 'New rental confirmed by 1221974@stbzu.birzeit.edu (Phone: 0568548606). Flat Ref: .', '2025-06-12 16:12:54', 1),
(17, 11, '', 'Rental Confirmed', 'Your flat has been rented successfully. Please collect the key from Mahmoud@gmail.com. Contact: .', '2025-06-12 16:12:54', 1),
(18, 8, '', 'Flat Rented', 'Flat Rental Notification:\nFlat Ref #: \nLocation: Askaf - behind the the boys school \nRented From: 2025-06-23 To: 2025-07-01\nOwner: Mahmoud@gmail.com (Phone: )\nCustomer: 1221974@stbzu.birzeit.edu (Phone: 0568548606)', '2025-06-12 16:12:54', 1),
(19, 17, 'customer', 'Appointment Request', 'Customer 1221974@stbzu.birzeit.edu requested a flat viewing on 2025-06-25 at 16:53.', '2025-06-12 23:58:17', 0),
(20, 17, 'customer', 'Appointment Request', 'Customer 1221974@stbzu.birzeit.edu requested a flat viewing on 2025-06-18 at 16:53.', '2025-06-13 00:04:42', 0),
(21, 19, 'customer', 'Appointment Request', 'Customer 1221974@stbzu.birzeit.edu requested a flat viewing on 2025-06-15 at 03:24.', '2025-06-13 00:27:14', 1),
(22, 19, 'customer', 'Appointment Request', 'Customer 1221974@stbzu.birzeit.edu requested a flat viewing on 2025-06-22 at 03:24.', '2025-06-13 00:27:16', 1),
(23, 11, 'owner', 'Appointment Rejected', 'Your flat preview appointment has been rejected by the owner.', '2025-06-13 00:27:45', 1),
(24, 19, 'customer', 'Rental Request', 'Customer 1221974@stbzu.birzeit.edu has requested to rent Flat Ref #F000017. Rental Period: 2025-06-14 to 2025-06-19. Please review and accept or reject the rental.', '2025-06-13 00:29:31', 1),
(25, 11, 'owner', 'Rental Approved', 'Your rental request for Flat Ref #F000017 has been approved. Please contact the owner (fatima@gmail.com) at phone: 0568548606 to collect the key.', '2025-06-13 00:42:32', 1),
(26, 19, 'customer', 'Appointment Request', 'Customer 1221974@stbzu.birzeit.edu requested a flat viewing on 2025-06-24 at 13:40.', '2025-06-13 16:37:42', 1),
(27, 19, 'customer', 'Appointment Request', 'Customer 1221974@stbzu.birzeit.edu requested a flat viewing on 2025-06-17 at 13:40.', '2025-06-13 16:37:45', 1),
(28, 11, 'owner', 'Appointment Rejected', 'Your flat preview appointment has been rejected by the owner.', '2025-06-13 16:37:59', 1),
(29, 11, 'owner', 'Appointment Rejected', 'Your flat preview appointment has been rejected by the owner.', '2025-06-13 16:38:02', 1),
(30, 11, 'owner', 'Appointment Approved', 'Your flat preview appointment has been approved by the owner.', '2025-06-13 16:42:41', 1),
(31, 19, 'customer', 'Rental Request', 'Customer 1221974@stbzu.birzeit.edu has requested to rent Flat Ref #F000021 from 2025-06-12 to 2025-06-25.', '2025-06-13 16:53:34', 1),
(32, 11, 'customer', 'Rental Request Sent', 'Your rental request for Flat Ref #F000021 has been sent to the owner.', '2025-06-13 16:53:34', 1),
(33, 8, 'customer', 'Rental Request Submitted', 'Rental request for Flat Ref #F000021 from 1221974@stbzu.birzeit.edu (2025-06-12 to 2025-06-25).', '2025-06-13 16:53:34', 1),
(34, 11, 'owner', 'Rental Approved', 'Your rental request for Flat Ref #F000021 has been approved. Please contact the owner (fatima@gmail.com) at phone: 0568548606 to collect the key.', '2025-06-13 16:54:15', 1),
(35, 17, 'customer', 'Rental Request', 'Customer 1221785@student.birzeit.edu has requested to rent Flat Ref #F000016 from 2025-06-24 to 2025-06-26.', '2025-06-13 17:08:15', 0),
(36, 21, 'customer', 'Rental Request Sent', 'Your rental request for Flat Ref #F000016 has been sent to the owner.', '2025-06-13 17:08:15', 1),
(37, 8, 'customer', 'Rental Request Submitted', 'Rental request for Flat Ref #F000016 from 1221785@student.birzeit.edu (2025-06-24 to 2025-06-26).', '2025-06-13 17:08:15', 1),
(38, 19, 'customer', 'Appointment Request', 'Customer 1221974@stbzu.birzeit.edu requested a flat viewing on 2025-06-21 at 20:23.', '2025-06-13 17:20:10', 1),
(39, 19, 'customer', 'Appointment Request', 'Customer 1221974@stbzu.birzeit.edu requested a flat viewing on 2025-06-17 at 20:21.', '2025-06-13 17:20:13', 1),
(40, 11, 'owner', 'Appointment Rejected', 'Your flat preview appointment has been rejected by the owner.', '2025-06-13 17:20:36', 1),
(41, 11, 'owner', 'Appointment Approved', 'Your flat preview appointment has been approved by the owner.', '2025-06-13 17:20:38', 1),
(42, 19, 'customer', 'Rental Request', 'Customer 1221974@stbzu.birzeit.edu has requested to rent Flat Ref #F000022 from 2025-06-30 to 2025-07-01.', '2025-06-13 17:22:22', 1),
(43, 11, 'customer', 'Rental Request Sent', 'Your rental request for Flat Ref #F000022 has been sent to the owner.', '2025-06-13 17:22:22', 1),
(44, 8, 'customer', 'Rental Request Submitted', 'Rental request for Flat Ref #F000022 from 1221974@stbzu.birzeit.edu (2025-06-30 to 2025-07-01).', '2025-06-13 17:22:22', 1),
(45, 11, 'owner', 'Rental Rejected', 'Your rental request for Flat Ref #F000022 has been rejected by the owner.', '2025-06-13 17:23:45', 1),
(46, 11, 'owner', 'Rental Rejected', 'Your rental request for Flat Ref #F000022 has been rejected by the owner.', '2025-06-13 17:24:05', 1),
(47, 11, 'owner', 'Rental Rejected', 'Your rental request for Flat Ref #F000022 has been rejected by the owner.', '2025-06-13 17:24:10', 1),
(48, 11, 'owner', 'Rental Rejected', 'Your rental request for Flat Ref #F000022 has been rejected by the owner.', '2025-06-13 17:24:52', 1),
(49, 19, 'customer', 'Rental Request', 'Customer 1221974@stbzu.birzeit.edu has requested to rent Flat Ref #F000022 from 2025-06-29 to 2025-07-02.', '2025-06-13 17:30:32', 1),
(50, 11, 'customer', 'Rental Request Sent', 'Your rental request for Flat Ref #F000022 has been sent to the owner.', '2025-06-13 17:30:32', 1),
(51, 8, 'customer', 'Rental Request Submitted', 'Rental request for Flat Ref #F000022 from 1221974@stbzu.birzeit.edu (2025-06-29 to 2025-07-02).', '2025-06-13 17:30:32', 1),
(52, 11, 'owner', 'Rental Rejected', 'Your rental request for Flat Ref #F000022 has been rejected by the owner.', '2025-06-13 17:30:56', 1),
(53, 11, 'owner', 'Rental Rejected', 'Your rental request for Flat Ref #F000022 has been rejected by the owner.', '2025-06-13 17:31:23', 1),
(54, 19, 'customer', 'Rental Request', 'Customer 1221974@stbzu.birzeit.edu has requested to rent Flat Ref #F000022 from 2025-06-30 to 2025-07-01.', '2025-06-13 17:31:24', 1),
(55, 11, 'customer', 'Rental Request Sent', 'Your rental request for Flat Ref #F000022 has been sent to the owner.', '2025-06-13 17:31:24', 1),
(56, 8, 'customer', 'Rental Request Submitted', 'Rental request for Flat Ref #F000022 from 1221974@stbzu.birzeit.edu (2025-06-30 to 2025-07-01).', '2025-06-13 17:31:24', 1),
(57, 11, 'owner', 'Rental Approved', 'Your rental request for Flat Ref #F000022 has been approved. Please contact the owner (fatima@gmail.com) at phone: 0568548606 to collect the key.', '2025-06-13 17:31:29', 1),
(58, 19, 'customer', 'Appointment Request', 'Customer 1221974@stbzu.birzeit.edu requested a flat viewing on 2025-06-13 at 23:40.', '2025-06-13 17:43:44', 1),
(59, 11, 'owner', 'Appointment Approved', 'Your flat preview appointment has been approved by the owner.', '2025-06-13 17:43:52', 1),
(60, 17, 'customer', 'Rental Request', 'Customer 1221785@student.birzeit.edu has requested to rent Flat Ref #F000023 from 2025-06-22 to 2025-07-12.', '2025-06-13 17:57:42', 0),
(61, 21, 'customer', 'Rental Request Sent', 'Your rental request for Flat Ref #F000023 has been sent to the owner.', '2025-06-13 17:57:42', 1),
(62, 8, 'customer', 'Rental Request Submitted', 'Rental request for Flat Ref #F000023 from 1221785@student.birzeit.edu (2025-06-22 to 2025-07-12).', '2025-06-13 17:57:42', 1),
(63, 11, 'owner', 'Rental Approved', 'Your rental request for Flat Ref #F000016 has been approved. Please contact the owner (Mahmoud@gmail.com) at phone: 0568548606 to collect the key.', '2025-06-13 17:59:23', 0),
(64, 11, 'owner', 'Rental Rejected', 'Your rental request for Flat Ref #F000016 has been rejected by the owner.', '2025-06-13 17:59:28', 0),
(65, 11, 'owner', 'Rental Rejected', 'Your rental request for Flat Ref #F000016 has been rejected by the owner.', '2025-06-13 17:59:29', 0),
(66, 21, 'owner', 'Rental Approved', 'Your rental request for Flat Ref #F000023 has been approved. Please contact the owner (Mahmoud@gmail.com) at phone: 0542363234 to collect the key.', '2025-06-13 18:00:44', 1),
(67, 11, 'owner', 'Rental Rejected', 'Your rental request for Flat Ref #F000015 has been rejected by the owner.', '2025-06-13 18:00:46', 0);

-- --------------------------------------------------------

--
-- Table structure for table `owners`
--

CREATE TABLE `owners` (
  `owner_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `national_id` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mobile` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bank_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bank_branch` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `account_number` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `house_no` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `street_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `postal_code` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `owners`
--

INSERT INTO `owners` (`owner_id`, `user_id`, `national_id`, `name`, `address`, `dob`, `email`, `mobile`, `telephone`, `bank_name`, `bank_branch`, `account_number`, `phone`, `house_no`, `street_name`, `city`, `postal_code`) VALUES
(1, 3, '12219744', 'Red Shoes', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 4, '1221974', 'Mahmoud Kafafi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 5, '34', 'medea', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 7, '12', 'Ameen Kafafi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(166493430, 18, '123456789', 'Fatima Hani ', NULL, '2004-12-16', 'fatimainjass934@gmail.com', '0568116228', '022568686', 'Bank ', 'Ramalaah', '1612200400', NULL, '12B', 'Ersal ', 'Ramallah ', '00980'),
(414815040, 15, '111111111', 'Mahmoud ', NULL, '2000-03-03', 'mm2015-kafafi@hotmail.com', '0568548606', '984548688', 'Bank Of Palistine', 'Nablus ', '3452564536', NULL, '14b', 'askar', 'Nablus ', '43564'),
(527443622, 19, '987654321', 'Fatima Injas', NULL, '2004-06-13', 'faghge@hrj.cjn', '0568116228', '025568986', 'B', 'R', '1612200400', NULL, '12b', 'M', 'M', '00970'),
(900952175, 17, '222222222', 'Mahmoud ', NULL, '2000-02-02', 'mm2015-kafafi@hotmail.com', '0568548606', '984548688', 'Bank Of Palistine', 'Nablus ', '3452564536', NULL, '4', 'askar', 'Nablus ', '43564');

-- --------------------------------------------------------

--
-- Table structure for table `rentals`
--

CREATE TABLE `rentals` (
  `rental_id` int NOT NULL,
  `customer_id` int DEFAULT NULL,
  `flat_id` int DEFAULT NULL,
  `rent_start` date DEFAULT NULL,
  `rent_end` date DEFAULT NULL,
  `credit_card_number` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `total_cost` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rentals`
--

INSERT INTO `rentals` (`rental_id`, `customer_id`, `flat_id`, `rent_start`, `rent_end`, `credit_card_number`, `total_cost`, `created_at`, `status`) VALUES
(1, 2, 5, '2025-06-02', '2025-07-10', NULL, 3.00, '2025-06-01 16:56:43', 'pending'),
(5, 2, 13, '2025-06-11', '2024-08-15', '124124345', 4350.00, '2025-06-11 14:12:00', 'pending'),
(6, 2, 5, '2025-06-17', '2026-01-28', '123456789', 22.50, '2025-06-11 14:27:23', 'pending'),
(7, 5, 7, '2025-06-02', '2025-06-10', NULL, 1185.07, '2025-06-11 20:02:32', 'pending'),
(8, 5, 7, '2025-06-02', '2025-06-09', NULL, 1036.93, '2025-06-11 20:10:12', 'pending'),
(9, 5, 7, '2025-06-02', '2025-06-09', NULL, 1036.93, '2025-06-11 20:22:46', 'pending'),
(10, 5, 16, '2025-06-18', '2025-09-18', NULL, 7666.67, '2025-06-12 01:29:53', 'approved'),
(11, 5, 16, '2025-06-18', '2025-09-18', NULL, 7666.67, '2025-06-12 01:34:32', 'rejected'),
(12, 5, 16, '2025-06-18', '2025-09-18', NULL, 7666.67, '2025-06-12 01:36:54', 'rejected'),
(13, 5, 6, '2025-06-02', '2025-06-03', NULL, 100.00, '2025-06-12 02:03:00', 'pending'),
(14, 5, 15, '2025-06-19', '2025-09-24', NULL, 6466.67, '2025-06-12 14:47:33', 'rejected'),
(15, 5, 12, '2025-06-12', '2025-07-01', NULL, 21750.57, '2025-06-12 14:48:56', 'pending'),
(16, 5, 14, '2025-06-23', '2025-07-01', NULL, 400.00, '2025-06-12 16:12:54', 'pending'),
(17, 5, 17, '2025-06-14', '2025-06-19', NULL, 3333.33, '2025-06-13 00:29:31', 'approved'),
(18, 5, 21, '2025-06-12', '2025-06-25', NULL, 866.67, '2025-06-13 16:53:34', 'approved'),
(19, 7, 16, '2025-06-24', '2025-06-26', NULL, 166.67, '2025-06-13 17:08:15', 'pending'),
(20, 5, 22, '2025-06-30', '2025-07-01', NULL, 66.67, '2025-06-13 17:22:22', 'rejected'),
(21, 5, 22, '2025-06-29', '2025-07-02', NULL, 200.00, '2025-06-13 17:30:32', 'rejected'),
(22, 5, 22, '2025-06-30', '2025-07-01', NULL, 66.67, '2025-06-13 17:31:24', 'approved'),
(23, 7, 23, '2025-06-22', '2025-07-12', NULL, 155.33, '2025-06-13 17:57:42', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('customer','owner','manager') COLLATE utf8mb4_general_ci NOT NULL,
  `profile_pic` varchar(255) COLLATE utf8mb4_general_ci DEFAULT 'images/user.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `city`, `phone`, `password`, `role`, `profile_pic`) VALUES
(1, 'Mahmoud_Kafafi', NULL, NULL, NULL, '$2y$10$Z8dKEsddyI5qeDIt2WdcHOf3ff.fO6kBnXCNTcpD6xX9C.g5arNI2', 'customer', 'images/user.png'),
(3, 'r', NULL, NULL, NULL, '$2y$10$34l1dNOX.UvY1nSuRrpZceg//dYuH6wheufnEVA2sKb5ao/73N0CS', 'owner', 'images/user.png'),
(4, 'Mahmoud_', NULL, NULL, NULL, '$2y$10$AWD9cN.sROIJUJWxl8aSbuxsL/6KOg5E0T3bKcT/L.H8gSaNIPfpG', 'owner', 'images/user.png'),
(5, 'Mahmoud', NULL, NULL, NULL, '$2y$10$i5YOIjtEaBgmC2Wx4xPB7u32/m6.ZYaz7RFuk/V.1Ga1Lwj6zH1ru', 'owner', 'images/user.png'),
(6, 'M', NULL, NULL, NULL, '$2y$10$QHeJcsr9m1/2jbaNpOZ0e.ih6fba.b4ViqV/UwIl1E0J9625Ve41G', 'customer', 'uploads/1748802454_name_user_3716.png'),
(7, 'Ameen', NULL, NULL, NULL, '$2y$10$QXTtrc9Fo8LfJRtv7ygGqOE2Xar3VsyRc7Z6S.IpWg2c4VyXlFHSy', 'owner', 'uploads/1748628506_download.jpg'),
(8, 'mar', NULL, NULL, NULL, '$2y$10$TmK45g/EjxZ8uxjM6KEuNu7gKSmo9g47YCevokaPK4/RmlOPZkX3u', 'manager', 'images/user.png'),
(10, 'e', NULL, NULL, NULL, '$2y$10$mPa1Y6mIvJSHdz7hiYSPRe.1kMsQrNl8i3tvMaGce/wcJiR6B1Z1O', 'customer', 'uploads/1748790584_flat1.1.jpg'),
(11, '1221974@stbzu.birzeit.edu', NULL, 'Nablus ', '0568548606', '$2y$10$lDk2l4cJ2oT2G8RFvziGtOwl2Py.QYFnoaX.wgcFsAcIHkhZYbXAq', 'customer', 'images/user.png'),
(12, '122@ex.com', NULL, NULL, NULL, '$2y$10$IgdGMLmPIuqcK/ggzYvV.uraRGVwfNKaC/tnc7WhW.5RQCtQKgTk6', 'owner', 'images/user.png'),
(14, 'Mahmoud@test.com', NULL, NULL, NULL, '$2y$10$.8effHDQ4NDOHW.wrE1SY.HZXneSuicKHw5tzztuAOIbi9EdR75Y2', 'owner', 'images/user.png'),
(15, 'm1234@gmail.com', NULL, NULL, NULL, '$2y$10$9Yge8OPjvj9jWCEfNum9lubV2nwXIeaTLiauqlfPI5HeSIwEuzmlu', 'owner', 'images/user.png'),
(17, 'Mahmoud@gmail.com', NULL, NULL, NULL, '$2y$10$eJiWsrOVM4tjBvcAYbimYegXZjAocw8S/GdhZXooNxAprS0XQQ3rq', 'owner', 'images/user.png'),
(18, 'fatimainjass934@gmail.com', NULL, NULL, NULL, '$2y$10$8oH78MPbR7/Pk3mLhWyxIOKSN6WH2R89noqq4FUw/IeyP0A8UTLZi', 'owner', 'images/user.png'),
(19, 'fatima@gmail.com', NULL, 'الحارة الشرقية الناصرة', '0542363234', '$2y$10$xrJ1m.xfP9E1QgQwfvmqMedVfO23Vgd3ZDIxf82atfw.DuI3RMTiu', 'owner', 'uploads/1749774635_IMG_0839.jpeg'),
(20, 'maryammahameed2@gmail.com', NULL, 'ramallah', '0569604194', '$2y$10$mwvt0f.ErRI34/4YAQ60EOwUQsnFCJZXHya7q5mgglEG92DM4LXOK', 'owner', 'images/user.png'),
(21, '1221785@student.birzeit.edu', NULL, 'الحارة الشرقية الناصرة', '0542363234', '$2y$10$OIVodb89Jj6AvmJCWNZEkuA1eS0fSbZTX162grJmD3j1rPJfD/Kj6', 'customer', 'uploads/1749837840_WhatsApp Image 2024-11-03 at 01.27.09.jpeg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `flat_id` (`flat_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `national_id` (`national_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `flats`
--
ALTER TABLE `flats`
  ADD PRIMARY KEY (`flat_id`),
  ADD UNIQUE KEY `ref_number` (`ref_number`),
  ADD UNIQUE KEY `ref_number_2` (`ref_number`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `flat_availability`
--
ALTER TABLE `flat_availability`
  ADD PRIMARY KEY (`availability_id`),
  ADD KEY `flat_id` (`flat_id`);

--
-- Indexes for table `flat_marketing`
--
ALTER TABLE `flat_marketing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `flat_id` (`flat_id`);

--
-- Indexes for table `flat_marketing_info`
--
ALTER TABLE `flat_marketing_info`
  ADD PRIMARY KEY (`info_id`),
  ADD KEY `flat_id` (`flat_id`);

--
-- Indexes for table `flat_photos`
--
ALTER TABLE `flat_photos`
  ADD PRIMARY KEY (`photo_id`),
  ADD KEY `flat_id` (`flat_id`);

--
-- Indexes for table `marketing_info`
--
ALTER TABLE `marketing_info`
  ADD PRIMARY KEY (`marketing_id`),
  ADD KEY `flat_id` (`flat_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `owners`
--
ALTER TABLE `owners`
  ADD PRIMARY KEY (`owner_id`),
  ADD UNIQUE KEY `national_id` (`national_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `rentals`
--
ALTER TABLE `rentals`
  ADD PRIMARY KEY (`rental_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `flat_id` (`flat_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `flats`
--
ALTER TABLE `flats`
  MODIFY `flat_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `flat_availability`
--
ALTER TABLE `flat_availability`
  MODIFY `availability_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `flat_marketing`
--
ALTER TABLE `flat_marketing`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `flat_marketing_info`
--
ALTER TABLE `flat_marketing_info`
  MODIFY `info_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `flat_photos`
--
ALTER TABLE `flat_photos`
  MODIFY `photo_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `marketing_info`
--
ALTER TABLE `marketing_info`
  MODIFY `marketing_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `owners`
--
ALTER TABLE `owners`
  MODIFY `owner_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=900952176;

--
-- AUTO_INCREMENT for table `rentals`
--
ALTER TABLE `rentals`
  MODIFY `rental_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`flat_id`) REFERENCES `flats` (`flat_id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`);

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `flats`
--
ALTER TABLE `flats`
  ADD CONSTRAINT `flats_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `owners` (`owner_id`);

--
-- Constraints for table `flat_availability`
--
ALTER TABLE `flat_availability`
  ADD CONSTRAINT `flat_availability_ibfk_1` FOREIGN KEY (`flat_id`) REFERENCES `flats` (`flat_id`) ON DELETE CASCADE;

--
-- Constraints for table `flat_marketing`
--
ALTER TABLE `flat_marketing`
  ADD CONSTRAINT `flat_marketing_ibfk_1` FOREIGN KEY (`flat_id`) REFERENCES `flats` (`flat_id`) ON DELETE CASCADE;

--
-- Constraints for table `flat_marketing_info`
--
ALTER TABLE `flat_marketing_info`
  ADD CONSTRAINT `flat_marketing_info_ibfk_1` FOREIGN KEY (`flat_id`) REFERENCES `flats` (`flat_id`) ON DELETE CASCADE;

--
-- Constraints for table `flat_photos`
--
ALTER TABLE `flat_photos`
  ADD CONSTRAINT `flat_photos_ibfk_1` FOREIGN KEY (`flat_id`) REFERENCES `flats` (`flat_id`);

--
-- Constraints for table `marketing_info`
--
ALTER TABLE `marketing_info`
  ADD CONSTRAINT `marketing_info_ibfk_1` FOREIGN KEY (`flat_id`) REFERENCES `flats` (`flat_id`);

--
-- Constraints for table `owners`
--
ALTER TABLE `owners`
  ADD CONSTRAINT `owners_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `rentals`
--
ALTER TABLE `rentals`
  ADD CONSTRAINT `rentals_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  ADD CONSTRAINT `rentals_ibfk_2` FOREIGN KEY (`flat_id`) REFERENCES `flats` (`flat_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
