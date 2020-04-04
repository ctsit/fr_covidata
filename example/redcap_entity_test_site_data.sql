SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


INSERT INTO `redcap_entity_test_site` (`created`, `updated`, `site_long_name`, `site_short_name`, `site_address`, `site_appointment_duration`, `open_time`, `close_time`, `closed_days`, `horizon_days`, `testing_type`, `project_id`) VALUES(1585878002, 1585878668, 'Kanapaha Emergency Department', 'KED', '7405 SW Archer Road Gainesville, FL 32608', '15', '7:00', '19:00', '6', '3', 'swab', 1);
INSERT INTO `redcap_entity_test_site` (`created`, `updated`, `site_long_name`, `site_short_name`, `site_address`, `site_appointment_duration`, `open_time`, `close_time`, `closed_days`, `horizon_days`, `testing_type`, `project_id`) VALUES(1585878660, 1585878660, 'Spring Hill Emergency Department', 'SHED', '8475 NW 39th Ave, Gainesville, FL 32606', '15', '8:00', '18:00', '6', '3', 'swab', 1);
INSERT INTO `redcap_entity_test_site` (`created`, `updated`, `site_long_name`, `site_short_name`, `site_address`, `site_appointment_duration`, `open_time`, `close_time`, `closed_days`, `horizon_days`, `testing_type`, `project_id`) VALUES(1585878864, 1585878949, 'Archer Road Emergency Department - Swab & Serum Test', 'AEDS', '1515 SW Archer Rd, Gainesville, FL 32608', '20', '7:00', '12:00', '6', '3', 'swabandserum', 1);
INSERT INTO `redcap_entity_test_site` (`created`, `updated`, `site_long_name`, `site_short_name`, `site_address`, `site_appointment_duration`, `open_time`, `close_time`, `closed_days`, `horizon_days`, `testing_type`, `project_id`) VALUES(1585878923, 1585878923, 'Archer Road Emergency Department ', 'AED', '1515 SW Archer Rd, Gainesville, FL 32608', '15', '12:00', '24:00', '6', '3', 'swab', 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
