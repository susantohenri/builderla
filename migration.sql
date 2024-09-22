-- -------------------------------------------------------------
-- TablePlus 6.0.6(558)
--
-- https://tableplus.com/
--
-- Database: local
-- Generation Time: 2024-06-14 13:08:41.8810
-- -------------------------------------------------------------


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombres` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefono` int(10) NOT NULL,
  `secret` text NOT NULL,
  `nuevo` int(1) NOT NULL,
  `createdBy` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `historial`;
CREATE TABLE `historial` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vehiculo_id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `contenido` text NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uploads` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `vehiculo_id` (`vehiculo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `modelos`;
CREATE TABLE `modelos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `marca` varchar(255) NOT NULL,
  `modelo` varchar(255) NOT NULL,
  `version` varchar(255) NOT NULL,
  `imagen` text NOT NULL,
  `blueprint` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `ot`;
CREATE TABLE `ot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) NOT NULL,
  `vehiculo_id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `detalle` text NOT NULL,
  `valor` int(11) NOT NULL,
  `estado` int(1) NOT NULL,
  `regdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `upddate` datetime DEFAULT NULL,
  `entregar` tinyint(1) NOT NULL DEFAULT '0',
  `entregardate` date DEFAULT NULL,
  `site_services` text NOT NULL,
  `customer_to_provide` text NOT NULL,
  `not_included` text NOT NULL,
  `price_breakdown` tinyint(1) NOT NULL DEFAULT '0',
  `client_signature` text,
  `client_initial` text,
  `client_dob` date DEFAULT NULL,
  `signed_date` date DEFAULT NULL,
  `estimate_status` varchar(50),
  `contract_status` varchar(50),
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `solicitud`;
CREATE TABLE `solicitud` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) NOT NULL,
  `vehiculo_id` int(11) DEFAULT NULL,
  `solicitud` text NOT NULL,
  `details` text NOT NULL,
  `photos` text NOT NULL,
  `estado` int(1) NOT NULL,
  `regdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `upddate` datetime DEFAULT NULL,
  `ot_id` int(11) NOT NULL,
  `motivo` text NOT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time NOT NULL,
  `expense` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `total` double NOT NULL,
  `iva_debito` double NOT NULL,
  `iva_credito` double NOT NULL,
  `gastos` double NOT NULL,
  `utilidad` double NOT NULL,
  `createdBy` int(10) NOT NULL,
  `owner_over_65` tinyint(1) NOT NULL DEFAULT '0',
  `construction_lender_name` varchar(255),
  `construction_lender_address` text,
  `approximate_start_date` date DEFAULT NULL,
  `approximate_completion_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `vehiculos`;
CREATE TABLE `vehiculos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modelo` varchar(255) NOT NULL,
  `street_address` varchar(255) NOT NULL,
  `address_line_2` varchar(255) DEFAULT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `zip_code` varchar(10) NOT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `cliente_id_2` int(11) DEFAULT NULL,
  `createdBy` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;



/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
