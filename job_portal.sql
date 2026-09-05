-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 05, 2026 at 10:01 AM
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
-- Database: `job_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(30) DEFAULT NULL,
  `education` varchar(255) DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `experience` varchar(100) DEFAULT NULL,
  `expected_salary` varchar(100) DEFAULT NULL,
  `resume` varchar(255) DEFAULT NULL,
  `cover_letter` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `job_id`, `user_id`, `status`, `applied_at`, `phone`, `education`, `skills`, `experience`, `expected_salary`, `resume`, `cover_letter`) VALUES
(1, 1, 1, 'Selected', '2026-09-05 06:05:11', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 10, 1, 'Shortlisted', '2026-09-05 06:24:22', '858858885858', 'bsc', 'php developer', 'Fresher', '400000', 'uploads/resumes/resume_1_1788589462.pdf', 'dd;ele[oeoiokoeo'),
(3, 2, 1, 'Rejected', '2026-09-05 06:34:14', '858858885858', 'bsc', 'php developer', 'Fresher', '400000', 'uploads/resumes/resume_1_1788590054_6a9bb7e6da530.pdf', 'smsmsmmmsms');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `employer_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `company` varchar(150) NOT NULL,
  `location` varchar(150) NOT NULL,
  `salary` varchar(100) DEFAULT NULL,
  `description` text NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `employer_id`, `title`, `company`, `location`, `salary`, `description`, `category`, `created_at`) VALUES
(1, 1, 'PHP Developer', 'Tech Solutions', 'Kochi', '₹25,000 - ₹40,000', 'We are looking for a PHP developer to join our development team.', 'IT', '2026-09-05 05:48:46'),
(2, 1, 'Frontend Developer', 'WebWorks', 'Bangalore', '₹30,000 - ₹45,000', 'Build modern and responsive websites using HTML, CSS and JavaScript.', 'Web Development', '2026-09-05 06:00:47'),
(3, 1, 'Backend Developer', 'CodeTech', 'Hyderabad', '₹35,000 - ₹50,000', 'Develop and maintain backend applications and APIs.', 'Software Development', '2026-09-05 06:00:47'),
(4, 1, 'Full Stack Developer', 'Digital Labs', 'Kochi', '₹40,000 - ₹60,000', 'Work on both frontend and backend development of web applications.', 'IT', '2026-09-05 06:00:47'),
(5, 1, 'UI/UX Designer', 'Creative Studio', 'Chennai', '₹25,000 - ₹40,000', 'Design user-friendly interfaces and engaging digital experiences.', 'Design', '2026-09-05 06:00:47'),
(6, 1, 'Java Developer', 'Tech Solutions', 'Bangalore', '₹35,000 - ₹55,000', 'Develop Java applications and work with development teams.', 'Software Development', '2026-09-05 06:00:47'),
(7, 1, 'Data Analyst', 'DataWorks', 'Pune', '₹30,000 - ₹45,000', 'Analyze data and create reports to support business decisions.', 'Data', '2026-09-05 06:00:47'),
(8, 1, 'Digital Marketing Executive', 'MarketPro', 'Mumbai', '₹20,000 - ₹35,000', 'Plan and manage digital marketing campaigns and social media activities.', 'Marketing', '2026-09-05 06:00:47'),
(9, 1, 'Mobile App Developer', 'AppTech', 'Delhi', '₹35,000 - ₹55,000', 'Develop and maintain modern mobile applications.', 'Mobile Development', '2026-09-05 06:00:47'),
(10, 1, 'WordPress Developer', 'WebStudio', 'Kochi', '₹20,000 - ₹35,000', 'Create and maintain WordPress websites and custom themes.', 'Web Development', '2026-09-05 06:00:47');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('jobseeker','employer','admin') DEFAULT 'jobseeker',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `profile_picture`) VALUES
(1, 'johngy', 'johngy@gmail.com', '$2y$10$jpo72SgVxI57eMzsSktmXOPJ0ynVrPkIPXFtdns.j5hZyPUrr0L8u', 'jobseeker', '2026-09-05 05:47:03', 'profile_1_1788591191.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
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
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
