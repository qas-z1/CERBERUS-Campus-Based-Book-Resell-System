-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 28, 2025 at 03:21 PM
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
-- Database: `bookreselldb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `Admin_id` int(11) NOT NULL,
  `admin_name` varchar(255) NOT NULL,
  `admin_studentID` varchar(10) NOT NULL,
  `admin_email` varchar(255) NOT NULL,
  `admin_password` varchar(255) NOT NULL,
  `admin_Nophone` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`Admin_id`, `admin_name`, `admin_studentID`, `admin_email`, `admin_password`, `admin_Nophone`) VALUES
(1, 'Abu', '112233', 'ddqdsar@gmail', '123', '3133232'),
(2, 'Mhmd Amri', '123', '123@gmail', '123', '01151360998');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `Book_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `course_code` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `semester` int(7) NOT NULL,
  `price` double(6,2) NOT NULL,
  `book_condition` varchar(4) NOT NULL,
  `description` text NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL,
  `studentID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`Book_id`, `title`, `course_code`, `subject`, `semester`, `price`, `book_condition`, `description`, `image_url`, `status`, `studentID`) VALUES
(20, 'Complete Mandarin Chinese Beginner-Intermediary Course', 'TMC451', 'Introductory Mandarin Level II', 3, 20.00, 'Fair', 'Few pages scribbled with notes,some are highlighted', 'image/download (4).jpeg', 'Sold', '2332323'),
(21, 'Web Development Beginners Starter Pack', 'CSC574', 'Dynamic Web Development And Application', 4, 38.00, 'New', 'Clean cover,pages look like new,price is negotiable', 'image/webdev2.jpg', 'Available', '2332323'),
(22, 'Fundamentals to Computer Networking', 'CSC 278', 'Information Technology and Networking', 2, 50.00, 'New', 'Negotiable price,still wrapped in plastics', 'image/download (7).jpeg', 'Available', '123456'),
(23, 'Full Stack Web Development-Fundamentals and Basics of FrontEnd Programming', 'CSC 574', 'Dynamic Web Development And Application', 4, 30.00, 'Dece', '7/10 condition,fairly clean and kept in good condition', 'image/download (6).jpeg', 'Available', '123456'),
(24, 'Python Programming-Road to Mastery', 'CSC472', 'Fundamentals of Programming', 2, 15.00, 'Fair', 'kept well but old book', 'image/python book.jpg', 'Available', '123456'),
(25, 'Solo Leveling Volume 4', '-', '-', 0, 70.00, 'New', 'Freshly bought,new volume released', 'image/solo_leveling.jpg', 'Available', '2332323');

-- --------------------------------------------------------

--
-- Table structure for table `meetups`
--

CREATE TABLE `meetups` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `meetup_type` enum('Now','Scheduled') DEFAULT NULL,
  `scheduled_time` datetime DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meetups`
--

INSERT INTO `meetups` (`id`, `transaction_id`, `meetup_type`, `scheduled_time`, `location`, `created_at`) VALUES
(16, 28, 'Now', NULL, 'Cafe B', '2025-06-28 16:05:00');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` varchar(10) NOT NULL,
  `receiver_id` varchar(10) NOT NULL,
  `message` varchar(255) NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `timestamp`, `is_read`) VALUES
(52, '654321', '2332323', 'Hi, I\'m interested in your book 『Complete Mandarin Chinese Beginner-Intermediary Course』 (ID: 20) for TMC451 - is it still available?', '2025-06-28 15:46:06', 1),
(53, '2332323', '654321', 'yes still available, want any discount for the book?', '2025-06-28 15:54:17', 1),
(54, '654321', '2332323', 'of course i want a discount?how much can you give me the book for?', '2025-06-28 15:56:10', 1),
(55, '2332323', '654321', 'how about RM 20? you agree?', '2025-06-28 15:58:05', 1),
(56, '654321', '2332323', 'i agree', '2025-06-28 15:58:52', 1),
(57, '2332323', '654321', 'okay, i have updated the price, you can purchase now', '2025-06-28 16:00:27', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `studentID` varchar(10) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `transaction_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `studentID`, `message`, `is_read`, `created_at`, `transaction_id`) VALUES
(105, '654321', 'You have successfully purchased \"Complete Mandarin Chinese Beginner-Intermediary Course\".', 1, '2025-06-28 16:02:21', NULL),
(106, '2332323', 'Your book \"Complete Mandarin Chinese Beginner-Intermediary Course\" has been purchased. <a href=\'confirm_meetup.php?transaction_id=28\'>Click here to confirm the meetup</a>.', 1, '2025-06-28 16:02:21', 28),
(107, '654321', 'The seller has confirmed to meet immediately for \"Complete Mandarin Chinese Beginner-Intermediary Course\" at location: Cafe B.', 1, '2025-06-28 16:05:00', 28),
(108, '2332323', 'Reminder: After completing the meetup for \"Complete Mandarin Chinese Beginner-Intermediary Course\", <a href=\'complete_meetup.php?transaction_id=28\'>click here to mark the transaction as completed</a>.', 1, '2025-06-28 16:05:00', 28),
(109, '654321', 'The seller has marked the transaction for \"Complete Mandarin Chinese Beginner-Intermediary Course\" as completed.', 1, '2025-06-28 16:09:09', 28),
(110, '654321', 'Meet-up completed for \"Complete Mandarin Chinese Beginner-Intermediary Course\". Thank you for using CampusBooks!', 1, '2025-06-28 16:09:09', 28),
(111, '2332323', 'You have successfully completed the meetup for \"Complete Mandarin Chinese Beginner-Intermediary Course\".', 1, '2025-06-28 16:09:09', 28);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `buyer_studentID` varchar(10) NOT NULL,
  `seller_studentID` varchar(10) NOT NULL,
  `price` double(6,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `meetup_location` text NOT NULL,
  `status` varchar(20) DEFAULT 'Completed',
  `purchase_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `book_id`, `buyer_studentID`, `seller_studentID`, `price`, `payment_method`, `meetup_location`, `status`, `purchase_date`) VALUES
(28, 20, '654321', '2332323', 20.00, 'Cash on Delivery (COD)', 'Cafe B', 'Completed', '2025-06-28 16:02:21');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `User_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `studentID` varchar(10) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `usertype` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`User_id`, `username`, `studentID`, `email`, `password`, `phone`, `usertype`) VALUES
(1, 'Muhammad Amri', '2023406272', '2023406272@gmail.com', '123', '01151360998', 'buyer'),
(2, 'Muhammad Ali', '2332323', 'optictoaster229@gmail.com', '123', '0317535233', 'seller'),
(3, 'Ahmad Qasthalani Bin Mohd Hisham', '123456', '2023261724@student.uitm.edu.my', '123', '0192260545', 'seller'),
(5, 'John Wick', '654321', 'johnwick@gmail.com', '123', '01234678', 'buyer');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`Admin_id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`Book_id`);

--
-- Indexes for table `meetups`
--
ALTER TABLE `meetups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `studentID` (`studentID`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `buyer_studentID` (`buyer_studentID`),
  ADD KEY `seller_studentID` (`seller_studentID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`User_id`),
  ADD UNIQUE KEY `studentID` (`studentID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `Admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `Book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `meetups`
--
ALTER TABLE `meetups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `User_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`studentID`) REFERENCES `user` (`studentID`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`Book_id`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`buyer_studentID`) REFERENCES `user` (`studentID`),
  ADD CONSTRAINT `transactions_ibfk_3` FOREIGN KEY (`seller_studentID`) REFERENCES `user` (`studentID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
