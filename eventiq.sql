-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 13, 2025 at 06:21 AM
-- Server version: 10.4.32-MariaDB-log
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eventiq`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `pesan_tiket` (IN `uid` INT, IN `eid` INT, IN `jumlah` INT)   BEGIN
  DECLARE harga DECIMAL(10,2);
  DECLARE total DECIMAL(10,2);

  SELECT harga_tiket INTO harga FROM events WHERE id_event = eid;
  SET total = harga * jumlah;

  INSERT INTO tickets(id_user, id_event, jumlah_tiket, total_bayar, status)
  VALUES (uid, eid, jumlah, total, 'pending');
END$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `cek_kuota` (`eid` INT) RETURNS TINYINT(1)  BEGIN
  DECLARE sisa INT;
  SELECT kuota INTO sisa FROM events WHERE id_event = eid;
  RETURN sisa > 0;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id_event` int(11) NOT NULL,
  `judul_event` varchar(100) DEFAULT NULL,
  `kategori` varchar(50) DEFAULT NULL,
  `lokasi` varchar(100) DEFAULT NULL,
  `jadwal` datetime DEFAULT NULL,
  `kuota` int(11) DEFAULT NULL,
  `harga_tiket` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id_event`, `judul_event`, `kategori`, `lokasi`, `jadwal`, `kuota`, `harga_tiket`) VALUES
(4, 'Test', 'Test', 'Test', '2025-06-15 09:30:00', 13, 10000.00),
(5, 'a', 'a', 'a', '2025-08-08 19:00:00', 25, 1000.00);

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id_tiket` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_event` int(11) DEFAULT NULL,
  `jumlah_tiket` int(11) DEFAULT NULL,
  `total_bayar` decimal(10,2) DEFAULT NULL,
  `qr_code` varchar(255) DEFAULT NULL,
  `status` enum('pending','valid') DEFAULT NULL,
  `tanggal_pesan` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id_tiket`, `id_user`, `id_event`, `jumlah_tiket`, `total_bayar`, `qr_code`, `status`, `tanggal_pesan`) VALUES
(5, 1, 4, 1, 10000.00, 'qrcodes/tiket_5.png', '', '2025-06-07 08:28:34');

--
-- Triggers `tickets`
--
DELIMITER $$
CREATE TRIGGER `kurangi_kuota` AFTER INSERT ON `tickets` FOR EACH ROW BEGIN
  UPDATE events SET kuota = kuota - NEW.jumlah_tiket
  WHERE id_event = NEW.id_event;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `role` enum('admin','pengguna') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama`, `email`, `password`, `role`) VALUES
(1, 'RHD', 'rhd@gmail.com', '$2y$10$ZwBC3F3bkPgpUxt0X.32buthFKFth53aT7F3U8gzDzp.UUca4cCwq', 'admin'),
(2, 'RHD 2', 'rhd123@gmail.com', '$2y$10$QlLtyluTDc.gAeb7bJ0DRuWp3RmZg/xjwWG4ZchvKNDafRtYAkike', 'pengguna');

-- --------------------------------------------------------

--
-- Table structure for table `validasi`
--

CREATE TABLE `validasi` (
  `id_validasi` int(11) NOT NULL,
  `id_tiket` int(11) DEFAULT NULL,
  `waktu_validasi` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id_event`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id_tiket`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_event` (`id_event`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- Indexes for table `validasi`
--
ALTER TABLE `validasi`
  ADD PRIMARY KEY (`id_validasi`),
  ADD KEY `id_tiket` (`id_tiket`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id_event` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id_tiket` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `validasi`
--
ALTER TABLE `validasi`
  MODIFY `id_validasi` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`id_event`) REFERENCES `events` (`id_event`);

--
-- Constraints for table `validasi`
--
ALTER TABLE `validasi`
  ADD CONSTRAINT `validasi_ibfk_1` FOREIGN KEY (`id_tiket`) REFERENCES `tickets` (`id_tiket`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
