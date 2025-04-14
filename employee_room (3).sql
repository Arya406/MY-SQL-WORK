-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 13, 2025 at 01:55 PM
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
-- Database: `employee_room`
--

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `room_no` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `description`, `room_no`) VALUES
(1, 'Finance Section', 'Finance Section', NULL),
(2, 'Pay Cell', 'Pay Cell', NULL),
(3, 'LTC Room', 'LTC Room', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `ic_no` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `designation` varchar(100) NOT NULL,
  `dob` date NOT NULL,
  `mobile_no` varchar(20) DEFAULT NULL,
  `room_allotted` enum('No','Yes') DEFAULT 'No',
  `photo` varchar(255) DEFAULT NULL,
  `temp_address` text DEFAULT NULL,
  `perm_address` text DEFAULT NULL,
  `aadhar_no` varchar(12) DEFAULT NULL,
  `pan_no` varchar(10) DEFAULT NULL,
  `bank_acc_no` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `ic_no`, `name`, `designation`, `dob`, `mobile_no`, `room_allotted`, `photo`, `temp_address`, `perm_address`, `aadhar_no`, `pan_no`, `bank_acc_no`) VALUES
(8, 'IC100', 'Rajeev Bhatnagar', 'Technical Officer C', '2000-01-01', '9999999999', 'Yes', 'uploads/Screenshot (95).png', 'DRDO(SSPL) DELHI', 'DRDO(SSPL) DELHI', '999999999999', 'ABCDE0001C', '11111111111111111111'),
(9, 'IC002', 'Rajmani', 'Technical Officer B', '2000-02-01', '9999999999', 'Yes', '', 'DRDO(SSPL) DELHI', 'DRDO(SSPL) DELHI', '999999999999', 'ABCDE0001C', '11111111111111111111'),
(10, 'IC003', 'Manish Sinha', 'Scientist E', '2000-01-01', '9999999999', 'Yes', '', 'DRDO(SSPL) DELHI', 'DRDO(SSPL) DELHI', '999999999999', 'ABCDE0001C', '11111111111111111111'),
(11, 'IC200', 'S. K. Singh', 'Scientist F', '2000-01-01', '9999999999', 'Yes', '', 'DRDO(SSPL) DELHI', 'DRDO(SSPL) DELHI', '999999999999', 'ABCDE0001C', '11111111111111111111'),
(12, 'IC005', 'Arun Tanwar', 'Scientist F', '2000-01-01', '9999999999', 'Yes', '', 'DRDO(SSPL) DELHI', 'DRDO(SSPL) DELHI', '999999999999', 'ABCDE0001C', '11111111111111111111'),
(13, 'IC006', 'Sudeep Verma', 'Scientist F', '0001-01-01', '9999999999', 'Yes', '', 'DRDO(SSPL) DELHI', 'DRDO(SSPL) DELHI', '999999999999', 'ABCDE0001C', '11111111111111111111'),
(14, 'IC007', 'Umesh SAH', 'Technical Officer A', '2000-01-01', '9999999999', 'Yes', '', 'DRDO(SSPL) DELHI', 'DRDO(SSPL) DELHI', '999999999999', 'ABCDE0001C', '11111111111111111111'),
(15, 'IC001', 'Surrender LOI', 'Technical Officer B', '2025-04-10', '9999999999', 'Yes', '', 'DRDO(SSPL) DELHI', 'DRDO(SSPL) DELHI', '999999999999', 'ABCDE0001C', '11111111111111111111'),
(16, 'IC009', 'Chirag Sharma', 'Scientist B', '2000-01-01', '9999999999', 'Yes', '', 'DRDO(SSPL) DELLHI', 'DRDO(SSPL) DELLHI', '999999999999', 'ABCDE0001C', '11111111111111111111'),
(17, 'IC010', 'Brijesh Arora', 'Scientist C', '2000-01-11', '9999999999', 'Yes', '', 'DRDO(SSPL) Delhi', 'DRDO(SSPL) Delhi', '999999999999', 'ABCDE0001C', '11111111111111111111'),
(18, 'IC011', 'Amit Kumar', 'Scientist D', '2000-01-01', '9999999999', 'Yes', '', 'DRDO(SSPL) DELHI', 'DRDO(SSPL) DELHI', '999999999999', 'ABCDE0001C', '11111111111111111111'),
(19, 'IC012', 'Shyamli Thakur', 'Scientist E', '2000-01-01', '9999999999', 'Yes', '', 'DRDO(SSPL) DELHI', 'DRDO(SSPL) DELHI', '999999999999', 'ABCDE0001C', '11111111111111111111'),
(20, 'IC013', 'Rekha Singh', 'Scientist E', '2000-01-01', '9999999999', 'Yes', '', 'DRDO(SSPL)', 'DRDO(SSPL)', '999999999999', 'ABCDE0001C', '11111111111111111111'),
(21, 'IC014', 'Deepak Semwal', 'Technical Officer B', '2000-01-01', '9999999999', 'Yes', '', 'DRDO(SSPL) DELHI', 'DRDO(SSPL) DELHI', '999999999999', 'ABCDE0001C', '11111111111111111111'),
(23, 'IC016', 'Dimple Sethi', 'Administrative Officer', '2000-01-01', '9999999999', 'Yes', '', 'DRDO(SSPL) DELHI', 'DRDO(SSPL) DELHI', '999999999999', 'ABCDE0001C', '11111111111111111111'),
(24, 'IC017', 'Surrender Pal', 'Technical Officer A', '2000-01-01', '9999999999', 'Yes', '', 'DRDO(SSPL) DELHI', 'DRDO(SSPL) DELHI', '999999999999', 'ABCDE0001C', '11111111111111111111'),
(25, 'IC018', 'Chanda Anand', 'Administrative Officer', '2000-01-01', '9999999999', 'Yes', '', 'DRDO(SSPL) DELHI', 'DRDO(SSPL) DELHI', '999999999999', 'ABCDE0001C', '11111111111111111111'),
(28, 'IC021', 'Account Officer', 'Scientist G', '2025-04-09', '8178716399', 'No', '', 'delhi', 'delhi', '783662593214', 'GLQPS5530C', '11111111111111111111'),
(29, 'IC022', 'Sanjay Rawat', 'Chief Account Officer', '2000-01-01', '9999999999', 'Yes', '', 'DRDO(SSPL) DELHI', 'DRDO(SSPL) DELHI', '999999999999', 'ABCDE0001C', '11111111111111111111');

