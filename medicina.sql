-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Mar 16, 2020 at 11:44 PM
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
(1, '9:30 AM', 1),
(2, 'Mediodia', 1),
(3, '4 PM', 1),
(4, 'Cena', 1),
(5, 'Noche', 1),
(6, 'mañana', 2),
(7, 'Noche', 2);

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

--
-- Dumping data for table `droga`
--

INSERT INTO `droga` (`id`, `nombre`, `pastillero`) VALUES
(14, 'Tacrolimus', 1),
(15, 'Prednisona', 1),
(16, 'Furosemide', 1),
(17, 'Lercardipina', 1),
(18, 'Losartan', 1),
(19, 'Esoprazol', 1),
(20, 'Levodopa', 1),
(21, 'Bisopropol', 1),
(22, 'Linagliptina', 1),
(23, 'Oxibutinina', 1),
(24, 'Allopurinol', 1),
(25, 'Escitalopram', 1),
(26, 'Atovastatina', 1),
(27, 'Melatonina', 1),
(28, 'Prueba droga', 2),
(29, 'Prueba esde postman', 2),
(30, 'Prueba desde app', 2),
(31, 'PRueba desde app 2', 2),
(32, 'er', 2),
(33, '123', 2),
(34, '13', 2),
(35, '44', 2),
(36, 'Prueba desde postman 5', 2);

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

--
-- Dumping data for table `droga_x_dosis`
--

INSERT INTO `droga_x_dosis` (`id`, `droga_id`, `dosis_id`, `cantidad_mg`, `notas`) VALUES
(3, 14, 1, 1, ''),
(4, 15, 1, 5, ''),
(5, 16, 1, 40, ''),
(6, 17, 1, 10, ''),
(7, 18, 1, 25, ''),
(8, 19, 1, 20, ''),
(9, 20, 1, 250, ''),
(10, 21, 2, 5, ''),
(11, 22, 2, 5, ''),
(12, 23, 2, 200, ''),
(13, 20, 2, 125, ''),
(14, 24, 3, 50, ''),
(15, 20, 3, 250, ''),
(16, 25, 3, 20, ''),
(18, 14, 4, 0.5, ''),
(19, 17, 4, 10, ''),
(20, 18, 4, 25, ''),
(21, 26, 4, 10, ''),
(22, 20, 4, 250, ''),
(26, 25, 6, 2466220, ''),
(29, 25, 7, 50, 'notas'),
(31, 28, 6, 109, 'Otras notas editadas33333'),
(32, 27, 7, 13, 'Notas'),
(33, 28, 7, 12, '14'),
(34, 36, 6, 10, 'Nomtas de prueba desde postman');

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
  `dueno` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pastillero`
--

INSERT INTO `pastillero` (`id`, `dueno`) VALUES
(1, 'Julio'),
(2, 'PRUEBA');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `droga`
--
ALTER TABLE `droga`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `droga_x_dosis`
--
ALTER TABLE `droga_x_dosis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `medicina`
--
ALTER TABLE `medicina`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pastillero`
--
ALTER TABLE `pastillero`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
