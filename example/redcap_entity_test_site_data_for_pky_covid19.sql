SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


INSERT INTO `redcap_entity_test_site` (`created`,`updated`,`site_long_name`,`site_short_name`,`site_address`,`site_appointment_duration`,`open_time`,`close_time`,`closed_days`,`horizon_days`,`testing_type`,`project_id`,`start_date`) VALUES (1586970569,1586970569,'PK Yonge Tent 1','1','1080 SW 11th St, Gainesville, FL 32601','20','09:00','13:00','6','0','swabandserum',1,1586923200);
INSERT INTO `redcap_entity_test_site` (`created`,`updated`,`site_long_name`,`site_short_name`,`site_address`,`site_appointment_duration`,`open_time`,`close_time`,`closed_days`,`horizon_days`,`testing_type`,`project_id`,`start_date`) VALUES (1586970569,1586970569,'PK Yonge Tent 2','2','1080 SW 11th St, Gainesville, FL 32601','20','09:00','13:00','6','0','swabandserum',1,1586923200);
INSERT INTO `redcap_entity_test_site` (`created`,`updated`,`site_long_name`,`site_short_name`,`site_address`,`site_appointment_duration`,`open_time`,`close_time`,`closed_days`,`horizon_days`,`testing_type`,`project_id`,`start_date`) VALUES (1586970569,1586970569,'PK Yonge Tent 3','3','1080 SW 11th St, Gainesville, FL 32601','20','09:00','13:00','6','0','swabandserum',1,1586923200);
INSERT INTO `redcap_entity_test_site` (`created`,`updated`,`site_long_name`,`site_short_name`,`site_address`,`site_appointment_duration`,`open_time`,`close_time`,`closed_days`,`horizon_days`,`testing_type`,`project_id`,`start_date`) VALUES (1586970569,1586970569,'PK Yonge Tent 4','4','1080 SW 11th St, Gainesville, FL 32601','20','09:00','13:00','6','0','swabandserum',1,1586923200);
INSERT INTO `redcap_entity_test_site` (`created`,`updated`,`site_long_name`,`site_short_name`,`site_address`,`site_appointment_duration`,`open_time`,`close_time`,`closed_days`,`horizon_days`,`testing_type`,`project_id`,`start_date`) VALUES (1586970569,1586970569,'PK Yonge Tent 5','5','1080 SW 11th St, Gainesville, FL 32601','20','09:00','13:00','6','0','swabandserum',1,1586923200);
INSERT INTO `redcap_entity_test_site` (`created`,`updated`,`site_long_name`,`site_short_name`,`site_address`,`site_appointment_duration`,`open_time`,`close_time`,`closed_days`,`horizon_days`,`testing_type`,`project_id`,`start_date`) VALUES (1586970569,1586970569,'PK Yonge Tent 6','6','1080 SW 11th St, Gainesville, FL 32601','20','09:00','13:00','6','0','swabandserum',1,1586923200);

-- redcap_entity_test_site_update.sql
-- revise the project_id of test_site data

update redcap_entity_test_site
set project_id = 2
where project_id = 1;
COMMIT;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