-- --------------------------------------------------------

--
-- Stand-in structure for view `employee_room_assignments`
-- (See below for the actual view)
--
CREATE TABLE `employee_room_assignments` (
`id` int(11)
,`ic_no` varchar(50)
,`name` varchar(100)
,`designation` varchar(100)
,`room_no` varchar(50)
,`mobile_no` varchar(20)
,`photo` varchar(255)
);

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `room_no` varchar(50) DEFAULT NULL,
  `building_name` varchar(100) NOT NULL,
  `type_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `room_no`, `building_name`, `type_id`, `employee_id`) VALUES
(35, '101', 'Crystal Tower', NULL, 15),
(37, '102', 'Crystal Tower', NULL, 16),
(38, '103', 'Crystal Tower', NULL, 17),
(39, '104', 'Crystal Tower', NULL, 18),
(41, '105', 'Crystal Tower', NULL, 20),
(42, '106', 'Crystal Tower', NULL, 21),
(43, '201', 'Crystal Tower', NULL, 8),
(44, '202', 'Crystal Tower', NULL, 10),
(45, '204', 'Crystal Tower', NULL, 11),
(46, '205', 'Crystal Tower', NULL, 12),
(47, '206', 'Crystal Tower', NULL, 13),
(48, '306', 'Crystal Tower', NULL, 23),
(49, '301', 'Crystal Tower', NULL, 29),
(50, '207', 'Crystal Tower', NULL, 14),
(51, '104', 'Crystal Tower', NULL, 19),
(52, '201', 'Crystal Tower', NULL, 9),
(53, '305', 'Crystal Tower', NULL, 24),
(54, '305', 'Crystal Tower', NULL, 25);

--
-- Triggers `rooms`
--
DELIMITER $$
CREATE TRIGGER `after_room_assignment` AFTER INSERT ON `rooms` FOR EACH ROW BEGIN
    UPDATE employees SET room_allotted = 'Yes' WHERE id = NEW.employee_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_room_removal` AFTER DELETE ON `rooms` FOR EACH ROW BEGIN
    UPDATE employees SET room_allotted = 'No' WHERE id = OLD.employee_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `rooms_backup`
--

CREATE TABLE `rooms_backup` (
  `id` int(11) NOT NULL DEFAULT 0,
  `room_no` varchar(50) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `designation` varchar(100) NOT NULL,
  `mobile_no` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms_backup`
--

INSERT INTO `rooms_backup` (`id`, `room_no`, `employee_id`, `name`, `designation`, `mobile_no`) VALUES
(20, '201', 8, 'Rajeev Bhatnagar', 'Technical Officer C', '000000000'),
(23, '101', 9, 'Rajmani', 'Technical Officer B', '000000000'),
(26, '555', 11, ' S. K. Singh', 'Scientist F', '000000000'),
(27, '1', 10, 'Manish Sinha', 'Scientist E', '000000000');

-- --------------------------------------------------------

--
-- Table structure for table `room_types`
--

CREATE TABLE `room_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_shared` tinyint(1) DEFAULT 0 COMMENT '1 if multiple employees can use this room',
  `capacity` int(11) DEFAULT 1 COMMENT 'Max number of people allowed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_types`
--

INSERT INTO `room_types` (`id`, `name`, `description`, `is_shared`, `capacity`) VALUES
(1, 'Office', 'Regular office space', 0, 1),
(2, 'Conference Room', 'Meeting space', 1, 20),
(3, 'Lab', 'Research laboratory', 1, 5),
(4, 'Medical Room', 'First aid and medical care', 1, 3),
(5, 'Storage', 'Storage space', 1, NULL),
(6, 'Auditorium', 'Large gathering space', 1, 100),
(7, 'Server Room', 'IT infrastructure', 0, 1),
(8, 'LTC\r\n', '', 1, 10);

-- --------------------------------------------------------

--
-- Structure for view `employee_room_assignments`
--
DROP TABLE IF EXISTS `employee_room_assignments`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `employee_room_assignments`  AS SELECT `e`.`id` AS `id`, `e`.`ic_no` AS `ic_no`, `e`.`name` AS `name`, `e`.`designation` AS `designation`, `r`.`room_no` AS `room_no`, `e`.`mobile_no` AS `mobile_no`, `e`.`photo` AS `photo` FROM (`employees` `e` left join `rooms` `r` on(`e`.`id` = `r`.`employee_id`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ic_no` (`ic_no`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`),
  ADD KEY `fk_room_type` (`type_id`);

--
-- Indexes for table `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `fk_room_type` FOREIGN KEY (`type_id`) REFERENCES `room_types` (`id`),
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
