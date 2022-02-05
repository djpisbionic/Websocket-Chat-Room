-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 05, 2022 at 02:36 AM
-- Server version: 10.5.8-MariaDB-1:10.5.8+maria~bionic
-- PHP Version: 7.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `darryl_chatdemo`
--

-- --------------------------------------------------------

--
-- Table structure for table `chatroom_user`
--

CREATE TABLE `chatroom_user` (
  `id` int(11) NOT NULL,
  `chatroom` varchar(100) DEFAULT NULL,
  `user` varchar(100) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL,
  `is_banned` int(11) DEFAULT 0,
  `is_instant` int(11) DEFAULT 0,
  `is_active` int(11) DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `instant_chats`
--

CREATE TABLE `instant_chats` (
  `id` int(11) NOT NULL,
  `user_to` varchar(255) DEFAULT NULL,
  `user_from` varchar(255) DEFAULT NULL,
  `is_banned` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `instant_messages`
--

CREATE TABLE `instant_messages` (
  `id` int(11) NOT NULL,
  `text` varchar(250) DEFAULT NULL,
  `user_to` varchar(255) DEFAULT NULL,
  `user_from` varchar(255) DEFAULT NULL,
  `is_seen` int(11) DEFAULT 0,
  `chat` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chatroom_user`
--
ALTER TABLE `chatroom_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `instant_chats`
--
ALTER TABLE `instant_chats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `instant_messages`
--
ALTER TABLE `instant_messages`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chatroom_user`
--
ALTER TABLE `chatroom_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `instant_chats`
--
ALTER TABLE `instant_chats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `instant_messages`
--
ALTER TABLE `instant_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=275;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
