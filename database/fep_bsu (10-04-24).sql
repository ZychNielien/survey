-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 04, 2024 at 02:51 PM
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
-- Database: `fep_bsu`
--

-- --------------------------------------------------------

--
-- Table structure for table `assigned_subject`
--

CREATE TABLE `assigned_subject` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `day_id` int(11) NOT NULL,
  `S_time_id` int(11) NOT NULL,
  `E_time_id` int(11) NOT NULL,
  `day_id_2` int(11) NOT NULL,
  `S_time_id_2` int(11) NOT NULL,
  `E_time_id_2` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assigned_subject`
--

INSERT INTO `assigned_subject` (`id`, `subject_id`, `faculty_id`, `section_id`, `day_id`, `S_time_id`, `E_time_id`, `day_id_2`, `S_time_id_2`, `E_time_id_2`) VALUES
(17, 1, 1, 16, 1, 4, 7, 0, 0, 0),
(18, 52, 13, 1, 4, 1, 3, 0, 0, 0),
(19, 50, 5, 1, 4, 4, 6, 0, 0, 0),
(20, 1, 1, 37, 1, 1, 4, 0, 0, 0),
(21, 9, 12, 16, 1, 1, 3, 0, 0, 0),
(22, 10, 8, 16, 1, 8, 10, 2, 2, 3),
(23, 6, 11, 16, 1, 11, 12, 3, 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `days`
--

CREATE TABLE `days` (
  `day_id` int(11) NOT NULL,
  `days` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `days`
--

INSERT INTO `days` (`day_id`, `days`) VALUES
(1, 'Monday'),
(2, 'Tuesday'),
(3, 'Wednesday'),
(4, 'Thursday'),
(5, 'Friday'),
(6, 'Saturday'),
(7, 'Sunday');

-- --------------------------------------------------------

--
-- Table structure for table `enrolled_student`
--

CREATE TABLE `enrolled_student` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `sr_code` varchar(30) NOT NULL,
  `section_id` int(11) NOT NULL,
  `subject_status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrolled_student`
--

INSERT INTO `enrolled_student` (`id`, `subject_id`, `sr_code`, `section_id`, `subject_status`) VALUES
(15, 1, '19-61072', 16, 1),
(16, 1, '21-60268', 16, 0),
(17, 9, '21-60268', 16, 0),
(18, 10, '21-60268', 16, 0),
(19, 9, '19-61072', 16, 0);

-- --------------------------------------------------------

--
-- Table structure for table `evaluation`
--

CREATE TABLE `evaluation` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `sr_code` varchar(20) NOT NULL,
  `faculty_id` varchar(30) NOT NULL,
  `rate` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluation`
--

INSERT INTO `evaluation` (`id`, `question_id`, `sr_code`, `faculty_id`, `rate`) VALUES
(49, 1, '19-61072', '3', 5),
(50, 2, '19-61072', '3', 4),
(51, 3, '19-61072', '3', 5),
(52, 4, '19-61072', '3', 4),
(53, 5, '19-61072', '3', 5),
(54, 6, '19-61072', '3', 4),
(55, 7, '19-61072', '3', 5),
(56, 8, '19-61072', '3', 4),
(57, 9, '19-61072', '3', 5),
(58, 10, '19-61072', '3', 4),
(59, 11, '19-61072', '3', 5),
(60, 12, '19-61072', '3', 4),
(61, 13, '19-61072', '3', 5),
(62, 14, '19-61072', '3', 4),
(63, 15, '19-61072', '3', 5),
(64, 16, '19-61072', '3', 4),
(65, 17, '19-61072', '3', 5),
(66, 18, '19-61072', '3', 4),
(67, 19, '19-61072', '3', 5),
(68, 20, '19-61072', '3', 4),
(69, 21, '19-61072', '3', 5),
(70, 22, '19-61072', '3', 4),
(71, 23, '19-61072', '3', 5),
(72, 24, '19-61072', '3', 4);

-- --------------------------------------------------------

--
-- Table structure for table `facultylogin`
--

