-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 14, 2026 at 02:06 PM
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
-- Database: `web_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `show_id` int(11) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `total_seats` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `booking_date` date NOT NULL,
  `has_kids` tinyint(1) NOT NULL DEFAULT 0,
  `kids_count` int(11) DEFAULT 0,
  `adults_count` int(11) DEFAULT 0,
  `payment_status` varchar(50) DEFAULT 'pending',
  `booking_status` varchar(50) DEFAULT 'confirmed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `show_id`, `class_id`, `total_seats`, `total_price`, `booking_date`, `has_kids`, `kids_count`, `adults_count`, `payment_status`, `booking_status`) VALUES
(21, 2, 6, 1, 1, 900.00, '2026-05-08', 0, 0, 1, 'pending', 'confirmed'),
(22, 2, 6, 1, 1, 900.00, '2026-05-08', 0, 0, 1, 'pending', 'confirmed'),
(23, 2, 6, 1, 4, 3600.00, '2026-05-09', 0, 0, 4, 'pending', 'confirmed'),
(24, 2, 6, 4, 2, 1800.00, '2026-05-12', 0, 0, 2, 'pending', 'confirmed'),
(25, 2, 6, 4, 2, 1350.00, '2026-05-12', 1, 1, 1, 'pending', 'confirmed'),
(26, 2, 6, 4, 3, 2700.00, '2026-05-12', 0, 0, 3, 'pending', 'confirmed');

-- --------------------------------------------------------

--
-- Table structure for table `booking_seats`
--

CREATE TABLE `booking_seats` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `seat_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_seats`
--

INSERT INTO `booking_seats` (`id`, `booking_id`, `seat_id`) VALUES
(24, 21, 576),
(25, 22, 577),
(26, 23, 578),
(27, 23, 579),
(28, 23, 580),
(29, 23, 581),
(30, 24, 556),
(31, 24, 557),
(32, 25, 558),
(33, 25, 559),
(34, 26, 560),
(35, 26, 561),
(36, 26, 562);

-- --------------------------------------------------------

--
-- Table structure for table `carousel`
--

CREATE TABLE `carousel` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `status` varchar(100) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `category_name`, `status`) VALUES
(1, 'Action ', 'Active'),
(2, 'Triller', 'Active'),
(5, 'Comedy', 'Active'),
(6, 'Drama', 'Active'),
(9, 'Marvel', 'Active'),
(14, 'Horror', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `class_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `class_name`) VALUES
(1, 'Gold'),
(2, 'Platinum'),
(4, 'Box Office');

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `id` int(11) NOT NULL,
  `poster` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `trailer_link` varchar(255) NOT NULL,
  `movie_desc` varchar(255) NOT NULL,
  `duration` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `release_date` date DEFAULT NULL,
  `director` varchar(255) DEFAULT NULL,
  `rating` varchar(50) DEFAULT NULL,
  `language` varchar(100) DEFAULT NULL,
  `movie_status` varchar(20) NOT NULL DEFAULT 'now_showing',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `genre` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`id`, `poster`, `title`, `trailer_link`, `movie_desc`, `duration`, `created_at`, `release_date`, `director`, `rating`, `language`, `movie_status`, `is_featured`, `genre`) VALUES
