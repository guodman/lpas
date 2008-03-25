-- MySQL dump 10.11
--
-- Host: localhost    Database: restaurants
-- ------------------------------------------------------
-- Server version	5.0.45-Debian_1ubuntu3.3-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `restaurants`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `restaurants` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `restaurants`;

--
-- Table structure for table `attendees`
--

DROP TABLE IF EXISTS `attendees`;
CREATE TABLE `attendees` (
  `RestaurantID` int(11) NOT NULL,
  `User` varchar(25) NOT NULL,
  `Rating` int(11) NOT NULL,
  PRIMARY KEY  (`RestaurantID`,`User`),
  CONSTRAINT `attendees_ibfk_1` FOREIGN KEY (`RestaurantID`) REFERENCES `restaurants` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `attendees`
--

LOCK TABLES `attendees` WRITE;
/*!40000 ALTER TABLE `attendees` DISABLE KEYS */;
INSERT INTO `attendees` VALUES (1,'Andy',50),(1,'doug',50),(1,'jacob',60),(1,'Stephen',60),(2,'Andy',100),(2,'doug',50),(2,'jacob',60),(2,'Stephen',60),(3,'Andy',50),(3,'doug',10),(3,'jacob',0),(3,'Stephen',75),(4,'Andy',50),(4,'doug',80),(4,'jacob',100),(4,'Stephen',1),(5,'Andy',100),(5,'doug',60),(5,'jacob',70),(5,'Stephen',55),(6,'Andy',50),(6,'doug',40),(6,'jacob',40),(6,'Stephen',60),(7,'Andy',50),(7,'doug',90),(7,'jacob',50),(7,'Stephen',75),(8,'Andy',50),(8,'doug',50),(8,'jacob',90),(8,'Stephen',80),(9,'Andy',90),(9,'doug',60),(9,'jacob',70),(9,'Stephen',50),(10,'Andy',75),(10,'doug',70),(10,'jacob',75),(10,'Stephen',5),(11,'Andy',1),(11,'doug',80),(11,'jacob',65),(11,'Stephen',75),(12,'Andy',50),(12,'doug',40),(12,'jacob',50),(12,'Stephen',75),(13,'Andy',100),(13,'doug',50),(13,'jacob',80),(13,'Stephen',70),(14,'Andy',75),(14,'doug',50),(14,'jacob',30),(14,'Stephen',70),(15,'Andy',50),(15,'doug',65);
/*!40000 ALTER TABLE `attendees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `history`
--

DROP TABLE IF EXISTS `history`;
CREATE TABLE `history` (
  `RestaurantID` int(11) NOT NULL,
  `Date` date NOT NULL,
  `Duration` smallint(6) default NULL,
  PRIMARY KEY  (`RestaurantID`,`Date`),
  CONSTRAINT `history_ibfk_1` FOREIGN KEY (`RestaurantID`) REFERENCES `restaurants` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `history`
--

LOCK TABLES `history` WRITE;
/*!40000 ALTER TABLE `history` DISABLE KEYS */;
INSERT INTO `history` VALUES (1,'2008-03-06',NULL),(2,'2008-03-07',NULL),(2,'2008-03-17',NULL),(3,'2008-03-10',NULL),(4,'2008-03-11',NULL),(4,'2008-03-21',30),(5,'2008-03-12',NULL),(6,'2008-03-13',NULL),(8,'2008-03-20',60),(10,'2008-03-24',30),(11,'2008-03-18',NULL),(13,'2008-03-14',NULL),(14,'2008-03-19',45);
/*!40000 ALTER TABLE `history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `restaurants`
--

DROP TABLE IF EXISTS `restaurants`;
CREATE TABLE `restaurants` (
  `ID` int(11) NOT NULL auto_increment,
  `Name` varchar(100) NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `restaurants`
--

LOCK TABLES `restaurants` WRITE;
/*!40000 ALTER TABLE `restaurants` DISABLE KEYS */;
INSERT INTO `restaurants` VALUES (12,'Carbones'),(10,'Chipotle'),(7,'Clicquot Club Cafe'),(6,'Davannis'),(2,'Green Mill Restaurant'),(14,'Green Mill Slices'),(5,'Groveland Tap'),(3,'Jimmy Johns'),(1,'Lake Street Garage'),(9,'Longfellow Grill'),(11,'Neighborhood Cafe'),(8,'Pizza Luce'),(13,'Saint Clair Broiler'),(15,'Subway'),(4,'Taste of Thailand');
/*!40000 ALTER TABLE `restaurants` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2008-03-25 13:22:24