CREATE TABLE `facultylogin` (
  `id` int(11) NOT NULL,
  `facultyId` varchar(20) NOT NULL,
  `password` varchar(50) NOT NULL,
  `level` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `instructor`
--

CREATE TABLE `instructor` (
  `faculty_id` int(11) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `firstname` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `instructor`
--

INSERT INTO `instructor` (`faculty_id`, `lastname`, `firstname`) VALUES
(1, 'Garcia', 'Shiela Marie'),
(2, 'Manzanal', 'Johnrey'),
(3, 'Guillo', 'Joseph Rizalde'),
(4, 'Garcia', 'Donna'),
(5, 'Bucad', 'Eddie Jr.'),
(6, 'De Castro', 'Erwin'),
(7, 'Rosal', 'Miguel Edward'),
(8, 'Abejuela', 'Mary Jean'),
(9, 'Babol', 'Melvin'),
(10, 'Canoy', 'Menard'),
(11, 'Calzo', 'Cruzette'),
(12, 'Eusebio', 'Nino'),
(13, 'Biscocho', 'Val Juniel');

-- --------------------------------------------------------

--
-- Table structure for table `question`
--

CREATE TABLE `question` (
  `q_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `q_group` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `question`
--

INSERT INTO `question` (`q_id`, `description`, `q_group`) VALUES
(1, 'Demonstrates a strong knowledge of the\nsubject and answers questions accurately\nand confidently.', 1),
(2, 'Stays updated with the latest\r\ndevelopments and trends in the subject\r\nmatter.', 1),
(3, 'Explains topics clearly and makes them\r\neasy to understand, so I can follow the\r\nlessons without confusion.', 1),
(4, 'Provides relevant examples that help me\r\ngrasp difficult concepts more easily.', 1),
(5, 'Connects what we are learning to real-life\r\nsituations, showing how it applies outside\r\nof class.', 1),
(6, 'Adapts their teaching methods to different\r\nlearning styles, helping me understand the\r\nmaterial better', 1),
(7, 'Starts and ends classes on time, respecting\r\nmy schedule and keeping things\r\norganized.', 2),
(8, 'Keeps the class focused on the topic and\r\nminimizes distractions.', 2),
(9, 'Handles any disruptions in the classroom\r\nquickly and effectively, maintaining a positive learning environment.', 2),
(10, 'Makes classroom environment positive\r\nand encouraging, making it easier for me\r\nto participate and learn.', 2),
(11, 'Manages classroom time well, balancing\r\ndifferent activities and covering all\r\nnecessary topics.', 2),
(12, 'Encourages me to participate in class\r\ndiscussions and activities.', 3),
(13, 'Learning activities are enjoyable and help\r\nkeep me interested in the subject matter.', 3),
(14, 'Activities used in class help me\r\nunderstand and remember the material\r\nbetter.', 3),
(15, 'The instructor shows genuine concern for\r\nmy progress and provides support to help\r\nme succeed.', 3),
(16, 'The instructor motivates me to do my best\r\nthrough encouragement and positive\r\nreinforcement.', 3),
(17, 'Clearly explains what is expected in the\r\ncourse, including goals and grading\r\ncriteria.', 4),
(18, 'Answers my questions promptly and\r\nprovides clear explanations.', 4),
(19, 'Lessons are explained in a straightforward\r\nway that is easy for me to understand.', 4),
(20, 'There is effective communication\r\nbetween the instructor and me, allowing\r\nfor open discussion and feedback.', 4),
(21, 'Feedback on my assignments and exams\r\nis given in a helpful manner, guiding me\r\non how to improve.', 4),
(22, 'The instructor handles their emotions\r\neffectively, creating a calm and stable\r\nclassroom environment.', 5),
(23, 'The instructor is approachable and\r\nrespectful, making me feel valued and\r\nheard.', 5),
(24, 'The classroom atmosphere is warm and\r\nsupportive, contributing to a positive\r\nlearning experience.', 5);

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `room_id` int(11) NOT NULL,
  `room` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `section`
--

CREATE TABLE `section` (
  `id` int(11) NOT NULL,
  `section` varchar(30) NOT NULL,
  `year_id` int(11) NOT NULL,
  `sem_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `section`
--

INSERT INTO `section` (`id`, `section`, `year_id`, `sem_id`) VALUES
(1, 'IT-1101', 1, 1),
(2, 'IT-1102', 1, 1),
(3, 'IT-1103', 1, 1),
(4, 'IT-1201', 1, 2),
(5, 'IT-1202', 1, 2),
(6, 'IT-1203', 1, 2),
(7, 'IT-2101', 2, 1),
(8, 'IT-2102', 2, 1),
(9, 'IT-2103', 2, 1),
(10, 'IT-2201', 2, 2),
(11, 'IT-2202', 2, 2),
(12, 'IT-2203', 2, 2),
(13, 'ITSM-3201', 3, 2),
(14, 'ITBA-3201', 3, 2),
(15, 'ITSM-4101', 4, 1),
(16, 'ITBA-4101', 4, 1),
(17, 'IT-1104', 1, 1),
(18, 'IT-1105', 1, 1),
(19, 'IT-1106', 1, 1),
(20, 'IT-1204', 1, 2),
(21, 'IT-1205', 1, 2),
(22, 'IT-1206', 1, 2),
(23, 'IT-2104', 2, 1),
(24, 'IT-2204', 2, 2),
(25, 'ITNT-3101', 3, 1),
(26, 'ITNT-3201', 3, 2),
(27, 'ITSM-3101', 3, 1),
(28, 'ITSM-3102', 3, 1),
(29, 'ITSM-3202', 3, 2),
(30, 'ITBA-3101', 3, 1),
(31, 'ITBA-3102', 3, 1),
(32, 'ITBA-3103', 3, 1),
(33, 'ITBA-3202', 3, 2),
(34, 'ITBA-3203', 3, 2),
(35, 'ITNT-4101', 4, 1),
(36, 'ITSM-4102', 4, 1),
(37, 'ITBA-4102', 4, 1),
(38, 'ITBA-4103', 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `semester`
--

CREATE TABLE `semester` (
  `sem_id` int(11) NOT NULL,
  `semester` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `semester`
--

INSERT INTO `semester` (`sem_id`, `semester`) VALUES
(1, 'FIRST'),
(2, 'SECOND');

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `id` int(11) NOT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`id`, `status`) VALUES
(1, 'Regular'),
(2, 'Irregular');

-- --------------------------------------------------------

--
-- Table structure for table `studentlogin`
--

CREATE TABLE `studentlogin` (
  `id` int(11) NOT NULL,
  `srcode` varchar(20) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `studentlogin`
--

INSERT INTO `studentlogin` (`id`, `srcode`, `password`) VALUES
(1, '21-69247', 'CORO'),
(2, '21-67450', 'REYES'),
(3, '21-63034', 'ESTILLER'),
(4, '21-01915', 'LICMO'),
(5, '21-65231', 'TOMBOCCON'),
(6, '21-60268', 'LOPEZ'),
(7, '21-67790', 'VILLAPANDO'),
(8, '19-61072', 'LOPEZ');

-- --------------------------------------------------------

--
-- Table structure for table `student_basic_info`
--

CREATE TABLE `student_basic_info` (
  `id` int(11) NOT NULL,
  `sr_code` varchar(20) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `middlename` varchar(50) NOT NULL,
  `birthday` varchar(50) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `address` varchar(200) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_basic_info`
--

INSERT INTO `student_basic_info` (`id`, `sr_code`, `lastname`, `firstname`, `middlename`, `birthday`, `gender`, `address`, `email`, `contact`) VALUES
(1, '19-61072', 'Lopez', 'John Kenneth', 'Moncayo', '10-20-2000', 'Male', 'Brgy. Mabini Tanauan City, Batangas', 'johnkenneth.lopez@g.batstate-u.edu.ph', '09771183520'),
(2, '21-60268', 'Lopez', 'Alyza Nicole', 'Moncayo', '07-10-2003', 'Female', 'Brgy. Mabini Tanauan City, Batangas', '21-60268@g.batstate-u.eddu.ph', '09771183520');

-- --------------------------------------------------------

--
-- Table structure for table `student_status`
--

CREATE TABLE `student_status` (
  `id` int(11) NOT NULL,
  `sr_code` varchar(20) NOT NULL,
  `year_level` varchar(20) NOT NULL,
  `status_id` int(11) NOT NULL,
  `section` varchar(20) NOT NULL,
  `course` varchar(100) NOT NULL,
  `sem_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_status`
--

INSERT INTO `student_status` (`id`, `sr_code`, `year_level`, `status_id`, `section`, `course`, `sem_id`) VALUES
(1, '21-60268', '4', 1, 'ITSM-4101', 'Bachelor of Science in Information Technology', 1),
(2, '19-61072', '4', 2, 'ITSM-4103', 'Bachelor of Science in Information Technology', 1);

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE `subject` (
  `subject_id` int(11) NOT NULL,
  `subject` varchar(75) NOT NULL,
  `unit` int(10) NOT NULL,
  `year` varchar(10) NOT NULL,
  `semester` varchar(10) NOT NULL,
  `subject_code` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subject`
--

INSERT INTO `subject` (`subject_id`, `subject`, `unit`, `year`, `semester`, `subject_code`) VALUES
(1, 'Capstone Project 2', 3, '4', '1', 'IT 411'),
(2, 'Capstone Project 1', 3, '3', '2', 'IT 324'),
(4, 'Introduction to Computing', 3, '1', '1', 'IT 111'),
(6, 'Social Issues and Professional Practice', 3, '4', '1', 'CS 423'),
(7, 'Principles of System Thinking', 3, '4', '1', 'SMT 405'),
(8, 'Advanced Information Assurance and Security', 3, '4', '1', 'IT 413'),
(9, 'Platform Technologies', 3, '4', '1', 'IT 412'),
(10, 'Technopreneurship', 3, '4', '1', 'ENGG 405'),
(11, 'IT Internship Training', 6, '4', '2', 'IT 421'),
(12, 'Systems Quality Assurance', 3, '4', '1', 'IT 414'),
(13, 'Advanced Systems Integration and Architecture', 3, '3', '2', 'IT 322'),
(14, 'Fundamentals of Business Process Outsourcing 2', 3, '3', '2', 'SMT 403'),
(15, 'Human-Computer Interaction', 3, '3', '2', 'IT 321'),
(16, 'Information Assurance and Security', 3, '3', '2', 'IT 323'),
(17, 'IT Project Management', 3, '3', '2', 'IT 325'),
(18, 'Service Culture', 3, '3', '2', 'SMT 404'),
(19, 'Business Communication', 3, '3', '1', 'SMT 402'),
(20, 'Ethics', 3, '3', '1', 'GEd 107'),
(21, 'System Analysis and Design', 3, '3', '1', 'IT 313'),
(22, 'Systems Administration and Maintenance', 3, '3', '1', 'IT 311'),
(23, 'Systems Integration and Architecture', 3, '3', '1', 'IT 312'),
(24, 'Web Systems and Technologies', 3, '3', '1', 'IT 314'),
(25, 'Advanced Database Management System', 3, '2', '2', 'IT 222'),
(26, 'Computer Networking 2', 3, '2', '2', 'IT 223'),
(27, 'Data Analysis', 3, '2', '2', 'MATH 408'),
(28, 'Environmental Sciences', 3, '2', '2', 'ES 101'),
(29, 'Information Management', 3, '2', '2', 'IT 221'),
(30, 'Purposive Communication', 3, '2', '2', 'GEd 106'),
(31, 'Team Sports', 2, '2', '2', 'PE 104'),
(32, 'Understanding the Self', 3, '2', '2', 'GEd 101'),
(33, 'Advanced Computer Programming', 3, '2', '1', 'CS 121'),
(34, 'ASEAN Literature', 3, '2', '1', 'Litr 102'),
(35, 'Calculus-Based Physics', 3, '2', '1', 'Phy 101'),
(36, 'Computer Networking 1', 3, '2', '1', 'IT 212'),
(37, 'Database Management System', 3, '2', '1', 'IT 211'),
(38, 'Discrete Mathematics', 3, '2', '1', 'CpE 405'),
(39, 'Individual and Dual Sports', 2, '2', '1', 'PE 103'),
(40, 'Object-Oriented Programming', 3, '2', '1', 'CS 211'),
(41, 'Computer Programming', 3, '1', '2', 'CS 111'),
(42, 'Data Structures and Algorithms', 3, '1', '2', 'CS 131'),
(43, 'Filipino sa Ibat-ibang Disiplina', 3, '1', '2', 'Fili 102'),
(44, 'Linear Algebra', 3, '1', '2', 'MATH 111'),
(45, 'NSTP - Civic Welfare Training Service 2', 3, '1', '2', 'NSTP 121CW'),
(46, 'Readings in Philippine History', 3, '1', '2', 'GEd 105'),
(47, 'Rhythmic Activities', 2, '1', '2', 'PE 102'),
(48, 'Science, Technology and Society', 3, '1', '2', 'GEd 109'),
(49, 'Art Appreciation', 3, '1', '1', 'GEd 108'),
(50, 'Introduction to Computing', 3, '1', '1', 'IT 111'),
(51, 'Kontekstwalisadong Komunikasyon sa Filipino', 3, '1', '1', 'Fili 101'),
(52, 'Life and Works of Rizal', 3, '1', '1', 'GEd 103'),
(53, 'Mathematics in the Modern World', 3, '1', '1', 'GEd 102'),
(54, 'NSTP - Civic Welfare Training Service 1', 3, '1', '1', 'NSTP 111CW'),
(55, 'Physical Fitness, Gymnastics and Aerobics', 2, '1', '1', 'PE 101'),
(56, 'The Contemporary World', 3, '1', '1', 'GEd 104');

-- --------------------------------------------------------

--
-- Table structure for table `subject_status`
--

CREATE TABLE `subject_status` (
  `sub_stat_ID` int(11) NOT NULL,
  `status` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subject_status`
--

INSERT INTO `subject_status` (`sub_stat_ID`, `status`) VALUES
(1, 'ongoing'),
(2, 'completed'),
(3, 'Incomplete');

-- --------------------------------------------------------

--
-- Table structure for table `time`
--

CREATE TABLE `time` (
  `time_id` int(11) NOT NULL,
  `time` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `time`
--

INSERT INTO `time` (`time_id`, `time`) VALUES
(1, '7:00 AM'),
(2, '8:00 AM'),
(3, '9:00 AM'),
(4, '10:00 AM'),
(5, '11:00 AM'),
(6, '12:00 PM'),
(7, '1:00 PM'),
(8, '2:00 PM'),
(9, '3:00 PM'),
(10, '4:00 PM'),
(11, '5:00 PM'),
(12, '6:00 PM'),
(13, '7:00 PM');

-- --------------------------------------------------------

--
-- Table structure for table `year_level`
--

CREATE TABLE `year_level` (
  `year_id` int(11) NOT NULL,
  `year_level` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `year_level`
--

INSERT INTO `year_level` (`year_id`, `year_level`) VALUES
(1, 'FIRST'),
(2, 'SECOND'),
(3, 'THIRD'),
(4, 'FOURTH');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assigned_subject`
--
ALTER TABLE `assigned_subject`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `days`
--
ALTER TABLE `days`
  ADD PRIMARY KEY (`day_id`);

--
-- Indexes for table `enrolled_student`
--
ALTER TABLE `enrolled_student`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `evaluation`
--
ALTER TABLE `evaluation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `facultylogin`
--
ALTER TABLE `facultylogin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `instructor`
--
ALTER TABLE `instructor`
  ADD PRIMARY KEY (`faculty_id`);

--
-- Indexes for table `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`q_id`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`room_id`);

--
-- Indexes for table `section`
--
ALTER TABLE `section`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `semester`
--
ALTER TABLE `semester`
  ADD PRIMARY KEY (`sem_id`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `studentlogin`
--
ALTER TABLE `studentlogin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_basic_info`
--
ALTER TABLE `student_basic_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_status`
--
ALTER TABLE `student_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subject`
--
ALTER TABLE `subject`
  ADD PRIMARY KEY (`subject_id`);

--
-- Indexes for table `subject_status`
--
ALTER TABLE `subject_status`
  ADD PRIMARY KEY (`sub_stat_ID`);

--
-- Indexes for table `time`
--
ALTER TABLE `time`
  ADD PRIMARY KEY (`time_id`);

--
-- Indexes for table `year_level`
--
ALTER TABLE `year_level`
  ADD PRIMARY KEY (`year_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assigned_subject`
--
ALTER TABLE `assigned_subject`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `days`
--
ALTER TABLE `days`
  MODIFY `day_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `enrolled_student`
--
ALTER TABLE `enrolled_student`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `evaluation`
--
ALTER TABLE `evaluation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `facultylogin`
--
ALTER TABLE `facultylogin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `instructor`
--
ALTER TABLE `instructor`
  MODIFY `faculty_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `question`
--
ALTER TABLE `question`
  MODIFY `q_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=183;

--
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `section`
--
ALTER TABLE `section`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `semester`
--
ALTER TABLE `semester`
  MODIFY `sem_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `status`
--
ALTER TABLE `status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `studentlogin`
--
ALTER TABLE `studentlogin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `student_basic_info`
--
ALTER TABLE `student_basic_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `student_status`
--
ALTER TABLE `student_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `subject_status`
--
ALTER TABLE `subject_status`
  MODIFY `sub_stat_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `year_level`
--
ALTER TABLE `year_level`
  MODIFY `year_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
