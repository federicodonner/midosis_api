-- phpMyAdmin SQL Dump
-- version 4.9.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: May 13, 2020 at 04:26 AM
-- Server version: 5.7.26
-- PHP Version: 7.4.2

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

-- --------------------------------------------------------

--
-- Table structure for table `droga`
--

DROP TABLE IF EXISTS `droga`;
CREATE TABLE `droga` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `pastillero` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `droga_x_dosis`
--

DROP TABLE IF EXISTS `droga_x_dosis`;
CREATE TABLE `droga_x_dosis` (
  `id` int(11) NOT NULL,
  `droga_id` int(11) NOT NULL,
  `dosis_id` int(11) NOT NULL,
  `cantidad_mg` float NOT NULL,
  `notas` varchar(500) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL
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

-- --------------------------------------------------------

--
-- Table structure for table `pastillero`
--

DROP TABLE IF EXISTS `pastillero`;
CREATE TABLE `pastillero` (
  `id` int(11) NOT NULL,
  `dueno` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `dia_actualizacion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

DROP TABLE IF EXISTS `stock`;
CREATE TABLE `stock` (
  `id` int(11) NOT NULL,
  `droga` int(11) NOT NULL,
  `comprimido` int(11) NOT NULL,
  `cantidad_doceavos` int(11) NOT NULL,
  `fecha_ingreso` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
-- Indexes for table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dosis`
--
ALTER TABLE `dosis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `droga`
--
ALTER TABLE `droga`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `droga_x_dosis`
--
ALTER TABLE `droga_x_dosis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medicina`
--
ALTER TABLE `medicina`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pastillero`
--
ALTER TABLE `pastillero`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
