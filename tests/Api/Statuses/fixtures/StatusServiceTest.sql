-- MariaDB dump 10.19  Distrib 10.5.15-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: 127.0.0.1    Database: support_local
-- ------------------------------------------------------
-- Server version	10.5.15-MariaDB-0+deb11u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `support_local`
--

/*!40000 DROP DATABASE IF EXISTS `support_local`*/;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `support_local` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

USE `support_local`;

--
-- Table structure for table `billings`
--

DROP TABLE IF EXISTS `billings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `billings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) DEFAULT NULL,
  `project` int(11) DEFAULT NULL,
  `billed` decimal(10,0) DEFAULT NULL,
  `created_on` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `billings_projects_null_fk` (`project`),
  KEY `billings_users_null_fk` (`user`),
  CONSTRAINT `billings_projects_null_fk` FOREIGN KEY (`project`) REFERENCES `projects` (`id`),
  CONSTRAINT `billings_users_null_fk` FOREIGN KEY (`user`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `billings`
--

LOCK TABLES `billings` WRITE;
/*!40000 ALTER TABLE `billings` DISABLE KEYS */;
INSERT INTO `billings` VALUES (5,3,2,111,'2023-03-16 18:41:15');
/*!40000 ALTER TABLE `billings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bug_report`
--

DROP TABLE IF EXISTS `bug_report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bug_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket` int(11) DEFAULT NULL,
  `expected` text NOT NULL,
  `actual` text NOT NULL,
  `steps_to_reproduce` text NOT NULL,
  `solution` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bug_report_tickets_id_fk` (`ticket`),
  CONSTRAINT `bug_report_tickets_id_fk` FOREIGN KEY (`ticket`) REFERENCES `tickets` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bug_report`
--

LOCK TABLES `bug_report` WRITE;
/*!40000 ALTER TABLE `bug_report` DISABLE KEYS */;
/*!40000 ALTER TABLE `bug_report` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `change_orders`
--

DROP TABLE IF EXISTS `change_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `change_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket` int(11) DEFAULT NULL,
  `title` char(150) NOT NULL,
  `current_feature` text NOT NULL,
  `required_changes` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `change_orders_tickets_id_fk` (`ticket`),
  CONSTRAINT `change_orders_tickets_id_fk` FOREIGN KEY (`ticket`) REFERENCES `tickets` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `change_orders`
--

LOCK TABLES `change_orders` WRITE;
/*!40000 ALTER TABLE `change_orders` DISABLE KEYS */;
INSERT INTO `change_orders` VALUES (1,3,'Example title','Now it is...','I need to be changed ...');
/*!40000 ALTER TABLE `change_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(150) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clients`
--

LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
INSERT INTO `clients` VALUES (1,'Test','2023-03-14 18:59:00');
/*!40000 ALTER TABLE `clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket` int(11) NOT NULL,
  `type` int(11) NOT NULL COMMENT '1=reply; 2=internal;',
  `created_by` int(11) NOT NULL,
  `created_on` datetime NOT NULL DEFAULT current_timestamp(),
  `comment` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comments_tickets_id_fk` (`ticket`),
  KEY `craeted_by__fk` (`created_by`),
  CONSTRAINT `comments_tickets_id_fk` FOREIGN KEY (`ticket`) REFERENCES `tickets` (`id`),
  CONSTRAINT `craeted_by__fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES (1,1,1,1,'2023-03-15 21:42:17','Test');
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `faq`
--

DROP TABLE IF EXISTS `faq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `faq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` char(250) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `faq`
--

LOCK TABLES `faq` WRITE;
/*!40000 ALTER TABLE `faq` DISABLE KEYS */;
INSERT INTO `faq` VALUES (1,'What is..?','aaaaaaaaa'),(2,'How much is..?','bbbbbbb');
/*!40000 ALTER TABLE `faq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feature_requests`
--

DROP TABLE IF EXISTS `feature_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feature_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket` int(11) DEFAULT NULL,
  `title` char(150) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `feature_requests_tickets_id_fk` (`ticket`),
  CONSTRAINT `feature_requests_tickets_id_fk` FOREIGN KEY (`ticket`) REFERENCES `tickets` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feature_requests`
--

LOCK TABLES `feature_requests` WRITE;
/*!40000 ALTER TABLE `feature_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `feature_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(100) DEFAULT NULL,
  `company` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `projects_clients_null_fk` (`company`),
  CONSTRAINT `projects_clients_null_fk` FOREIGN KEY (`company`) REFERENCES `clients` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES (2,'Test Project',1,'2023-03-14 19:31:50');
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_navigation_items`
--

DROP TABLE IF EXISTS `role_navigation_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_navigation_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` int(11) DEFAULT NULL,
  `icon` char(50) DEFAULT NULL,
  `title` char(50) DEFAULT NULL,
  `link` char(255) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `enabled` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_navigation_items_id_uindex` (`id`),
  KEY `role_navigation_items_roles_id_fk` (`role`),
  CONSTRAINT `role_navigation_items_roles_id_fk` FOREIGN KEY (`role`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_navigation_items`
--

LOCK TABLES `role_navigation_items` WRITE;
/*!40000 ALTER TABLE `role_navigation_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_navigation_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(25) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_id_uindex` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COMMENT='Holds the user roles in the system.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Admin'),(3,'Client');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_categories`
--

DROP TABLE IF EXISTS `ticket_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(255) DEFAULT NULL,
  `url` char(255) DEFAULT NULL,
  `icon` char(255) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_categories`
--

LOCK TABLES `ticket_categories` WRITE;
/*!40000 ALTER TABLE `ticket_categories` DISABLE KEYS */;
INSERT INTO `ticket_categories` VALUES (1,'Technical Support','technical-support',NULL,NULL),(2,'Phone Service Support','phone-support',NULL,NULL),(3,'Digital Marketing Order','marketing-support',NULL,NULL),(4,'Software Development','software-development',NULL,NULL);
/*!40000 ALTER TABLE `ticket_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_progress`
--

DROP TABLE IF EXISTS `ticket_progress`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_progress` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `added_on` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ticket_progress_tickets_null_fk` (`ticket_id`),
  KEY `ticket_progress_users_null_fk` (`user_id`),
  KEY `ticket_progress_ticket_status_null_fk` (`status`),
  CONSTRAINT `ticket_progress_tickets_null_fk` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`),
  CONSTRAINT `ticket_progress_users_null_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_progress`
--

LOCK TABLES `ticket_progress` WRITE;
/*!40000 ALTER TABLE `ticket_progress` DISABLE KEYS */;
/*!40000 ALTER TABLE `ticket_progress` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_status`
--

DROP TABLE IF EXISTS `ticket_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_status`
--

LOCK TABLES `ticket_status` WRITE;
/*!40000 ALTER TABLE `ticket_status` DISABLE KEYS */;
INSERT INTO `ticket_status` VALUES (1,'Open'),(2,'Blocked'),(3,'In Progress'),(4,'Ready for QA'),(5,'Ready to close'),(6,'Resolved'),(7,'Closed');
/*!40000 ALTER TABLE `ticket_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_types`
--

DROP TABLE IF EXISTS `ticket_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_types`
--

LOCK TABLES `ticket_types` WRITE;
/*!40000 ALTER TABLE `ticket_types` DISABLE KEYS */;
INSERT INTO `ticket_types` VALUES (1,'Bug Report'),(2,'Feature Request'),(3,'Change Order');
/*!40000 ALTER TABLE `ticket_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` char(200) NOT NULL,
  `ticket_type` int(11) DEFAULT NULL,
  `category` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `assignee` int(11) DEFAULT NULL,
  `created_on` datetime NOT NULL DEFAULT current_timestamp(),
  `status` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `tickets_ticket_categories_id_fk` (`category`),
  KEY `tickets_ticket_types_id_fk` (`ticket_type`),
  KEY `tickets_users_id_fk` (`created_by`),
  KEY `tickets_users_assignee_id_fk` (`assignee`),
  CONSTRAINT `tickets_ticket_categories_id_fk` FOREIGN KEY (`category`) REFERENCES `ticket_categories` (`id`),
  CONSTRAINT `tickets_ticket_types_id_fk` FOREIGN KEY (`ticket_type`) REFERENCES `ticket_types` (`id`),
  CONSTRAINT `tickets_users_assignee_id_fk` FOREIGN KEY (`assignee`) REFERENCES `users` (`id`),
  CONSTRAINT `tickets_users_id_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tickets`
--

LOCK TABLES `tickets` WRITE;
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
INSERT INTO `tickets` VALUES (1,'test',3,3,1,1,'2023-03-13 19:01:41',1),(3,'',3,3,1,1,'2023-03-13 19:01:41',1);
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `uploads`
--

DROP TABLE IF EXISTS `uploads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uploads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `original_file_name` char(255) DEFAULT NULL,
  `storage_path` char(255) DEFAULT NULL,
  `url` char(255) DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `mime_type` char(120) DEFAULT NULL,
  `created_on` datetime DEFAULT current_timestamp(),
  `upload_type` char(20) DEFAULT NULL,
  `ticket` int(11) DEFAULT NULL,
  `comment` int(11) DEFAULT NULL,
  `file_hash` char(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uploads_comments_null_fk` (`comment`),
  KEY `uploads_tickets_null_fk` (`ticket`),
  KEY `uploads_users_null_fk` (`uploaded_by`),
  CONSTRAINT `uploads_comments_null_fk` FOREIGN KEY (`comment`) REFERENCES `comments` (`id`),
  CONSTRAINT `uploads_tickets_null_fk` FOREIGN KEY (`ticket`) REFERENCES `tickets` (`id`),
  CONSTRAINT `uploads_users_null_fk` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `uploads`
--

LOCK TABLES `uploads` WRITE;
/*!40000 ALTER TABLE `uploads` DISABLE KEYS */;
INSERT INTO `uploads` VALUES (1,'test.jpg','/home/didityyyy/uploads/9f052830-c39a-4bac-81a2-64bf78c38030.jpg','https://dev.support.hph.io/uploads/9f052830-c39a-4bac-81a2-64bf78c38030.jpg',1,'image/jpeg','2023-03-15 21:33:12','ticket',1,NULL,'dd9816890bc4db3414c8cd4b6585964580c7aa14'),(2,'test.jpg','/home/didityyyy/uploads/9f052830-c39a-4bac-81a2-64bf78c38030.jpg','https://dev.support.hph.io/uploads/9f052830-c39a-4bac-81a2-64bf78c38030.jpg',1,'image/jpeg','2023-03-15 21:44:28','comment',NULL,1,'dd9816890bc4db3414c8cd4b6585964580c7aa14');
/*!40000 ALTER TABLE `uploads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_tokens`
--

DROP TABLE IF EXISTS `user_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) DEFAULT NULL,
  `token` varchar(1024) DEFAULT NULL,
  `delete_after` datetime NOT NULL,
  `jti` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_tokens_id_uindex` (`id`),
  KEY `user_tokens_users_id_fk` (`user`),
  KEY `user_tokens_jti_index` (`jti`),
  CONSTRAINT `user_tokens_users_id_fk` FOREIGN KEY (`user`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Holds JWTs that are used as bearer tokens for acces.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_tokens`
--

LOCK TABLES `user_tokens` WRITE;
/*!40000 ALTER TABLE `user_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` char(255) NOT NULL,
  `email` char(255) NOT NULL,
  `password` char(64) DEFAULT NULL,
  `nonce` char(36) DEFAULT NULL,
  `role` int(11) DEFAULT NULL,
  `first` char(60) DEFAULT NULL,
  `last` char(60) DEFAULT NULL,
  `company_name` char(100) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT current_timestamp(),
  `activation_status` tinyint(4) DEFAULT 0 COMMENT 'Determines the status of activation. 0 is new (not activated), 1 is active, 2 is disabled (not in spec, just reserved for future use).',
  `activated_on` datetime DEFAULT NULL,
  `password_version` int(11) DEFAULT 1,
  `uuid` char(36) NOT NULL DEFAULT uuid() COMMENT 'The user''s UUID for use with messaging.',
  PRIMARY KEY (`username`),
  UNIQUE KEY `users_email_uindex` (`email`),
  UNIQUE KEY `users_id_uindex` (`id`),
  UNIQUE KEY `users_pk` (`uuid`),
  UNIQUE KEY `users_username_uindex` (`username`),
  KEY `users__fk_role` (`role`),
  KEY `users_clients_null_fk` (`parent_id`),
  CONSTRAINT `users__fk_role` FOREIGN KEY (`role`) REFERENCES `roles` (`id`),
  CONSTRAINT `users_clients_null_fk` FOREIGN KEY (`parent_id`) REFERENCES `clients` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (3,'aaa@aaa.com','aaa@aaa.com','123',NULL,3,'aaa','aaa','www',NULL,'2023-03-16 18:40:25','2023-03-16 18:40:29',1,'2023-03-16 18:40:34',1,'e793a1fe-bb8e-11ed-8a45-cae72503a121'),(1,'kaloyan@hph.io','kaloyan@hph.io','$2y$10$fqew8TQSNDBRcXugHh2D2elmkSsH8Vd0vs0CnkeQoI2xJb/2XEVee',NULL,1,'Kaloyan','Stoyanov',NULL,NULL,'2022-04-05 12:17:14',NULL,1,NULL,NULL,'a815a6a2-7d50-11ed-a6c9-f0d4e2e605b0');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'support_local'
--

--
-- Dumping routines for database 'support_local'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-03-31 19:00:57
