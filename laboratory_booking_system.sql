-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 29, 2025 at 02:24 PM
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
-- Database: `laboratory_booking_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `instructor`
--

CREATE TABLE `instructor` (
  `Instructor_ID` varchar(10) NOT NULL,
  `user_id` varchar(10) DEFAULT NULL,
  `Name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `instructor`
--

INSERT INTO `instructor` (`Instructor_ID`, `user_id`, `Name`) VALUES
('IST001', 'IT1', 'Bob Smith'),
('IST002', 'IT2', 'David Ben');

-- --------------------------------------------------------

--
-- Table structure for table `lab`
--

CREATE TABLE `lab` (
  `Lab_ID` varchar(10) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Type` varchar(50) DEFAULT NULL,
  `Capacity` int(11) DEFAULT NULL,
  `Lab_TO_ID` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab`
--

INSERT INTO `lab` (`Lab_ID`, `Name`, `Type`, `Capacity`, `Lab_TO_ID`) VALUES
('CO1', 'Computer Lab', 'Java', 50, 'LTO001'),
('CO2', 'Networking Lab', 'Cisco packet', 15, 'LTO003'),
('EE1', 'Communication Lab', 'Analog & Digital', 15, 'LTO002'),
('EE2', 'Simulation Lab', 'MatLab', 45, 'LTO004');

-- --------------------------------------------------------

--
-- Table structure for table `lab_booking`
--

CREATE TABLE `lab_booking` (
  `Booking_ID` varchar(10) NOT NULL,
  `Booking_Date` timestamp NOT NULL DEFAULT current_timestamp(),
  `Student_ID` varchar(10) DEFAULT NULL,
  `Instructor_ID` varchar(10) DEFAULT NULL,
  `Schedule_ID` varchar(10) DEFAULT NULL,
  `Lecture_ID` varchar(10) DEFAULT NULL,
  `Status` enum('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_booking`
--

INSERT INTO `lab_booking` (`Booking_ID`, `Booking_Date`, `Student_ID`, `Instructor_ID`, `Schedule_ID`, `Lecture_ID`, `Status`) VALUES
('BO001', '2025-06-25 14:23:44', '2022e180', NULL, 'SCH001', NULL, ''),
('BO002', '2025-06-25 14:24:35', '2022e042', NULL, 'SCH001', NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `lab_equipment`
--

CREATE TABLE `lab_equipment` (
  `Equipment_ID` varchar(20) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Quantity` int(11) DEFAULT NULL,
  `Lab_ID` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_equipment`
--

INSERT INTO `lab_equipment` (`Equipment_ID`, `Name`, `Quantity`, `Lab_ID`) VALUES
('CDE001', 'Dekstop Computers', 60, 'CO1'),
('CDE002', 'Router', 5, 'CO2'),
('EDE001', 'Oscilloscope', 10, 'EE1'),
('EDE002', 'Signale Genarators', 10, 'EE1'),
('EDE003', 'Dekstop Computers', 55, 'EE2');

-- --------------------------------------------------------

--
-- Table structure for table `lab_schedule`
--

CREATE TABLE `lab_schedule` (
  `Schedule_ID` varchar(10) NOT NULL,
  `Date` date DEFAULT NULL,
  `Remaining_Capacity` int(11) DEFAULT NULL,
  `Start_Time` time DEFAULT NULL,
  `End_Time` time DEFAULT NULL,
  `Lab_ID` varchar(10) DEFAULT NULL,
  `Lab_TO_ID` varchar(10) DEFAULT NULL,
  `Instructor_ID` varchar(10) DEFAULT NULL,
  `Status` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_schedule`
--

INSERT INTO `lab_schedule` (`Schedule_ID`, `Date`, `Remaining_Capacity`, `Start_Time`, `End_Time`, `Lab_ID`, `Lab_TO_ID`, `Instructor_ID`, `Status`) VALUES
('SCH001', '2025-07-10', 18, '08:00:00', '11:00:00', 'EE1', 'LTO002', 'IST001', 'approved'),
('SCH002', '2025-07-15', 50, '08:00:00', '11:00:00', 'EE2', 'LTO004', 'IST002', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `lab_to`
--

CREATE TABLE `lab_to` (
  `Lab_TO_ID` varchar(10) NOT NULL,
  `user_id` varchar(10) DEFAULT NULL,
  `Name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_to`
--

INSERT INTO `lab_to` (`Lab_TO_ID`, `user_id`, `Name`) VALUES
('LTO001', 'TO1', 'Carol Evans'),
('LTO002', 'TO2', 'Saun Carl'),
('LTO003', 'TO3', 'Migar Shewn'),
('LTO004', 'TO4', 'Vimukthi Devon');

-- --------------------------------------------------------

--
-- Table structure for table `lecture`
--

CREATE TABLE `lecture` (
  `Lecture_ID` varchar(10) NOT NULL,
  `user_id` varchar(10) DEFAULT NULL,
  `Name` varchar(100) NOT NULL,
  `Department` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecture`
--

INSERT INTO `lecture` (`Lecture_ID`, `user_id`, `Name`, `Department`) VALUES
('LCT001', 'L1', 'John Don', 'Computer'),
('LCT002', 'L2', 'Martin Sin', 'Electrical');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `Student_ID` varchar(10) NOT NULL,
  `user_id` varchar(10) DEFAULT NULL,
  `Name` varchar(100) NOT NULL,
  `Semester` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`Student_ID`, `user_id`, `Name`, `Semester`) VALUES
('2022e033', 'ST3', 'Nayanaka Dayarathna', '5'),
('2022e042', 'ST2', 'Pasindu Ranasingha', '5'),
('2022e180', 'ST1', 'Thisanda Prasanjana', '5'),
('2022e182', 'ST4', 'Ashan Madushanka', '5');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` varchar(10) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','instructor','lab_to','lecture') NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `status`, `created_at`) VALUES
('IT1', 'bob', 'password456', 'instructor', 'active', '2025-06-08 02:57:33'),
('IT2', 'david', 'password457', 'instructor', 'active', '2025-06-08 02:57:33'),
('L1', 'John', 'password987', 'lecture', 'active', '2025-06-08 02:57:33'),
('L2', 'martin', 'password988', 'lecture', 'active', '2025-06-08 02:57:33'),
('ST1', 'thisanda', 'password123', 'student', 'active', '2025-06-08 02:57:33'),
('ST2', 'pasindu', 'password122', 'student', 'active', '2025-06-08 02:57:33'),
('ST3', 'nayanaka', '1234', 'student', 'active', '2025-06-24 13:58:35'),
('ST4', 'ashan', '4321', 'student', 'active', '2025-06-24 14:00:32'),
('TO1', 'carol', 'password789', 'lab_to', 'active', '2025-06-08 02:57:33'),
('TO2', 'saun', 'password790', 'lab_to', 'active', '2025-06-08 02:57:33'),
('TO3', 'migar', 'migar1', 'lab_to', 'active', '2025-06-24 15:31:39'),
('TO4', 'vimukthi', 'vimukthi1', 'lab_to', 'active', '2025-06-24 15:31:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `instructor`
--
ALTER TABLE `instructor`
  ADD PRIMARY KEY (`Instructor_ID`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `lab`
--
ALTER TABLE `lab`
  ADD PRIMARY KEY (`Lab_ID`),
  ADD KEY `Lab_TO_ID` (`Lab_TO_ID`),
  ADD KEY `idx_lab_type` (`Type`);

--
-- Indexes for table `lab_booking`
--
ALTER TABLE `lab_booking`
  ADD PRIMARY KEY (`Booking_ID`),
  ADD KEY `Student_ID` (`Student_ID`),
  ADD KEY `Instructor_ID` (`Instructor_ID`),
  ADD KEY `Schedule_ID` (`Schedule_ID`),
  ADD KEY `Lecture_ID` (`Lecture_ID`),
  ADD KEY `idx_booking_date` (`Booking_Date`);

--
-- Indexes for table `lab_equipment`
--
ALTER TABLE `lab_equipment`
  ADD PRIMARY KEY (`Equipment_ID`),
  ADD KEY `Lab_ID` (`Lab_ID`);

--
-- Indexes for table `lab_schedule`
--
ALTER TABLE `lab_schedule`
  ADD PRIMARY KEY (`Schedule_ID`),
  ADD KEY `Lab_ID` (`Lab_ID`),
  ADD KEY `Lab_TO_ID` (`Lab_TO_ID`),
  ADD KEY `idx_schedule_date` (`Date`),
  ADD KEY `idx_schedule_time` (`Start_Time`,`End_Time`),
  ADD KEY `fk_lab_schedule_instructor` (`Instructor_ID`);

--
-- Indexes for table `lab_to`
--
ALTER TABLE `lab_to`
  ADD PRIMARY KEY (`Lab_TO_ID`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `lecture`
--
ALTER TABLE `lecture`
  ADD PRIMARY KEY (`Lecture_ID`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`Student_ID`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_student_semester` (`Semester`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_user_role` (`role`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `instructor`
--
ALTER TABLE `instructor`
  ADD CONSTRAINT `instructor_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `lab`
--
ALTER TABLE `lab`
  ADD CONSTRAINT `lab_ibfk_1` FOREIGN KEY (`Lab_TO_ID`) REFERENCES `lab_to` (`Lab_TO_ID`);

--
-- Constraints for table `lab_booking`
--
ALTER TABLE `lab_booking`
  ADD CONSTRAINT `lab_booking_ibfk_1` FOREIGN KEY (`Student_ID`) REFERENCES `student` (`Student_ID`),
  ADD CONSTRAINT `lab_booking_ibfk_2` FOREIGN KEY (`Instructor_ID`) REFERENCES `instructor` (`Instructor_ID`),
  ADD CONSTRAINT `lab_booking_ibfk_3` FOREIGN KEY (`Schedule_ID`) REFERENCES `lab_schedule` (`Schedule_ID`),
  ADD CONSTRAINT `lab_booking_ibfk_4` FOREIGN KEY (`Lecture_ID`) REFERENCES `lecture` (`Lecture_ID`);

--
-- Constraints for table `lab_equipment`
--
ALTER TABLE `lab_equipment`
  ADD CONSTRAINT `lab_equipment_ibfk_1` FOREIGN KEY (`Lab_ID`) REFERENCES `lab` (`Lab_ID`);

--
-- Constraints for table `lab_schedule`
--
ALTER TABLE `lab_schedule`
  ADD CONSTRAINT `fk_lab_schedule_instructor` FOREIGN KEY (`Instructor_ID`) REFERENCES `instructor` (`Instructor_ID`),
  ADD CONSTRAINT `lab_schedule_ibfk_1` FOREIGN KEY (`Lab_ID`) REFERENCES `lab` (`Lab_ID`),
  ADD CONSTRAINT `lab_schedule_ibfk_2` FOREIGN KEY (`Lab_TO_ID`) REFERENCES `lab_to` (`Lab_TO_ID`);

--
-- Constraints for table `lab_to`
--
ALTER TABLE `lab_to`
  ADD CONSTRAINT `lab_to_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `lecture`
--
ALTER TABLE `lecture`
  ADD CONSTRAINT `lecture_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
