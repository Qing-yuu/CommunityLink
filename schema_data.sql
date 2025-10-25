-- MySQL dump 10.13  Distrib 9.3.0, for macos15.2 (arm64)
--
-- Host: localhost    Database: fit2104_assessment3
-- ------------------------------------------------------
-- Server version	9.3.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` enum('unread','read') NOT NULL DEFAULT 'unread',
  PRIMARY KEY (`id`),
  KEY `idx_contact_isread` (`is_read`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_messages`
--

LOCK TABLES `contact_messages` WRITE;
/*!40000 ALTER TABLE `contact_messages` DISABLE KEYS */;
INSERT INTO `contact_messages` VALUES (1,'Alice Johnson','alice.johnson@example.com','Volunteering Inquiry','I would like to know more about volunteering opportunities.','unread'),(2,'Benjamin Lee','ben.lee@example.com','Event Participation','How can I register for the upcoming community sports day?','read'),(3,'Charlotte Smith','charlotte.smith@example.com','Donation','Do you accept second-hand clothes donations?','unread'),(4,'Daniel Martin','daniel.martin@example.com','Support Request','I need assistance with finding affordable housing.','read'),(5,'Ella Brown','ella.brown@example.com','Pet Adoption','I am interested in adopting a dog from your next fair.','unread'),(6,'Frank Wilson','frank.wilson@example.com','Partnership Proposal','Our company wants to partner for food distribution events.','unread'),(7,'Grace Thompson','grace.thompson@example.com','Volunteer Confirmation','Can you confirm my volunteer registration for next week?','read'),(8,'Henry Davis','henry.davis@example.com','Flood Relief','Where can I drop off donations for flood victims?','unread'),(9,'Isabella Garcia','isabella.garcia@example.com','Cultural Event','Do you still need performers for the cultural night?','read'),(10,'Jack Taylor','jack.taylor@example.com','Book Drive','Can I donate textbooks for the rural school program?','unread'),(11,'Karen Miller','karen.miller@example.com','Senior Care','Is there any training for elderly care volunteers?','read'),(12,'Liam Harris','liam.harris@example.com','Mental Health','Do you provide counselling sessions for students?','unread'),(13,'Mia White','mia.white@example.com','Library Event','How can I join the 24-hour reading marathon?','read'),(14,'Noah King','noah.king@example.com','Housing Support','I need more details about housing support services.','unread'),(15,'Olivia Scott','olivia.scott@example.com','Refugee Aid','Do you accept household items for refugees?','read'),(16,'Paul Adams','paul.adams@example.com','Women Empowerment','Can I register my daughter for the empowerment workshop?','unread'),(17,'Quinn Baker','quinn.baker@example.com','Tech Hackathon','Is the hackathon open to university students?','read'),(18,'Rachel Clark','rachel.clark@example.com','Neighborhood Safety','Can you share the safety guidelines from the last workshop?','unread'),(19,'Samuel Lewis','samuel.lewis@example.com','Charity Art Event','Are paintings for sale at the charity exhibition?','read'),(20,'Tina Evans','tina.evans@example.com','General Inquiry','What other programs are planned for next year?','unread');
/*!40000 ALTER TABLE `contact_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--



DROP TABLE IF EXISTS `organisations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `organisations` (
                                 `organisation_id` bigint unsigned NOT NULL AUTO_INCREMENT,
                                 `org_name` varchar(255) NOT NULL,
                                 `contact_person_full_name` varchar(255) NOT NULL,
                                 `email` varchar(255) NOT NULL,
                                 `phone` varchar(50) NOT NULL,
                                 `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                                 PRIMARY KEY (`organisation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;




DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `event_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `date` date NOT NULL,
  `organisation_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`event_id`),
  KEY `idx_events_org_date` (`organisation_id`,`date`),
  CONSTRAINT `fk_event_org` FOREIGN KEY (`organisation_id`) REFERENCES `organisations` (`organisation_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES (1,'Free Health Checkup','Melbourne City Hall','Offering free health checkups to the community.','2025-02-15',1,'2025-09-05 09:09:15'),(2,'Tree Plantation Drive','Royal Botanic Gardens','Join us to plant 500 new trees.','2025-03-10',2,'2025-09-05 09:09:15'),(3,'Clothes Donation Camp','Federation Square','Collecting warm clothes for the homeless.','2025-03-20',3,'2025-09-05 09:09:15'),(4,'Youth Leadership Workshop','Monash University','Training youth leaders for tomorrow.','2025-04-05',4,'2025-09-05 09:09:15'),(5,'Pet Adoption Fair','Carlton Gardens','Find a loving home for rescued animals.','2025-04-12',5,'2025-09-05 09:09:15'),(6,'Elderly Care Seminar','Docklands Community Center','Awareness program for senior citizens.','2025-05-01',6,'2025-09-05 09:09:15'),(7,'Food Distribution','Flinders Street Station','Serving meals to underprivileged families.','2025-05-15',7,'2025-09-05 09:09:15'),(8,'Flood Relief Collection','Richmond Town Hall','Collecting supplies for flood victims.','2025-06-01',8,'2025-09-05 09:09:15'),(9,'Clean Water Awareness','St Kilda Beach','Educating people about clean water usage.','2025-06-10',9,'2025-09-05 09:09:15'),(10,'Book Donation Drive','State Library Victoria','Donate books to rural schools.','2025-07-01',10,'2025-09-05 09:09:15'),(11,'Community Sports Day','Albert Park','Sports event to promote health.','2025-07-15',11,'2025-09-05 09:09:15'),(12,'Mental Health Talk','Collins Street Conference Hall','Session with mental health experts.','2025-08-01',12,'2025-09-05 09:09:15'),(13,'Library Reading Marathon','Local Library','24-hour community reading event.','2025-08-12',13,'2025-09-05 09:09:15'),(14,'Affordable Housing Expo','Melbourne Exhibition Centre','Showcasing housing support solutions.','2025-09-05',14,'2025-09-05 09:09:15'),(15,'Refugee Support Meet','Carlton Community Hall','Supporting refugee families.','2025-09-20',15,'2025-09-05 09:09:15'),(16,'Women Empowerment Fair','Southbank Promenade','Workshops for skill development.','2025-10-01',16,'2025-09-05 09:09:15'),(17,'International Cultural Night','Melbourne Town Hall','Celebrating cultural diversity.','2025-10-18',17,'2025-09-05 09:09:15'),(18,'Tech for Social Good Hackathon','RMIT University','Hackathon to solve community issues.','2025-11-01',18,'2025-09-05 09:09:15'),(19,'Neighborhood Safety Workshop','Brunswick Community Center','Tips for safer neighborhoods.','2025-11-15',19,'2025-09-05 09:09:15'),(20,'Art Exhibition for Charity','NGV International','Art showcase supporting local charities.','2025-12-01',20,'2025-09-05 09:09:15');
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organisations`
--


/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organisations`
--

LOCK TABLES `organisations` WRITE;
/*!40000 ALTER TABLE `organisations` DISABLE KEYS */;
INSERT INTO `organisations` VALUES (1,'Community Health Center','Alice Wong','alice.wong@chc.org','+61 400 123 456','2025-09-05 09:09:15'),(2,'Green Earth NGO','Benjamin Lee','ben.lee@greenearth.org','+61 400 234 567','2025-09-05 09:09:15'),(3,'Helping Hands','Charlotte Smith','charlotte.smith@hh.org','+61 400 345 678','2025-09-05 09:09:15'),(4,'Youth Empowerment','Daniel Johnson','daniel.johnson@ye.org','+61 400 456 789','2025-09-05 09:09:15'),(5,'Animal Rescue Victoria','Ella Brown','ella.brown@arv.org','+61 400 567 890','2025-09-05 09:09:15'),(6,'Senior Care Foundation','Frank Miller','frank.miller@scf.org','+61 400 678 901','2025-09-05 09:09:15'),(7,'Food Bank Australia','Grace Wilson','grace.wilson@fba.org','+61 400 789 012','2025-09-05 09:09:15'),(8,'Disaster Relief Team','Henry Davis','henry.davis@drt.org','+61 400 890 123','2025-09-05 09:09:15'),(9,'Clean Water Project','Isabella Garcia','isabella.garcia@cwp.org','+61 400 901 234','2025-09-05 09:09:15'),(10,'Education For All','Jack Martinez','jack.martinez@efa.org','+61 400 012 345','2025-09-05 09:09:15'),(11,'Community Sports Club','Karen Taylor','karen.taylor@csc.org','+61 400 234 890','2025-09-05 09:09:15'),(12,'Mental Health Awareness','Liam Harris','liam.harris@mha.org','+61 400 345 901','2025-09-05 09:09:15'),(13,'Local Library Friends','Mia Thompson','mia.thompson@llf.org','+61 400 456 012','2025-09-05 09:09:15'),(14,'Housing Support Network','Noah White','noah.white@hsn.org','+61 400 567 123','2025-09-05 09:09:15'),(15,'Refugee Aid Center','Olivia Martin','olivia.martin@rac.org','+61 400 678 234','2025-09-05 09:09:15'),(16,'Women Empowerment Group','Paul King','paul.king@weg.org','+61 400 789 345','2025-09-05 09:09:15'),(17,'Cultural Exchange Program','Quinn Scott','quinn.scott@cep.org','+61 400 890 456','2025-09-05 09:09:15'),(18,'Tech for Good','Rachel Adams','rachel.adams@tfg.org','+61 400 901 567','2025-09-05 09:09:15'),(19,'Neighborhood Watch','Samuel Baker','samuel.baker@nw.org','+61 400 012 678','2025-09-05 09:09:15'),(20,'Arts for Community','Tina Clark','tina.clark@afc.org','+61 400 123 789','2025-09-05 09:09:15');
/*!40000 ALTER TABLE `organisations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `identity` enum('admin','volunteer') NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'amy.tan@communitylink.com','$2y$12$GB1sKJHqRSy7LFaBBO9ni.hGYfo8aHlqIPM1Ndaej8XuG7lsbX8tu','admin',1),(2,'vol1@example.com','$2y$12$GB1sKJHqRSy7LFaBBO9ni.hGYfo8aHlqIPM1Ndaej8XuG7lsbX8tu','volunteer',1),(3,'vol2@example.com','c4318372f98f4c46ed3a32c16ee4d7a76c832886d887631c0294b3314f34edf1','volunteer',0),(4,'vol3@example.com','c4318372f98f4c46ed3a32c16ee4d7a76c832886d887631c0294b3314f34edf1','volunteer',0),(5,'vol4@example.com','c4318372f98f4c46ed3a32c16ee4d7a76c832886d887631c0294b3314f34edf1','volunteer',0),(6,'vol5@example.com','c4318372f98f4c46ed3a32c16ee4d7a76c832886d887631c0294b3314f34edf1','volunteer',0),(7,'vol6@example.com','c4318372f98f4c46ed3a32c16ee4d7a76c832886d887631c0294b3314f34edf1','volunteer',0),(8,'vol7@example.com','c4318372f98f4c46ed3a32c16ee4d7a76c832886d887631c0294b3314f34edf1','volunteer',0),(9,'vol8@example.com','c4318372f98f4c46ed3a32c16ee4d7a76c832886d887631c0294b3314f34edf1','volunteer',0),(10,'vol9@example.com','c4318372f98f4c46ed3a32c16ee4d7a76c832886d887631c0294b3314f34edf1','volunteer',0),(11,'vol10@example.com','c4318372f98f4c46ed3a32c16ee4d7a76c832886d887631c0294b3314f34edf1','volunteer',0),(12,'vol11@example.com','c4318372f98f4c46ed3a32c16ee4d7a76c832886d887631c0294b3314f34edf1','volunteer',0),(13,'vol12@example.com','c4318372f98f4c46ed3a32c16ee4d7a76c832886d887631c0294b3314f34edf1','volunteer',0),(14,'vol13@example.com','c4318372f98f4c46ed3a32c16ee4d7a76c832886d887631c0294b3314f34edf1','volunteer',0),(15,'vol14@example.com','c4318372f98f4c46ed3a32c16ee4d7a76c832886d887631c0294b3314f34edf1','volunteer',0),(16,'vol15@example.com','c4318372f98f4c46ed3a32c16ee4d7a76c832886d887631c0294b3314f34edf1','volunteer',0),(17,'vol16@example.com','c4318372f98f4c46ed3a32c16ee4d7a76c832886d887631c0294b3314f34edf1','volunteer',0),(18,'vol17@example.com','c4318372f98f4c46ed3a32c16ee4d7a76c832886d887631c0294b3314f34edf1','volunteer',0),(19,'vol18@example.com','c4318372f98f4c46ed3a32c16ee4d7a76c832886d887631c0294b3314f34edf1','volunteer',0),(20,'vol19@example.com','c4318372f98f4c46ed3a32c16ee4d7a76c832886d887631c0294b3314f34edf1','volunteer',0),(21,'vol20@example.com','c4318372f98f4c46ed3a32c16ee4d7a76c832886d887631c0294b3314f34edf1','volunteer',0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;




-- No demo data for volunteer_event (assignments will be created via the web app)
--
-- Table structure for table `volunteers`
--

DROP TABLE IF EXISTS `volunteers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `volunteers` (
  `volunteer_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `skills` text NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `status` enum('unhired','hired') NOT NULL DEFAULT 'unhired',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`volunteer_id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `fk_vol_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `volunteers`
--

--
-- Dumping data for table `volunteer_event`
--
-- Table structure for table `volunteer_event`
DROP TABLE IF EXISTS `volunteer_event`;
CREATE TABLE `volunteer_event` (
                                   `volunteer_id` BIGINT UNSIGNED NOT NULL,
                                   `event_id`     BIGINT UNSIGNED NOT NULL,
                                   `role` ENUM('helper','leader') DEFAULT 'helper',
                                   `assigned_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                   PRIMARY KEY (`volunteer_id`, `event_id`),
                                   CONSTRAINT `fk_ve_vol`
                                       FOREIGN KEY (`volunteer_id`) REFERENCES `volunteers`(`volunteer_id`)
                                           ON DELETE CASCADE ON UPDATE CASCADE,
                                   CONSTRAINT `fk_ve_event`
                                       FOREIGN KEY (`event_id`) REFERENCES `events`(`event_id`)
                                           ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_0900_ai_ci;



LOCK TABLES `volunteers` WRITE;
/*!40000 ALTER TABLE `volunteers` DISABLE KEYS */;
INSERT INTO `volunteers` VALUES (1,2,'Alice Johnson','0412345678','Event coordination',NULL,'hired','2025-09-05 09:09:15'),(2,3,'Benjamin Lee','0412345679','Logistics',NULL,'unhired','2025-09-05 09:09:15'),(3,4,'Charlotte Smith','0412345680','First aid',NULL,'unhired','2025-09-05 09:09:15'),(4,5,'Daniel Martin','0412345681','Cooking',NULL,'unhired','2025-09-05 09:09:15'),(5,6,'Ella Brown','0412345682','Teaching',NULL,'unhired','2025-09-05 09:09:15'),(6,7,'Frank Wilson','0412345683','Driving',NULL,'unhired','2025-09-05 09:09:15'),(7,8,'Grace Thompson','0412345684','Fundraising',NULL,'unhired','2025-09-05 09:09:15'),(8,9,'Henry Walker','0412345685','Sports coaching',NULL,'unhired','2025-09-05 09:09:15'),(9,10,'Isla Robinson','0412345686','Music',NULL,'unhired','2025-09-05 09:09:15'),(10,11,'Jack Harris','0412345687','Mentoring',NULL,'unhired','2025-09-05 09:09:15'),(11,12,'Kaitlyn Scott','0412345688','Photography',NULL,'unhired','2025-09-05 09:09:15'),(12,13,'Liam White','0412345689','Design',NULL,'unhired','2025-09-05 09:09:15'),(13,14,'Mia Hall','0412345690','Childcare',NULL,'unhired','2025-09-05 09:09:15'),(14,15,'Noah Allen','0412345691','Cleaning',NULL,'unhired','2025-09-05 09:09:15'),(15,16,'Olivia Young','0412345692','Administration',NULL,'unhired','2025-09-05 09:09:15'),(16,17,'Peter King','0412345693','Gardening',NULL,'unhired','2025-09-05 09:09:15'),(17,18,'Quinn Wright','0412345694','Tech support',NULL,'unhired','2025-09-05 09:09:15'),(18,19,'Ruby Adams','0412345695','Event planning',NULL,'unhired','2025-09-05 09:09:15'),(19,20,'Samuel Baker','0412345696','Customer service',NULL,'unhired','2025-09-05 09:09:15'),(20,21,'Tara Evans','0412345697','Social media',NULL,'unhired','2025-09-05 09:09:15');
/*!40000 ALTER TABLE `volunteers` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-06  3:05:30
