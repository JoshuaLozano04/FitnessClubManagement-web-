-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 26, 2025 at 02:22 PM
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
-- Database: `fitnessclubmanagement_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_notifications`
--

CREATE TABLE `admin_notifications` (
  `id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `read_status` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_notifications`
--

INSERT INTO `admin_notifications` (`id`, `message`, `created_at`, `read_status`) VALUES
(67, 'New trainer request from Joshua Lozano for March 28, 2025 at 13:30:00', '2025-03-26 06:17:25', 0);

-- --------------------------------------------------------

--
-- Table structure for table `check_ins`
--

CREATE TABLE `check_ins` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `check_in_time` datetime NOT NULL,
  `check_out_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `check_ins`
--
DELIMITER $$
CREATE TRIGGER `after_check_out_update_stats` AFTER UPDATE ON `check_ins` FOR EACH ROW BEGIN
    IF NEW.check_out_time IS NOT NULL THEN
        -- Update total workout time
        UPDATE workout_stats
        SET total_workout_time = SEC_TO_TIME(
            COALESCE(TIME_TO_SEC(total_workout_time), 0) + 
            TIMESTAMPDIFF(SECOND, NEW.check_in_time, NEW.check_out_time)
        ),
        -- Update longest session if the new session is longer
        longest_session = GREATEST(
            COALESCE(longest_session, '00:00:00'), 
            SEC_TO_TIME(TIMESTAMPDIFF(SECOND, NEW.check_in_time, NEW.check_out_time))
        ),
        last_updated = NOW()
        WHERE member_id = NEW.member_id;

        -- If no record exists, insert a new one
        INSERT INTO workout_stats (member_id, total_workout_time, longest_session)
        SELECT NEW.member_id, 
               SEC_TO_TIME(TIMESTAMPDIFF(SECOND, NEW.check_in_time, NEW.check_out_time)), 
               SEC_TO_TIME(TIMESTAMPDIFF(SECOND, NEW.check_in_time, NEW.check_out_time))
        WHERE NOT EXISTS (SELECT 1 FROM workout_stats WHERE member_id = NEW.member_id);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `email_confirmation`
--

CREATE TABLE `email_confirmation` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `code` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expired` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `email_confirmation`
--

INSERT INTO `email_confirmation` (`id`, `email`, `code`, `created_at`, `expired`) VALUES
(11, 'joshuasuelenlozano.ml@gmail.com', 1374, '2025-02-21 05:00:21', '2025-02-21 05:00:21'),
(12, 'joshuasuelenlozano.ml@gmail.com', 2593, '2025-02-21 05:04:49', '2025-02-21 05:04:49'),
(13, 'joshuasuelenlozano.ml@gmail.com', 2468, '2025-02-21 05:18:48', '2025-02-21 05:18:48'),
(14, 'joshuasuelenlozano@gmail.com', 1187, '2025-02-21 05:21:59', '2025-02-21 05:21:59'),
(20, 'joshuasuelenlozano.ml@gmail.com', 1991, '2025-02-21 05:47:31', '2025-02-21 05:47:31'),
(22, 'joshuasuelenlozano.ml@gmail.com', 8129, '2025-02-21 05:50:23', '2025-02-21 05:50:23'),
(23, 'joshuasuelenlozano.ml@gmail.com', 9784, '2025-02-21 05:50:23', '2025-02-21 05:50:23'),
(26, 'joshuasuelenlozano.ml@gmail.com', 6820, '2025-02-21 06:03:42', '2025-02-21 06:03:42'),
(28, 'melchizedek.lozano@gmail.com', 1586, '2025-03-07 06:31:43', '2025-03-07 06:31:43'),
(29, 'melchizedek.lozano@gmail.com', 3647, '2025-03-07 06:33:27', '2025-03-07 06:33:27'),
(31, 'rlmamaril58@gmail.com', 6443, '2025-03-21 07:19:44', '2025-03-21 07:19:44'),
(32, 'melchizedek.lozano@gmail.com', 5312, '2025-03-21 07:47:14', '2025-03-21 07:47:14');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `product_image` varchar(255) DEFAULT 'default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `product_name`, `description`, `price`, `stock_quantity`, `created_at`, `product_image`) VALUES
(18, 'Optimum Nutrition Gold Standard 100% Whey', 'Fuel your fitness goals with Optimum Nutrition Gold Standard 100% Whey, the world’s best-selling whey protein powder. Packed with 24g of high-quality whey protein per serving, including whey protein isolate as the primary source, it supports muscle growth, recovery, and performance.', 3990.00, 50, '2025-03-18 05:21:42', 'dawAwdawdsfwa.png'),
(19, 'Optimum Nutrition Serious Mass', 'Optimum Nutrition Serious Mass is a high-calorie weight gainer supplement designed for muscle building and weight gain. It contains a blend of protein, carbohydrates, and essential nutrients, providing around 1,250 calories per serving. Ideal for athletes and individuals struggling to gain weight, it supports muscle recovery and growth when combined with a proper workout routine.', 2410.00, 50, '2025-03-21 06:43:31', '475193979_1134930564798131_4769892897071539988_n.png'),
(20, 'MuscleTech Platinum 100% Creatine', 'MuscleTech Platinum 100% Creatine is a high-quality micronized creatine monohydrate supplement designed to enhance strength, muscle growth, and recovery. It helps replenish ATP stores, improving performance during intense workouts. This unflavored formula mixes easily and supports increased power, endurance, and lean muscle gains.', 1650.00, 50, '2025-03-21 06:45:32', '476194990_1284354009291075_2673779080707750565_n.png');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `membership_start` date NOT NULL,
  `membership_end` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `monthly_highlights`
--

CREATE TABLE `monthly_highlights` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `caption` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `monthly_highlights`
--

INSERT INTO `monthly_highlights` (`id`, `user_email`, `name`, `caption`, `created_at`) VALUES
(11, 'isla@gmail.com', 'Jonas Isla', 'SHEEESH FR FR ONG', '2025-02-26 10:21:04'),
(12, 'isla@gmail.com', 'Jonas Isla', 'Sheesh', '2025-02-26 10:35:27'),
(13, 'alonzo@gmail.com', 'Alexius Alonzo', 'Sheeshable', '2025-03-16 06:07:41'),
(14, 'isla@gmail.com', 'Jonas Isla', 'Hello!', '2025-03-21 07:24:05');

-- --------------------------------------------------------

--
-- Table structure for table `monthly_highlight_images`
--

CREATE TABLE `monthly_highlight_images` (
  `id` int(11) NOT NULL,
  `highlight_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `monthly_highlight_images`
--

INSERT INTO `monthly_highlight_images` (`id`, `highlight_id`, `image_url`) VALUES
(27, 11, 'uploads/1000303993.jpg'),
(28, 11, 'uploads/1000303992.jpg'),
(29, 11, 'uploads/1000300580.jpg'),
(30, 11, 'uploads/1000300581.jpg'),
(31, 12, 'uploads/1000303942.jpg'),
(32, 12, 'uploads/1000241269.jpg'),
(33, 13, 'uploads/33.jpg'),
(34, 14, 'uploads/34.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp(),
  `quantity` int(11) NOT NULL,
  `order_status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `order_date` date DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `product_picture` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `status` enum('Pending','Ready for Pickup','Picked Up','Unclaimed') DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`id`, `customer_name`, `order_date`, `product_name`, `product_picture`, `price`, `quantity`, `status`) VALUES
(7, 'Jonas Isla', '2025-03-23', 'Optimum Nutrition Serious Mass', '475193979_1134930564798131_4769892897071539988_n.png', 4820.00, 2, 'Pending'),
(8, 'Joshua Lozano', '2025-03-25', 'Optimum Nutrition Gold Standard 100% Whey', 'dawAwdawdsfwa.png', 3990.00, 1, 'Pending'),
(6, 'Jonas Isla', '2025-03-23', 'MuscleTech Platinum 100% Creatine', '476194990_1284354009291075_2673779080707750565_n.png', 1650.00, 1, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `trainer_id` int(11) NOT NULL,
  `trainee_id` int(11) NOT NULL,
  `session_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `schedules`
--
DELIMITER $$
CREATE TRIGGER `after_schedule_insert` AFTER INSERT ON `schedules` FOR EACH ROW BEGIN
    INSERT INTO notifications (user_id, message)
    VALUES (
        (SELECT user_id FROM members WHERE id = NEW.trainee_id),
        CONCAT('New training session scheduled on ', NEW.session_date, ' at ', NEW.start_time)
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `trainers`
--

CREATE TABLE `trainers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `specialty` varchar(255) DEFAULT NULL,
  `experience_years` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trainers_about`
--

CREATE TABLE `trainers_about` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `about` text DEFAULT 'Update your about info',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainers_about`
--

INSERT INTO `trainers_about` (`id`, `name`, `email`, `about`, `created_at`, `updated_at`) VALUES
(1, '', 'isla@gmail.com', 'Hello! I\'m Jonas Isla, a passionate fitness trainer dedicated to helping individuals achieve their health and wellness goals. With a strong background in strength training, functional fitness, and nutrition, I focus on creating personalized programs tailored to each client\'s needs.', '2025-03-24 13:48:06', '2025-03-25 11:13:32');

-- --------------------------------------------------------

--
-- Table structure for table `trainer_assignments`
--

CREATE TABLE `trainer_assignments` (
  `assignment_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `trainer_email` varchar(255) NOT NULL,
  `trainer_name` varchar(255) NOT NULL,
  `assignment_date` date NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainer_assignments`
--

INSERT INTO `trainer_assignments` (`assignment_id`, `request_id`, `user_email`, `user_name`, `trainer_email`, `trainer_name`, `assignment_date`, `status`, `start_time`, `end_time`) VALUES
(21, 9, 'im@gmail.com', 'Isaiah Menor', 'isla@gmail.com', 'Jonas Isla', '2025-03-23', 'approved', '08:00:00', '10:00:00'),
(23, 14, 'melchizedek.lozano@gmail.com', 'Joshua Lozano', 'isla@gmail.com', 'Joshua Lozano', '2025-03-28', 'approved', '08:00:00', '11:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `trainer_request`
--

CREATE TABLE `trainer_request` (
  `request_id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `trainer_name` varchar(255) DEFAULT NULL,
  `user_name` varchar(255) NOT NULL,
  `request_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(255) DEFAULT 'pending',
  `date_of_training` date DEFAULT NULL,
  `time_start` time DEFAULT NULL,
  `time_end` time DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `trainer_email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainer_request`
--

INSERT INTO `trainer_request` (`request_id`, `user_email`, `trainer_name`, `user_name`, `request_date`, `status`, `date_of_training`, `time_start`, `time_end`, `description`, `trainer_email`) VALUES
(9, 'im@gmail.com', 'Jonas Isla', 'Isaiah Menor', '2025-03-18 16:21:58', 'approved', '2025-03-23', '08:00:00', '10:00:00', 'Muscle gain', 'isla@gmail.com'),
(14, 'melchizedek.lozano@gmail.com', 'Joshua Lozano', 'Joshua Lozano', '2025-03-26 17:37:44', 'approved', '2025-03-28', '08:00:00', '11:00:00', 'Weight Loss', 'isla@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `trainer_reviews`
--

CREATE TABLE `trainer_reviews` (
  `id` int(11) NOT NULL,
  `trainee_id` int(11) NOT NULL,
  `trainer_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `profile_picture` varchar(255) NOT NULL DEFAULT 'default.png',
  `fullname` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('member','trainer','admin') DEFAULT 'member',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `token` varchar(255) DEFAULT NULL,
  `membership_start` date DEFAULT NULL,
  `membership_end` date DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'inactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `profile_picture`, `fullname`, `email`, `password`, `role`, `created_at`, `token`, `membership_start`, `membership_end`, `status`) VALUES
(8, 'default.png', 'Isaiah Menor', 'im@gmail.com', '$2y$10$BGvNIqnrqLcIfILF.wRtNej9JeFGgoWUStWYpgeScAUmdSdUih1O.', 'member', '2025-02-03 12:29:48', NULL, '2025-02-14', '2025-03-14', 'active'),
(15, 'default.png', 'Pumping Iron Gym', 'pumpitjonathan66@gmail.com', '$2y$10$OOqW0NVzlLwmofL/5/Jb1.FrjJ58t25W2z49y1PjqSMOufUcSPpgO', 'admin', '2025-02-14 13:20:25', NULL, NULL, NULL, 'inactive'),
(16, '67e28b2d7c5f2.jpg', 'Joshua Lozano', 'isla@gmail.com', '$2y$10$A8FF1FrGMrEwHzcUAMRqmO5vHd1nLMHCEJf0m/GqWDFPr/3.hOq4m', 'trainer', '2025-02-17 06:03:04', '1f0f32eadac29c9447696e1e72ef4f22', NULL, NULL, 'inactive'),
(17, 'default.png', 'Alexius Alonzo', 'alonzo@gmail.com', '$2y$10$Z863xHQQfD9JK6eIbjdXLONEpS2AJYQu.mBd6neSbMyrPz0vo.XP.', 'trainer', '2025-02-17 13:42:05', '76a6bbc26da8e6026e61062f77bfe3f5', NULL, NULL, 'inactive'),
(19, 'default.png', 'Joshua Lozano', 'joshuasuelenlozano.ml@gmail.com', '$2y$10$JFgtdI4ESc/mmMHfPxgRK.4gXwVwewMt.96mUxcCQaakRms78eZOO', 'member', '2025-02-21 06:05:33', 'af993c12f168bc533bff9cff138043a9', '2025-02-21', '2025-03-21', 'inactive'),
(21, 'default.png', 'Joshua Lozano', 'melchizedek.lozano@gmail.com', '$2y$10$Px7U0zM/sw42iwl/AxxCuuvt/fr0rAu7wtPWfD1fOGGjWSAaksNdG', 'member', '2025-03-21 07:50:15', '4d2b00d6a92153ae0c8e6370748e761b', NULL, NULL, 'inactive');

-- --------------------------------------------------------

--
-- Table structure for table `workout_stats`
--

CREATE TABLE `workout_stats` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `total_workout_time` time DEFAULT '00:00:00',
  `longest_session` time DEFAULT '00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `check_ins`
--
ALTER TABLE `check_ins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `email_confirmation`
--
ALTER TABLE `email_confirmation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `monthly_highlights`
--
ALTER TABLE `monthly_highlights`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_email` (`user_email`);

--
-- Indexes for table `monthly_highlight_images`
--
ALTER TABLE `monthly_highlight_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `highlight_id` (`highlight_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trainer_id` (`trainer_id`),
  ADD KEY `trainee_id` (`trainee_id`);

--
-- Indexes for table `trainers`
--
ALTER TABLE `trainers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `trainers_about`
--
ALTER TABLE `trainers_about`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `trainer_assignments`
--
ALTER TABLE `trainer_assignments`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `request_id` (`request_id`);

--
-- Indexes for table `trainer_request`
--
ALTER TABLE `trainer_request`
  ADD PRIMARY KEY (`request_id`);

--
-- Indexes for table `trainer_reviews`
--
ALTER TABLE `trainer_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trainee_id` (`trainee_id`),
  ADD KEY `trainer_id` (`trainer_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `workout_stats`
--
ALTER TABLE `workout_stats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `member_id` (`member_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `check_ins`
--
ALTER TABLE `check_ins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_confirmation`
--
ALTER TABLE `email_confirmation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `monthly_highlights`
--
ALTER TABLE `monthly_highlights`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `monthly_highlight_images`
--
ALTER TABLE `monthly_highlight_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trainers`
--
ALTER TABLE `trainers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trainers_about`
--
ALTER TABLE `trainers_about`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `trainer_assignments`
--
ALTER TABLE `trainer_assignments`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `trainer_request`
--
ALTER TABLE `trainer_request`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `trainer_reviews`
--
ALTER TABLE `trainer_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `workout_stats`
--
ALTER TABLE `workout_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `check_ins`
--
ALTER TABLE `check_ins`
  ADD CONSTRAINT `check_ins_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `members_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `monthly_highlights`
--
ALTER TABLE `monthly_highlights`
  ADD CONSTRAINT `monthly_highlights_ibfk_1` FOREIGN KEY (`user_email`) REFERENCES `users` (`email`) ON DELETE CASCADE;

--
-- Constraints for table `monthly_highlight_images`
--
ALTER TABLE `monthly_highlight_images`
  ADD CONSTRAINT `monthly_highlight_images_ibfk_1` FOREIGN KEY (`highlight_id`) REFERENCES `monthly_highlights` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_ibfk_2` FOREIGN KEY (`trainee_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `trainers`
--
ALTER TABLE `trainers`
  ADD CONSTRAINT `trainers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `trainer_assignments`
--
ALTER TABLE `trainer_assignments`
  ADD CONSTRAINT `trainer_assignments_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `trainer_request` (`request_id`);

--
-- Constraints for table `trainer_reviews`
--
ALTER TABLE `trainer_reviews`
  ADD CONSTRAINT `trainer_reviews_ibfk_1` FOREIGN KEY (`trainee_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trainer_reviews_ibfk_2` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `inventory` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `workout_stats`
--
ALTER TABLE `workout_stats`
  ADD CONSTRAINT `workout_stats_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
