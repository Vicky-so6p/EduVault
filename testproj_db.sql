-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
-- Host: 127.0.0.1
-- Generation Time: Sep 30, 2022 at 05:35 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Database: course_db
-- --------------------------------------------------------

-- Table structure for table bookmark
CREATE TABLE bookmark (
  user_id VARCHAR(20) NOT NULL,
  playlist_id VARCHAR(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table comments
CREATE TABLE comments (
  id VARCHAR(20) NOT NULL,
  content_id VARCHAR(20) NOT NULL,
  user_id VARCHAR(20) NOT NULL,
  tutor_id VARCHAR(20) NOT NULL,
  comment VARCHAR(1000) NOT NULL,
  date DATE NOT NULL DEFAULT CURRENT_TIMESTAMP()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table contact
CREATE TABLE contact (
  name VARCHAR(50) NOT NULL,
  email VARCHAR(50) NOT NULL,
  number INT(10) NOT NULL,
  message VARCHAR(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table content
CREATE TABLE content (
  id VARCHAR(20) NOT NULL,
  tutor_id VARCHAR(20) NOT NULL,
  playlist_id VARCHAR(20) NOT NULL,
  title VARCHAR(100) NOT NULL,
  description VARCHAR(1000) NOT NULL,
  video VARCHAR(100) NOT NULL,
  thumb VARCHAR(100) NOT NULL,
  date DATE NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  status VARCHAR(20) NOT NULL DEFAULT 'deactive',
  is_free TINYINT(1) NOT NULL DEFAULT 0  -- Add this column for free/paid content
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table likes
CREATE TABLE likes (
  user_id VARCHAR(20) NOT NULL,
  tutor_id VARCHAR(20) NOT NULL,
  content_id VARCHAR(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table playlist
CREATE TABLE playlist (
  id VARCHAR(20) NOT NULL,
  tutor_id VARCHAR(20) NOT NULL,
  title VARCHAR(100) NOT NULL,
  description VARCHAR(1000) NOT NULL,
  thumb VARCHAR(100) NOT NULL,
  date DATE NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  status VARCHAR(20) NOT NULL DEFAULT 'deactive',
  price DECIMAL(10, 2) DEFAULT 0.00,  -- Price for the playlist
  PRIMARY KEY (id)  -- Add primary key for playlist
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table tutors
CREATE TABLE tutors (
  id VARCHAR(20) NOT NULL,
  name VARCHAR(50) NOT NULL,
  profession VARCHAR(50) NOT NULL,
  email VARCHAR(50) NOT NULL,
  password VARCHAR(50) NOT NULL,
  image VARCHAR(100) NOT NULL,
  PRIMARY KEY (id)  -- Add primary key for tutors
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table users
CREATE TABLE users (
  id VARCHAR(20) NOT NULL,
  name VARCHAR(50) NOT NULL,
  email VARCHAR(50) NOT NULL,
  password VARCHAR(50) NOT NULL,
  image VARCHAR(100) NOT NULL,
  PRIMARY KEY (id)  -- Add primary key for users
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table purchases
CREATE TABLE purchases (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id VARCHAR(20) NOT NULL,
  playlist_id VARCHAR(20) NOT NULL,
  purchase_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (playlist_id) REFERENCES playlist(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;