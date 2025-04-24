-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 19, 2025 at 03:25 AM
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
-- Database: `crtvotingsystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(6, 'ad', 'ad'),
(7, 'ads', 'ads'),
(8, 'sa', 'sa');

-- --------------------------------------------------------

--
-- Table structure for table `admin_sessions`
--

CREATE TABLE `admin_sessions` (
  `session_id` varchar(255) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_sessions`
--

INSERT INTO `admin_sessions` (`session_id`, `admin_id`, `login_time`) VALUES
('04pj6fu0c62f36vjg072d3v62s', 2, '2025-04-19 01:19:36'),
('0pkke2174m5fq8jjgsk1coiot3', 5, '2025-04-19 00:41:55'),
('3g2kupv28l1351scp82ii4n3k8', 8, '2025-04-19 01:22:13'),
('os4ckl5i25h8c8smh286ugpkm4', 6, '2025-04-19 01:23:56');

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

CREATE TABLE `candidates` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `position` varchar(50) NOT NULL,
  `course` enum('ACT','FSM','HRS') NOT NULL,
  `party` varchar(100) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`id`, `name`, `position`, `course`, `party`, `photo`) VALUES
(59, 'Bombardino Crocodilo', 'Representative (ACT)', 'ACT', 'party uno', 'candidate_photos/68019ea8b8592_download.jfif'),
(60, 'Tung tung tung tung sahur', 'Representative (ACT)', 'ACT', 'party dos', 'candidate_photos/68019ecfad944_download (1).jfif'),
(63, 'Tralalero tralala', 'Representative (HRS)', 'HRS', 'party uno', 'candidate_photos/6802dea925cc9_download (2).jfif'),
(64, 'Pakrahmatmamat', 'Representative (HRS)', 'HRS', 'party dos', 'candidate_photos/6802e074527d7_images.jfif'),
(65, 'Lirili larila', 'Representative (FSM)', 'FSM', 'party uno', 'candidate_photos/6802e11e10ef1_download (3).jfif'),
(66, 'Sombraruote frratatà', 'Representative (FSM)', 'FSM', 'party dos', 'candidate_photos/6802e1ac972b0_Sombraruote_.webp'),
(67, 'trulimero trulichina', 'Governor', '', 'party uno', 'candidate_photos/6802e20f583b1_download (4).jfif'),
(68, 'Tripi Tropi', 'Governor', '', 'party dos', 'candidate_photos/6802e27912c33_640.webp'),
(69, 'Brr brr Patapim', 'Vice Governor', '', 'party uno', 'candidate_photos/6802e2aac63ec_Brr_Brr_Patapim.webp'),
(70, 'trikitrakatelas', 'Vice Governor', '', 'party dos', 'candidate_photos/6802e42c5589e_images (1).jfif'),
(71, 'friggocamelo', 'Secretary', '', 'party uno', 'candidate_photos/6802e4a742743_download (6).jfif'),
(72, 'cocofanto elephanto', 'Secretary', '', 'party dos', 'candidate_photos/6802e4c8956d5_download (7).jfif'),
(73, 'Chimpanzini Bananini', 'Treasurer', '', 'party uno', 'candidate_photos/6802e53d434f9_ChimpanziniBananini.webp'),
(74, 'Capuchino Assassino', 'Treasurer', '', 'party dos', 'candidate_photos/6802e5801c28d_Hq720.webp'),
(75, 'Ballerina Cappuccina', 'Auditor', '', 'party uno', 'candidate_photos/6802e5ad7e1b0_Ballerina_Cappucina.webp'),
(76, 'Piccione Macchina', 'Auditor', '', 'party dos', 'candidate_photos/6802e5de15b41_Piccione_Macchina.webp'),
(77, 'Rugginato LupoGT (Il Cannone Stradale)', 'PIO', '', 'party uno', 'candidate_photos/6802e65d1b07a_Rugginato_LupoGT_F.webp'),
(78, 'Burbaloni Luliloli', 'PIO', '', 'party dos', 'candidate_photos/6802e69edc4a7_Burbaloni.webp'),
(79, 'po1', 'PO', '', 'party uno', ''),
(80, 'po2', 'PO', '', 'party dos', ''),
(81, 'sgt1-1', 'Sergeant at Arms (1)', '', 'party uno', ''),
(82, 'sgt1-2', 'Sergeant at Arms (1)', '', 'party dos', ''),
(83, 'sgt2-1', 'Sergeant at Arms (2)', '', 'party uno', ''),
(84, 'sgt2-2', 'Sergeant at Arms (2)', '', 'party dos', ''),
(85, 'Maam Joyce', 'Muse', '', 'party uno', 'candidate_photos/6802e74b99096_484952893_952622820358401_6391655901331046948_n.jpg'),
(86, 'maam ahmie', 'Muse', '', 'party dos', 'candidate_photos/6802e7678b982_463295469_3792614531055808_6656980158301671263_n.jpg'),
(87, 'Michael Deguzman', 'Escort', '', 'party uno', 'candidate_photos/6802e78c5571b_480496313_1680119459376960_9150895144265000036_n.jpg'),
(88, 'John Denver Lagmay', 'Escort', '', 'party dos', 'candidate_photos/6802e7b1c2dbd_394279776_340526598527751_5724208269171627587_n.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE `schedule` (
  `id` int(11) NOT NULL,
  `open_time` datetime NOT NULL,
  `close_time` datetime NOT NULL,
  `status` enum('open','closed') NOT NULL DEFAULT 'closed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`id`, `open_time`, `close_time`, `status`) VALUES
(56, '2025-04-19 07:03:00', '2025-04-26 07:03:00', 'open');

-- --------------------------------------------------------

--
-- Table structure for table `voters`
--

CREATE TABLE `voters` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `course` enum('ACT','FSM','HRS') NOT NULL,
  `has_voted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `voters`
--

INSERT INTO `voters` (`id`, `username`, `password`, `course`, `has_voted`) VALUES
(20, 'act', 'act', 'ACT', 0),
(21, 'ada', 'ada', 'ACT', 0),
(22, 'hrs', 'hrs', 'HRS', 0);

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL,
  `voter_id` int(11) NOT NULL,
  `candidate_id` int(11) DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `admin_sessions`
--
ALTER TABLE `admin_sessions`
  ADD PRIMARY KEY (`session_id`);

--
-- Indexes for table `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `voters`
--
ALTER TABLE `voters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `voter_id` (`voter_id`,`position`),
  ADD KEY `candidate_id` (`candidate_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `candidates`
--
ALTER TABLE `candidates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `schedule`
--
ALTER TABLE `schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `voters`
--
ALTER TABLE `voters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`voter_id`) REFERENCES `voters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_ibfk_2` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
