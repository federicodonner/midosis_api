-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Feb 11, 2020 at 03:45 AM
-- Server version: 5.7.23
-- PHP Version: 7.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `medicina`
--
CREATE DATABASE IF NOT EXISTS `medicina` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `medicina`;

-- --------------------------------------------------------

--
-- Table structure for table `dosis`
--

DROP TABLE IF EXISTS `dosis`;
CREATE TABLE `dosis` (
  `id` int(11) NOT NULL,
  `horario` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `pastillero_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dosis`
--

INSERT INTO `dosis` (`id`, `horario`, `pastillero_id`) VALUES
(1, '9 AM', 1),
(2, 'Mediodia', 1),
(3, '4 PM', 1),
(4, 'Cena', 1),
(5, 'Noche', 1);

-- --------------------------------------------------------

--
-- Table structure for table `droga`
--

DROP TABLE IF EXISTS `droga`;
CREATE TABLE `droga` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `droga`
--

INSERT INTO `droga` (`id`, `nombre`) VALUES
(1, 'Droga 1'),
(2, 'Droga 2'),
(3, 'Prueba esde postman'),
(4, 'Prueba esde postman');

-- --------------------------------------------------------

--
-- Table structure for table `droga_x_dosis`
--

DROP TABLE IF EXISTS `droga_x_dosis`;
CREATE TABLE `droga_x_dosis` (
  `id` int(11) NOT NULL,
  `droga_id` int(11) NOT NULL,
  `dosis_id` int(11) NOT NULL,
  `cantidad_mg` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `medicina`
--

DROP TABLE IF EXISTS `medicina`;
CREATE TABLE `medicina` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `droga_id` int(11) NOT NULL,
  `comprimidos_por_caja` int(11) NOT NULL,
  `concentracion_mg` int(11) NOT NULL,
  `imagen` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `medicina`
--

INSERT INTO `medicina` (`id`, `nombre`, `droga_id`, `comprimidos_por_caja`, `concentracion_mg`, `imagen`) VALUES
(1, 'Medicina 1', 1, 10, 100, ''),
(2, 'Medicina 2', 1, 20, 100, ''),
(3, 'Medicina 3', 2, 300, 10, ''),
(4, 'Medicina desde postman', 2, 3, 4, 'Imagen'),
(6, 'Medicina desde postman', 2, 3, 4, 'Imagen'),
(7, 'Medicina desde postman', 3, 3, 4, 'Imagen');

-- --------------------------------------------------------

--
-- Table structure for table `pastillero`
--

DROP TABLE IF EXISTS `pastillero`;
CREATE TABLE `pastillero` (
  `id` int(11) NOT NULL,
  `dueno` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pastillero`
--

INSERT INTO `pastillero` (`id`, `dueno`) VALUES
(1, 'Julio');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dosis`
--
ALTER TABLE `dosis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `droga`
--
ALTER TABLE `droga`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `droga_x_dosis`
--
ALTER TABLE `droga_x_dosis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `medicina`
--
ALTER TABLE `medicina`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pastillero`
--
ALTER TABLE `pastillero`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dosis`
--
ALTER TABLE `dosis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `droga`
--
ALTER TABLE `droga`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `droga_x_dosis`
--
ALTER TABLE `droga_x_dosis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medicina`
--
ALTER TABLE `medicina`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pastillero`
--
ALTER TABLE `pastillero`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