(1, 'poster_69fb6a5184f78.jpg', 'Hera Pheri 2', 'https://www.youtube.com/watch?v=Im_lCAsA27Q', 'Phir Hera Pheri (2006) is a Bollywood comedy-crime film and the sequel to the 2000 cult classic Hera Pheri. Written and directed by Neeraj Vora, the film continues the misadventures of the iconic trio—Raju (Akshay Kumar), Shyam (Suniel Shetty), and Babura', '3 hour 5 minute', '2026-05-13 16:51:21', '2026-06-12', 'John Deo', '2.000000000', 'Urdu', 'now_showing', 0, NULL),
(5, 'poster_69fb6c4c7bec1.jpg', 'Toy Story', 'https://www.youtube.com/watch?v=c51ND9Hdbw0', 'Toy Story (1995) is a groundbreaking computer-animated film from Pixar about the secret life of toys. When a lanky cowboy doll named Woody (Tom Hanks) feels threatened by a new, flashy spaceman action figure, Buzz Lightyear (Tim Allen), his jealousy lands', '4 hour 30 minute', '2026-05-13 17:07:27', '2026-07-09', 'Ghaffar', '2.000000000', 'English,Urdu', 'upcoming', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `movie_category`
--

CREATE TABLE `movie_category` (
  `id` int(11) NOT NULL,
  `movi_id` int(11) NOT NULL,
  `cat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movie_category`
--

INSERT INTO `movie_category` (`id`, `movi_id`, `cat_id`) VALUES
(11, 1, 5);

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `id` int(11) NOT NULL,
  `users_id` int(11) DEFAULT NULL,
  `movies_id` int(11) DEFAULT NULL,
  `review` text NOT NULL,
  `rating` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`id`, `users_id`, `movies_id`, `review`, `rating`, `created_at`) VALUES
(2, 2, 5, 'good show', 2, '2026-05-08 13:32:00'),
(4, 2, 1, 'good movie', 2, '2026-05-13 16:51:21');

-- --------------------------------------------------------

--
-- Table structure for table `seats`
--

CREATE TABLE `seats` (
  `id` int(11) NOT NULL,
  `theater_id` int(11) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `seat_number` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seats`
--

INSERT INTO `seats` (`id`, `theater_id`, `class_id`, `seat_number`) VALUES
(556, 3, 4, 'S1'),
(557, 3, 4, 'S2'),
(558, 3, 4, 'S3'),
(559, 3, 4, 'S4'),
(560, 3, 4, 'S5'),
(561, 3, 4, 'S6'),
(562, 3, 4, 'S7'),
(563, 3, 4, 'S8'),
(564, 3, 4, 'S9'),
(565, 3, 4, 'S10'),
(566, 3, 2, 'S1'),
(567, 3, 2, 'S2'),
(568, 3, 2, 'S3'),
(569, 3, 2, 'S4'),
(570, 3, 2, 'S5'),
(571, 3, 2, 'S6'),
(572, 3, 2, 'S7'),
(573, 3, 2, 'S8'),
(574, 3, 2, 'S9'),
(575, 3, 2, 'S10'),
(576, 3, 1, 'S1'),
(577, 3, 1, 'S2'),
(578, 3, 1, 'S3'),
(579, 3, 1, 'S4'),
(580, 3, 1, 'S5'),
(581, 3, 1, 'S6'),
(582, 3, 1, 'S7'),
(583, 3, 1, 'S8'),
(584, 3, 1, 'S9'),
(585, 3, 1, 'S10'),
(586, 4, 4, 'S1'),
(587, 4, 4, 'S2'),
(588, 4, 4, 'S3'),
(589, 4, 2, 'S1'),
(590, 4, 2, 'S2'),
(591, 4, 2, 'S3'),
(592, 4, 1, 'S1'),
(593, 4, 1, 'S2'),
(594, 4, 1, 'S3');

-- --------------------------------------------------------

--
-- Table structure for table `shows`
--

CREATE TABLE `shows` (
  `id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `theater_id` int(11) NOT NULL,
  `show_date` date DEFAULT NULL,
  `show_time` time DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shows`
--

INSERT INTO `shows` (`id`, `movie_id`, `theater_id`, `show_date`, `show_time`, `created_at`) VALUES
(6, 1, 3, '2026-06-27', '12:00:00', '2026-05-08 18:00:16'),
(345, 1, 4, '2026-05-16', '17:07:00', '2026-05-14 12:01:35');

-- --------------------------------------------------------

--
-- Table structure for table `show_class_pricing`
--

CREATE TABLE `show_class_pricing` (
  `id` int(11) NOT NULL,
  `show_id` int(11) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `show_class_pricing`
--

INSERT INTO `show_class_pricing` (`id`, `show_id`, `class_id`, `price`) VALUES
(18, 6, 4, 900.00),
(19, 6, 2, 900.00),
(20, 6, 1, 900.00),
(1033, 345, 4, 900.00),
(1034, 345, 2, 1200.00),
(1035, 345, 1, 1500.00),
(1036, 345, 4, 0.00),
(1037, 345, 2, 0.00),
(1038, 345, 1, 0.00),
(1039, 345, 4, 900.00),
(1040, 345, 2, 1200.00),
(1041, 345, 1, 1500.00);

-- --------------------------------------------------------

--
-- Table structure for table `theaters`
--

CREATE TABLE `theaters` (
  `id` int(11) NOT NULL,
  `theater_name` varchar(255) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `Created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `screens` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `theaters`
--

INSERT INTO `theaters` (`id`, `theater_name`, `location`, `Created_at`, `screens`) VALUES
(3, 'Nuplex Cinema', 'University Road', '2026-05-06 19:06:01', 30),
(4, 'Capri Cinema', 'Tariq Road', '2026-05-06 19:28:36', 9);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `phone` varchar(30) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `phone`, `status`) VALUES
(1, 'fariha', 'fariha@gmail.com', '123456', 'user', '2026-05-06 16:44:48', '03152453522', 'Active'),
(2, 'john deo', 'john@gmail.com', 'sadiq987123', 'admin', '2026-05-06 19:16:19', '03152453522', 'Active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `show_id` (`show_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `booking_seats`
--
ALTER TABLE `booking_seats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `seat_id` (`seat_id`);

--
-- Indexes for table `carousel`
--
ALTER TABLE `carousel`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `movie_category`
--
ALTER TABLE `movie_category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_movie_category` (`movi_id`,`cat_id`),
  ADD KEY `cat_id` (`cat_id`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_id` (`users_id`),
  ADD KEY `movies_id` (`movies_id`);

--
-- Indexes for table `seats`
--
ALTER TABLE `seats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `theater_id` (`theater_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `shows`
--
ALTER TABLE `shows`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movie_id` (`movie_id`),
  ADD KEY `theater_id` (`theater_id`);

--
-- Indexes for table `show_class_pricing`
--
ALTER TABLE `show_class_pricing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `show_id` (`show_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `theaters`
--
ALTER TABLE `theaters`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `booking_seats`
--
ALTER TABLE `booking_seats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `carousel`
--
ALTER TABLE `carousel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `movie_category`
--
ALTER TABLE `movie_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `seats`
--
ALTER TABLE `seats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=934;

--
-- AUTO_INCREMENT for table `shows`
--
ALTER TABLE `shows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=346;

--
-- AUTO_INCREMENT for table `show_class_pricing`
--
ALTER TABLE `show_class_pricing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1042;

--
-- AUTO_INCREMENT for table `theaters`
--
ALTER TABLE `theaters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`show_id`) REFERENCES `shows` (`id`),
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`);

--
-- Constraints for table `booking_seats`
--
ALTER TABLE `booking_seats`
  ADD CONSTRAINT `booking_seats_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`),
  ADD CONSTRAINT `booking_seats_ibfk_2` FOREIGN KEY (`seat_id`) REFERENCES `seats` (`id`);

--
-- Constraints for table `movie_category`
--
ALTER TABLE `movie_category`
  ADD CONSTRAINT `movie_category_ibfk_1` FOREIGN KEY (`movi_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `movie_category_ibfk_2` FOREIGN KEY (`cat_id`) REFERENCES `category` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `review_ibfk_2` FOREIGN KEY (`movies_id`) REFERENCES `movies` (`id`);

--
-- Constraints for table `seats`
--
ALTER TABLE `seats`
  ADD CONSTRAINT `seats_ibfk_1` FOREIGN KEY (`theater_id`) REFERENCES `theaters` (`id`),
  ADD CONSTRAINT `seats_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`);

--
-- Constraints for table `shows`
--
ALTER TABLE `shows`
  ADD CONSTRAINT `shows_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`),
  ADD CONSTRAINT `shows_ibfk_2` FOREIGN KEY (`theater_id`) REFERENCES `theaters` (`id`);

--
-- Constraints for table `show_class_pricing`
--
ALTER TABLE `show_class_pricing`
  ADD CONSTRAINT `show_class_pricing_ibfk_1` FOREIGN KEY (`show_id`) REFERENCES `shows` (`id`),
  ADD CONSTRAINT `show_class_pricing_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
