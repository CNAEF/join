-- phpMyAdmin SQL Dump
-- version 4.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2015-09-19 00:20:05
-- 服务器版本： 10.0.16-MariaDB
-- PHP Version: 5.6.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `join`
--

-- --------------------------------------------------------

--
-- 表的结构 `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) CHARACTER SET utf8 NOT NULL,
  `password` varchar(50) CHARACTER SET utf8 NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COMMENT='管理员';

-- --------------------------------------------------------

--
-- 表的结构 `error`
--

CREATE TABLE IF NOT EXISTS `error` (
  `id` int(10) NOT NULL,
  `username` varchar(50) NOT NULL,
  `qq` varchar(14) NOT NULL,
  `phone` varchar(14) NOT NULL,
  `ip` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
  `id` int(11) NOT NULL,
  `content` text CHARACTER SET utf8 NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM AUTO_INCREMENT=5540 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 表的结构 `user_info`
--

CREATE TABLE IF NOT EXISTS `user_info` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `sex` tinyint(3) NOT NULL,
  `birthday` int(11) NOT NULL,
  `_age` tinyint(3) NOT NULL,
  `married` tinyint(3) NOT NULL,
  `hometown_province` varchar(40) NOT NULL,
  `hometown_city` varchar(40) NOT NULL,
  `id_num` varchar(20) NOT NULL,
  `id_photo` varchar(100) NOT NULL,
  `user_photo` varchar(100) NOT NULL,
  `edu_level` tinyint(3) NOT NULL,
  `edu_photo` varchar(100) NOT NULL,
  `_edu_high_level` varchar(100) NOT NULL,
  `edu_university` varchar(100) NOT NULL,
  `profession` varchar(100) NOT NULL,
  `special` text NOT NULL,
  `work` varchar(100) NOT NULL,
  `work_experience` text NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `qq` varchar(100) NOT NULL,
  `cur_province` varchar(100) NOT NULL,
  `cur_city` varchar(100) NOT NULL,
  `cur_addr` varchar(100) NOT NULL,
  `_user_post_addr` varchar(200) NOT NULL,
  `post_code` int(6) NOT NULL,
  `family_title` varchar(20) NOT NULL,
  `family_name` varchar(20) NOT NULL,
  `family_contact` varchar(100) NOT NULL,
  `family_workplace` varchar(100) NOT NULL,
  `family_addr` varchar(200) NOT NULL,
  `urgent_title` varchar(20) NOT NULL,
  `urgent_name` varchar(20) NOT NULL,
  `urgent_contact` varchar(100) NOT NULL,
  `urgent_workplace` varchar(200) NOT NULL,
  `is_disability` text NOT NULL,
  `is_experience` text NOT NULL,
  `predict_deadline` varchar(50) NOT NULL,
  `begin_date` tinyint(4) NOT NULL,
  `cur_status` tinyint(3) NOT NULL,
  `cur_income` text NOT NULL,
  `info_from` varchar(100) NOT NULL,
  `Q1` text NOT NULL,
  `Q2` text NOT NULL,
  `Q3` text NOT NULL,
  `Q4` text NOT NULL,
  `_Q1` text NOT NULL,
  `_Q2` text NOT NULL,
  `_Q3` text NOT NULL,
  `_Q4` text NOT NULL,
  `_Q5` text NOT NULL,
  `_Q6` text NOT NULL,
  `_Q7` text NOT NULL,
  `_Q8` text NOT NULL,
  `_Q9` text NOT NULL,
  `_Q10` text NOT NULL,
  `_Q11` text NOT NULL,
  `user_status` tinyint(3) NOT NULL,
  `verify_admin_id` int(11) NOT NULL,
  `verify_time` datetime NOT NULL,
  `verify_status` tinyint(3) NOT NULL,
  `time` datetime NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `error`
--
ALTER TABLE `error`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_info`
--
ALTER TABLE `user_info`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `error`
--
ALTER TABLE `error`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5540;
--
-- AUTO_INCREMENT for table `user_info`
--
ALTER TABLE `user_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=16;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
