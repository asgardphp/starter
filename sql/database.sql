-- MySQL dump 10.13  Distrib 5.5.24, for Win64 (x86)
--
-- Host: localhost    Database: coxis3
-- ------------------------------------------------------
-- Server version	5.5.24-log

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
-- Table structure for table `arpa_actualite`
--

DROP TABLE IF EXISTS `arpa_actualite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `arpa_actualite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` text,
  `date` text,
  `lieu` text,
  `introduction` text,
  `contenu` text,
  `slug` text,
  `position` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `filename_image` text,
  `commentaire_id` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=44 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `arpa_actualite`
--

LOCK TABLES `arpa_actualite` WRITE;
/*!40000 ALTER TABLE `arpa_actualite` DISABLE KEYS */;
INSERT INTO `arpa_actualite` VALUES (2,'JOURNEE MONDIALE DE LA VOIX','','','sdfgm','<p>\r\n	Cette ann&eacute;e la Journ&eacute;e Mondiale de la Voix se tiendra le lundi 16 avril 2012 &agrave; l&rsquo;Ecole Sup&eacute;rieure d&rsquo;Audiovisuel de Toulouse.</p>\r\n','journee-mondiale-de-la-voix',1,'2010-11-00 15:37:00','0000-00-00 00:00:00','img2.jpg',2),(17,'JOURNEE MONDIALE DE LA VOIX','','','sdfgm','<p>\r\n	Cette ann&eacute;e la Journ&eacute;e Mondiale de la Voix se tiendra le lundi 16 avril 2012 &agrave; l&rsquo;Ecole Sup&eacute;rieure d&rsquo;Audiovisuel de Toulouse.</p>\r\n','journee-mondiale-de-la-voix-3',0,'2013-11-00 15:37:00','2012-07-21 13:52:21','Chrysanthemum_24.jpg',2),(18,'JOURNEE MONDIALE DE LA VOIX','','','sdfgm','<p>\r\n	Cette ann&eacute;e la Journ&eacute;e Mondiale de la Voix se tiendra le lundi 16 avril 2012 &agrave; l&rsquo;Ecole Sup&eacute;rieure d&rsquo;Audiovisuel de Toulouse.</p>\r\n','journee-mondiale-de-la-voix-4',2,'2016-06-00 15:37:00','0000-00-00 00:00:00','img2.jpg',0),(19,'JOURNEE MONDIALE DE LA VOIX','','','sdfgm','<p>\r\n	Cette ann&eacute;e la Journ&eacute;e Mondiale de la Voix se tiendra le lundi 16 avril 2012 &agrave; l&rsquo;Ecole Sup&eacute;rieure d&rsquo;Audiovisuel de Toulouse.</p>\r\n','journee-mondiale-de-la-voix-5',3,'2016-06-00 15:37:00','0000-00-00 00:00:00','img2.jpg',0),(20,'JOURNEE MONDIALE DE LA VOIX','','','sdfgm','<p>\r\n	Cette ann&eacute;e la Journ&eacute;e Mondiale de la Voix se tiendra le lundi 16 avril 2012 &agrave; l&rsquo;Ecole Sup&eacute;rieure d&rsquo;Audiovisuel de Toulouse.</p>\r\n','journee-mondiale-de-la-voix-6',4,'2016-06-00 15:37:00','0000-00-00 00:00:00','img2.jpg',0),(21,'JOURNEE MONDIALE DE LA VOIX','','','sdfgm','<p>\r\n	Cette ann&eacute;e la Journ&eacute;e Mondiale de la Voix se tiendra le lundi 16 avril 2012 &agrave; l&rsquo;Ecole Sup&eacute;rieure d&rsquo;Audiovisuel de Toulouse.</p>\r\n','journee-mondiale-de-la-voix-2',5,'2016-06-00 15:37:00','0000-00-00 00:00:00','img2.jpg',0),(24,'aaa','','','aaa','aaa','aaa',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','',0),(25,'aaa','','','aaa','aaa','aaa',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','',0),(26,'aaa','','','aaa','aaa','aaa',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','',0),(27,'aaa','','','aaa','aaa','aaa',1,'2012-07-28 15:24:28','2012-07-28 15:24:28','',0),(28,'aaa','','','aaa','aaa','aaa',1,'2012-07-10 15:25:10','2012-07-10 15:25:10','',0),(29,'aaa','','','aaa','aaa','aaa',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','',0),(30,'aaa','','','aaa','aaa','aaa',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','',0),(31,'aaa','','','aaa','aaa','aaa',1,'2012-07-24 15:30:24','2012-07-24 15:30:24','',0),(32,'aaa','','','aaa','aaa','aaa',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','',0),(33,'aaa','','','aaa','aaa','aaa',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','',0),(34,'aaa','','','aaa','aaa','aaa',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','',0),(35,'aaa','','','aaa','aaa','aaa',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','',0),(36,'aaa','','','aaa','aaa','aaa',1,'2012-07-06 15:35:06','2012-07-06 15:35:06','',0),(37,'aaa','','','aaa','aaa','aaa-2',1,'2012-07-07 17:40:07','2012-07-18 09:59:18','',0),(38,'le titre','','','introduction','contenu','le-titre',0,'2012-07-08 12:07:08','2012-07-08 12:07:08','',0),(39,'le titre','','','introduction','contenu','le-titre',0,'2012-07-08 12:35:08','2012-07-08 12:35:08','',0),(40,'le titre','','','introduction','contenu','le-titre',0,'0000-00-00 00:00:00','0000-00-00 00:00:00','',0),(41,'le titre','','','introduction','contenu','le-titre',0,'2012-07-15 12:43:15','2012-07-15 12:43:15','',0),(42,'le titre','','','introduction','contenu','le-titre',0,'2012-07-22 12:43:22','2012-07-22 12:43:22','',0),(43,'le titre','','','introduction','contenu','le-titre',0,'2012-07-04 12:44:04','2012-07-04 12:44:04','',0);
/*!40000 ALTER TABLE `arpa_actualite` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `arpa_actualite_commentaire`
--

DROP TABLE IF EXISTS `arpa_actualite_commentaire`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `arpa_actualite_commentaire` (
  `actualite_id` int(11) NOT NULL,
  `commentaire_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `arpa_actualite_commentaire`
--

LOCK TABLES `arpa_actualite_commentaire` WRITE;
/*!40000 ALTER TABLE `arpa_actualite_commentaire` DISABLE KEYS */;
INSERT INTO `arpa_actualite_commentaire` VALUES (2,2);
/*!40000 ALTER TABLE `arpa_actualite_commentaire` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `arpa_actualite_translation`
--

DROP TABLE IF EXISTS `arpa_actualite_translation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `arpa_actualite_translation` (
  `id` int(11) NOT NULL,
  `locale` varchar(10) NOT NULL,
  `test` text NOT NULL,
  PRIMARY KEY (`id`,`locale`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `arpa_actualite_translation`
--

LOCK TABLES `arpa_actualite_translation` WRITE;
/*!40000 ALTER TABLE `arpa_actualite_translation` DISABLE KEYS */;
INSERT INTO `arpa_actualite_translation` VALUES (2,'en','Hello'),(2,'fr','Bonjour'),(25,'fr','un test'),(26,'fr','un test'),(29,'fr','un test'),(30,'fr','un test'),(31,'fr','un test'),(32,'fr',''),(33,'fr','un test'),(34,'fr','un test'),(35,'en','un test'),(35,'fr',''),(36,'en','a test'),(36,'fr','un test'),(37,'en','a test'),(37,'fr','un test !'),(38,'fr',''),(39,'fr',''),(40,'fr',''),(41,'fr',''),(42,'fr',''),(43,'fr','');
/*!40000 ALTER TABLE `arpa_actualite_translation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `arpa_administrator`
--

DROP TABLE IF EXISTS `arpa_administrator`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `arpa_administrator` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `arpa_administrator`
--

LOCK TABLES `arpa_administrator` WRITE;
/*!40000 ALTER TABLE `arpa_administrator` DISABLE KEYS */;
INSERT INTO `arpa_administrator` VALUES (1,'admin','aa9760ca2f0d59de8fd6eabebd61c4cf1b8ad972');
/*!40000 ALTER TABLE `arpa_administrator` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `arpa_annonce`
--

DROP TABLE IF EXISTS `arpa_annonce`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `arpa_annonce` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `intitule` text,
  `categorie` text,
  `region` text,
  `adresse` text,
  `ville` text,
  `code_postal` text,
  `contenu` varchar(600) NOT NULL,
  `nom` text,
  `prenom` text,
  `portable` text,
  `telephone` text,
  `email` text,
  `site_web` text,
  `slug` text,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `arpa_annonce`
--

LOCK TABLES `arpa_annonce` WRITE;
/*!40000 ALTER TABLE `arpa_annonce` DISABLE KEYS */;
INSERT INTO `arpa_annonce` VALUES (1,'Ensemble vocal Le Tourdion cherche un chef','Chorale','Haute-Garonne','','Toulouse','','Recherche ...','Hognerud','Michel','','303030303','bob@joe.com','joe.com','ensemble-vocal-le-tourdion-cherche-un-chef','0000-00-00 00:00:00','0000-00-00 00:00:00'),(2,'Ensemble vocal Le Tourdion cherche un chef vocal Le Tourdion cherche un chef','Recherche','Doubs','','Besancon','','Aaaaaaaah','Ghislain','Llorca','','303030303','lol@lol.com','lol.com','ensemble-vocal-le-tourdion-cherche-un-chef-vocal-le-tourdion-cherche-un-chef','0000-00-00 00:00:00','0000-00-00 00:00:00'),(3,'Ensemble vocal Le Tourdion cherche un chef','Stage','Midi-PyrÃ©nÃ©es','1 rue de la paix','Besancon','25000','Trolololol','Obama','Barrack','','811565908','barrack@obama.com','obama.com','ensemble-vocal-le-tourdion-cherche-un-chef-2','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `arpa_annonce` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `arpa_article`
--

DROP TABLE IF EXISTS `arpa_article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `arpa_article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `arpa_article`
--

LOCK TABLES `arpa_article` WRITE;
/*!40000 ALTER TABLE `arpa_article` DISABLE KEYS */;
INSERT INTO `arpa_article` VALUES (1,'About Me'),(2,'Introduction');
/*!40000 ALTER TABLE `arpa_article` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `arpa_article_author`
--

DROP TABLE IF EXISTS `arpa_article_author`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `arpa_article_author` (
  `article_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  PRIMARY KEY (`article_id`,`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `arpa_article_author`
--

LOCK TABLES `arpa_article_author` WRITE;
/*!40000 ALTER TABLE `arpa_article_author` DISABLE KEYS */;
INSERT INTO `arpa_article_author` VALUES (1,1);
/*!40000 ALTER TABLE `arpa_article_author` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `arpa_author`
--

DROP TABLE IF EXISTS `arpa_author`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `arpa_author` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `arpa_author`
--

LOCK TABLES `arpa_author` WRITE;
/*!40000 ALTER TABLE `arpa_author` DISABLE KEYS */;
INSERT INTO `arpa_author` VALUES (1,'Michel'),(2,'Bob');
/*!40000 ALTER TABLE `arpa_author` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `arpa_choeur`
--

DROP TABLE IF EXISTS `arpa_choeur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `arpa_choeur` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` text,
  `region` text,
  `adresse` text,
  `ville` text,
  `code_postal` text,
  `telephone` text,
  `mobile` text,
  `email` text,
  `site_web` text,
  `lieu_repetition_adresse` text,
  `lieu_repetition_ville` text,
  `lieu_repetition_code_postal` text,
  `repetitions_horaires` text,
  `style_musical` text,
  `responsable_adresse` text,
  `responsable_code_postal` text,
  `responsable_ville` text,
  `responsable_nom` text,
  `responsable_prenom` text,
  `responsable_telephone` text,
  `responsable_mobile` text,
  `responsable_email` text,
  `conditions_admission` text,
  `type_choeurs` text,
  `slug` text,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `arpa_choeur`
--

LOCK TABLES `arpa_choeur` WRITE;
/*!40000 ALTER TABLE `arpa_choeur` DISABLE KEYS */;
INSERT INTO `arpa_choeur` VALUES (1,'GlÃ¼e Design','Haute-Garonne','12 rue Arnaud vidal','Toulouse','31000','05 53 68 63 18','06 65 09 16 81','gl@glue-design.com','www.glue-design.com','12 rue arnaud Vidal','Toulouse','31000','Les rÃ©pÃ©titions ont lieux tous les jours \r\nde 18h00 Ã  20h00. \r\nSauf le dimanche : pas de rÃ©pÃ©tition.','a:2:{i:0;s:18:\"ChÅ“ur dâ€™enfants\";i:1;s:18:\"ChÅ“ur dâ€™adultes\";}','','','','Llorca','Ghislain','303030303','','ghislain@llorca.com','a:2:{i:0;s:16:\"Un test de chant\";i:1;s:12:\"Un entretien\";}','a:3:{i:0;s:7:\"Baroque\";i:1;s:15:\"Chants du monde\";i:2;s:9:\"Classique\";}','glue-design','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `arpa_choeur` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `arpa_commentaire`
--

DROP TABLE IF EXISTS `arpa_commentaire`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `arpa_commentaire` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `actualite_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `arpa_commentaire`
--

LOCK TABLES `arpa_commentaire` WRITE;
/*!40000 ALTER TABLE `arpa_commentaire` DISABLE KEYS */;
INSERT INTO `arpa_commentaire` VALUES (2,'un com','2012-01-00 00:00:00','2012-07-09 20:24:09',2);
/*!40000 ALTER TABLE `arpa_commentaire` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `arpa_document`
--

DROP TABLE IF EXISTS `arpa_document`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `arpa_document` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` text,
  `description` text,
  `position` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `filename_document` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `arpa_document`
--

LOCK TABLES `arpa_document` WRITE;
/*!40000 ALTER TABLE `arpa_document` DISABLE KEYS */;
INSERT INTO `arpa_document` VALUES (1,'Titre de lâ€™Ã©tude','attention le texte de ne doit pas excÃ©der les 400 caractÃ¨res. Description de lâ€™Ã©tude : attention le texte de ne doit pas excÃ©der les 400 caractÃ¨res.Description de lâ€™Ã©tude : attention le texte de ne doit pas excÃ©der les 400 caractÃ¨res. Description de lâ€™Ã©tude : attention le texte de ne doit pas excÃ©der les 400 caractÃ¨res.',0,'0000-00-00 00:00:00','0000-00-00 00:00:00','img1.png'),(2,'Titre de lâ€™Ã©tude',' attention le texte de ne doit pas excÃ©der les 400 caractÃ¨res. Description de lâ€™Ã©tude : attention le texte de ne doit pas excÃ©der les 400 caractÃ¨res.Description de lâ€™Ã©tude : attention le texte de ne doit pas excÃ©der les 400 caractÃ¨res. Description de lâ€™Ã©tude : attention le texte de ne doit pas excÃ©der les 400 caractÃ¨res.',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','img3.png'),(3,'Titre de lâ€™Ã©tude',' attention le texte de ne doit pas excÃ©der les 400 caractÃ¨res. Description de lâ€™Ã©tude : attention le texte de ne doit pas excÃ©der les 400 caractÃ¨res.Description de lâ€™Ã©tude : attention le texte de ne doit pas excÃ©der les 400 caractÃ¨res. Description de lâ€™Ã©tude : attention le texte de ne doit pas excÃ©der les 400 caractÃ¨res.',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','img3_1.png'),(4,'Titre de lâ€™Ã©tude','attention le texte de ne doit pas excÃ©der les 400 caractÃ¨res. Description de lâ€™Ã©tude : attention le texte de ne doit pas excÃ©der les 400 caractÃ¨res.Description de lâ€™Ã©tude : attention le texte de ne doit pas excÃ©der les 400 caractÃ¨res. Description de lâ€™Ã©tude : attention le texte de ne doit pas excÃ©der les 400 caractÃ¨res.',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','img2.png');
/*!40000 ALTER TABLE `arpa_document` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `arpa_foo`
--

DROP TABLE IF EXISTS `arpa_foo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `arpa_foo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_naissance` text NOT NULL,
  `mot_de_passe` text NOT NULL,
  `email` text NOT NULL,
  `slug` text NOT NULL,
  `position` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `filename_image` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `arpa_foo`
--

LOCK TABLES `arpa_foo` WRITE;
/*!40000 ALTER TABLE `arpa_foo` DISABLE KEYS */;
INSERT INTO `arpa_foo` VALUES (1,'4/6/2011','a','a','a',0,'0000-00-00 00:00:00','0000-00-00 00:00:00','a:0:{}'),(2,'4/6/2011','a','a','a',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','a:1:{i:0;s:4:\".jpg\";}'),(3,'4/6/2011','a','a','a',1,'2012-07-19 19:56:19','2012-07-19 19:56:19','a:1:{i:0;s:11:\"php7421.jpg\";}'),(4,'4/6/2011','a','a','a',1,'2012-07-12 20:06:12','2012-07-12 20:06:12','a:0:{}'),(5,'4/6/2011','a','a','a',1,'2012-07-23 20:06:23','2012-07-23 20:06:23','a:0:{}'),(6,'4/6/2011','a','a','a',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','a:0:{}'),(7,'4/6/2011','a','a','a',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','a:0:{}'),(8,'4/6/2011','a','a','a',1,'2012-07-01 20:07:01','2012-07-01 20:07:01','a:0:{}'),(9,'4/6/2011','a','a','a',1,'2012-07-19 20:07:19','2012-07-19 20:07:19','a:1:{i:0;s:20:\"Chrysanthemum_18.jpg\";}'),(10,'4/6/2011','a','a','a',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','a:1:{i:0;s:20:\"Chrysanthemum_19.jpg\";}'),(11,'4/6/2011','a','a','a',1,'2012-07-11 20:11:11','2012-07-11 20:11:11','a:1:{i:0;s:20:\"Chrysanthemum_20.jpg\";}'),(12,'4/6/2011','0cc175b9c0f1b6a831c399e269772661','a','a',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','a:1:{i:0;s:20:\"Chrysanthemum_21.jpg\";}'),(13,'4/6/2011','0cc175b9c0f1b6a831c399e269772661','a','a',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','a:1:{i:0;s:20:\"Chrysanthemum_22.jpg\";}'),(14,'','d41d8cd98f00b204e9800998ecf8427e','','n-a',1,'2012-07-00 20:01:00','2012-07-00 20:01:00','a:0:{}'),(15,'','d41d8cd98f00b204e9800998ecf8427e','','n-a',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','a:0:{}');
/*!40000 ALTER TABLE `arpa_foo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `arpa_formation`
--

DROP TABLE IF EXISTS `arpa_formation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `arpa_formation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` text,
  `date` text,
  `lieu` text,
  `introduction` text,
  `contenu` text,
  `meta_title` text,
  `meta_description` text,
  `meta_keywords` text,
  `slug` text,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `filename_image` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `arpa_formation`
--

LOCK TABLES `arpa_formation` WRITE;
/*!40000 ALTER TABLE `arpa_formation` DISABLE KEYS */;
INSERT INTO `arpa_formation` VALUES (1,'Physiologie de la voix : apports thÃ©oriques et pratiques','Physiologie de la voix : apports thÃ©oriques et pratiques','Foix centre universitaire.','Le Dr Sabine Crestani nous fera partager ses connaissances sur la question de la physiologie de la voix ainsi que sur la question des pathologies vocales frÃ©quemment rencontrÃ©es chez les enfants et les adultes qui utilisent leur voix dans des conditions parfois difficiles comme les enseignants. Jean-Louis COMORETTO est chanteur professionnel.','<p>\r\n	Intervenant : Sabine CRESTANI &amp; Jean-Louis COMORETTO<br />\r\n	Sabine CRESTANI est m&eacute;decin ORL sp&eacute;cialis&eacute; en chirurgie de la face et du cou, DIU Voix, parole et d&eacute;glutition, praticien hospitalier &agrave; l&rsquo;h&ocirc;pital Larrey CHU de Toulouse, service du Pr Serrano dans l&rsquo;Unit&eacute; Voix Parole et D&eacute;glutition du Dr Woisard. Le Dr Sabine Crestani nous fera partager ses connaissances sur la question de la physiologie de la voix ainsi que sur la question des pathologies vocales fr&eacute;quemment rencontr&eacute;es chez les enfants et les adultes qui utilisent leur voix dans des conditions parfois difficiles comme les enseignants.<br />\r\n	Jean-Louis COMORETTO est chanteur professionnel, directeur de l&rsquo;ARPA et directeur de l&rsquo;ensemble vocal A Sei Voci. Il interviendra sur la prise de conscience de l&rsquo;importance de la posture, sur le travail de la voix parl&eacute;e et chant&eacute;e, sur la coordination phono respiratoire au travers d&rsquo;exercices sp&eacute;cifiques.</p>\r\n<p>\r\n	Contenu :</p>\r\n<ul>\r\n	<li>\r\n		Partie th&eacute;orique : physionomie-anatomie du larynx, fonctionnement de l&rsquo;appareil phonatoire. Supports utilis&eacute;s : sch&eacute;mas, squelette, diaporama par exemple.</li>\r\n	<li>\r\n		Partie pratique : proposition d&rsquo;exercices sur la coordination phono respiratoire, la prise de conscience de l&rsquo;importance de la posture, le travail de la voix parl&eacute;e, projet&eacute;e et chant&eacute;e.</li>\r\n</ul>\r\n<p>\r\n	Public concern&eacute; : Amateurs ou professionnels de la voix (chanteurs, com&eacute;diens, enseignants, avocats, choristes...)</p>\r\n<p>\r\n	Tarif : gratuit&eacute; pour les stagiaires ari&eacute;geois dans le cadre des accords avec le Conseil G&eacute;n&eacute;ral 09, 20 &euro; pour une inscription individuelle + 15 &euro; d&rsquo;adh&eacute;sion &agrave; l&rsquo;ARPA, 45 &euro; dans le cadre de la prise en charge par l&rsquo;employeur au titre de la formation professionnelle continue + 15 &euro; d&rsquo;adh&eacute;sion &agrave; l&rsquo;ARPA.</p>\r\n','','','','physiologie-de-la-voix-apports-theoriques-et-pratiques','0000-00-00 00:00:00','0000-00-00 00:00:00','img2.jpg'),(2,'Polyphonies baltes et dâ€™Europe du nord','Le 16 octobre 2011, 8 janvier et 11 mars 2012','Maison de la Musique - Le Garric (81)','Ce stage est rÃ©alisÃ© Ã  lâ€™initiative de lâ€™ADDA du Tarn en partenariat avec lâ€™ARPA.','Ce stage est rÃ©alisÃ© Ã  lâ€™initiative de lâ€™ADDA du Tarn en partenariat avec lâ€™ARPA.','','','','polyphonies-baltes-et-deurope-du-nord','0000-00-00 00:00:00','0000-00-00 00:00:00',''),(3,'OMMM : Vocal effervescent','Le 15 et 16 octobre 2011','salle des fÃªtes dâ€™Escalquens (31)','Ce stage est organisÃ© en partenariat avec le festival Â« VOIX CROISÃ‰ES Â» Ommm câ€™est la rencontre de 5 voix singuliÃ¨res (Manon, Mayon, Melow, Mathis et Sam) et dâ€™un alchimiste des sons (LÃ©o) sur le terrain du groove, de lâ€™improvisation et de lâ€™humour.','<p>\r\n	<span style=\"color: rgb(77, 77, 79); font-family: Tahoma, Geneva, sans-serif; line-height: 15px; \">Ce stage est organis&eacute; en partenariat avec le festival &laquo; VOIX CROIS&Eacute;ES &raquo; Ommm c&rsquo;est la rencontre de 5 voix singuli&egrave;res (Manon, Mayon, Melow, Mathis et Sam) et d&rsquo;un alchimiste des sons (L&eacute;o) sur le terrain du groove, de l&rsquo;improvisation et de l&rsquo;humour.</span></p>\r\n','','','','ommm-vocal-effervescent','0000-00-00 00:00:00','0000-00-00 00:00:00','');
/*!40000 ALTER TABLE `arpa_formation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `arpa_inscrit`
--

DROP TABLE IF EXISTS `arpa_inscrit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `arpa_inscrit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` text,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `arpa_inscrit`
--

LOCK TABLES `arpa_inscrit` WRITE;
/*!40000 ALTER TABLE `arpa_inscrit` DISABLE KEYS */;
INSERT INTO `arpa_inscrit` VALUES (2,'sdf@dfg.com','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `arpa_inscrit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `arpa_page`
--

DROP TABLE IF EXISTS `arpa_page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `arpa_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `name` text,
  `content` text,
  `position` int(11) NOT NULL,
  `meta_title` text,
  `meta_description` text,
  `meta_keywords` text,
  `slug` text,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `arpa_page`
--

LOCK TABLES `arpa_page` WRITE;
/*!40000 ALTER TABLE `arpa_page` DISABLE KEYS */;
INSERT INTO `arpa_page` VALUES (1,'L\'Arpa','arpa','<p>\r\n	&nbsp;</p>\r\n<p style=\"margin-bottom: 15px; color: rgb(77, 77, 79); font-family: Tahoma, Geneva, sans-serif; \">\r\n	<strong>Aide aux chorales&nbsp;</strong><br />\r\n	Il s&#39;agit de 4h30 de formation gratuite dispens&eacute;e par un intervenant de l&rsquo;A.R.P.A. avec des conseils techniques adapt&eacute;s au r&eacute;pertoire travaill&eacute;.</p>\r\n<p style=\"margin-bottom: 15px; color: rgb(77, 77, 79); font-family: Tahoma, Geneva, sans-serif; \">\r\n	<strong>Aide aux projets</strong><br />\r\n	Si vous avez un projet artistique fort, si vous souhaitez &eacute;laborer un plan de formation sp&eacute;cifique pour votre ch&oelig;ur, l&rsquo;ARPA met &agrave; disposition un intervenant et vous aide dans votre d&eacute;marche. Un entretien pr&eacute;alable permettra de d&eacute;finir les objectifs g&eacute;n&eacute;raux de la formation, le contenu p&eacute;dagogique, le volume et le calendrier des interventions.</p>\r\n<p style=\"margin-bottom: 15px; color: rgb(77, 77, 79); font-family: Tahoma, Geneva, sans-serif; \">\r\n	<strong>Conseil et Expertise &quot;sur mesure&quot;</strong><br />\r\n	Les derni&egrave;res saisons d&eacute;montrent un int&eacute;r&ecirc;t croissant des ch&oelig;urs et chefs de ch&oelig;ur pour des conseils adapt&eacute;s. Vous vous posez des questions sur votre pratique en g&eacute;n&eacute;ral et plus particuli&egrave;rement sur vos techniques de r&eacute;p&eacute;titions l&rsquo;&eacute;tude de votre r&eacute;pertoire, le niveau vocal et musical de votre groupe, la d&eacute;finition d&rsquo;un nouveau projet... Nous vous proposons de vous accompagner dans l&rsquo;excercice quotidien de votre pratique, dans la mesure des disponibilit&eacute;s des intervenants et de vous mettre en relation avec une personne ressource pour vous faire profiter de notre exp&eacute;rience.</p>\r\n<p style=\"margin-bottom: 15px; color: rgb(77, 77, 79); font-family: Tahoma, Geneva, sans-serif; \">\r\n	<strong>Financement</strong><br />\r\n	Le co&ucirc;t de la formation peut-&ecirc;tre pris en charge en partie selon chaque d&eacute;partement. Le dispositif habituel consiste en la r&eacute;partition des frais li&eacute;s &agrave; l&rsquo;intervention &quot;prorata temporis&quot; &agrave; &eacute;galit&eacute; entre l&rsquo;ARPA, le ch&oelig;ur et votre relais d&eacute;partemental. Les frais de d&eacute;placement de l&rsquo;intervenant sont &agrave; la charge du ch&oelig;ur.</p>\r\n<p style=\"margin-bottom: 15px; color: rgb(77, 77, 79); font-family: Tahoma, Geneva, sans-serif; \">\r\n	<strong>Modalit&eacute;s</strong><br />\r\n	Pour b&eacute;n&eacute;ficier de ces 3 types de formations vous devez faire une demande par &eacute;crit avant le 30 novembre de chaque ann&eacute;e aupr&egrave;s de votre relais d&eacute;partemental. Les interventions se d&eacute;rouleront dans la mesure du possible dans le courant du 1er semestre de l&rsquo;ann&eacute;e civile suivante. Vous devez &eacute;galement acquitter &quot;l&rsquo;adh&eacute;sion chorale&quot; de 60&euro; &agrave; l&rsquo;ARPA qui assurera la gestion administrative et sociale des intervenants.</p>\r\n',0,'','','','l-arpa','0000-00-00 00:00:00','0000-00-00 00:00:00'),(2,'Partenaires','partenaires','<p>\r\n	sdfgj</p>\r\n',1,'','','','partenaires','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `arpa_page` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `arpa_preferences`
--

DROP TABLE IF EXISTS `arpa_preferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `arpa_preferences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text,
  `email` varchar(255) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `telephone` varchar(255) DEFAULT NULL,
  `head_script` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `arpa_preferences`
--

LOCK TABLES `arpa_preferences` WRITE;
/*!40000 ALTER TABLE `arpa_preferences` DISABLE KEYS */;
INSERT INTO `arpa_preferences` VALUES (1,'','','','','');
/*!40000 ALTER TABLE `arpa_preferences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `arpa_professeur`
--

DROP TABLE IF EXISTS `arpa_professeur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `arpa_professeur` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` text,
  `prenoms` text,
  `region` text,
  `adresse` text,
  `ville` text,
  `code_postal` text,
  `telephone` text,
  `email` text,
  `site_web` text,
  `cours_particuliers` text,
  `type_choeurs` text,
  `informations_complementaires` varchar(600) NOT NULL,
  `slug` text,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `arpa_professeur`
--

LOCK TABLES `arpa_professeur` WRITE;
/*!40000 ALTER TABLE `arpa_professeur` DISABLE KEYS */;
INSERT INTO `arpa_professeur` VALUES (1,'Llorca','Ghislain','Haute-Garonne','12 rue Arnaud vidal','Toulouse','31000','05 53 68 63 18','gl@glue-design.com','www.glue-design.com','oui','a:3:{i:0;s:6:\"Gospel\";i:1;s:4:\"Jazz\";i:2;s:6:\"MÃ©tal\";}','Vous pouvez me joindre du lundi au jeudi de 9h00 Ã  14h00. Je peux venir avec mon matÃ©riel (guitare si nÃ©cessaire). L\'heure de cours est Ã  15 â‚¬. Plusieurs peronnes peuvent participer au cours (3 â‚¬ sup. par personne).','llorca','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `arpa_professeur` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `arpa_test`
--

DROP TABLE IF EXISTS `arpa_test`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `arpa_test` (
  `date` text NOT NULL,
  `lieu` text NOT NULL,
  `titre` text NOT NULL,
  `introduction` text NOT NULL,
  `contenu` text NOT NULL,
  `id` int(11) NOT NULL,
  `slug` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `filename_image` text NOT NULL,
  `actualite_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `arpa_test`
--

LOCK TABLES `arpa_test` WRITE;
/*!40000 ALTER TABLE `arpa_test` DISABLE KEYS */;
/*!40000 ALTER TABLE `arpa_test` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `arpa_value`
--

DROP TABLE IF EXISTS `arpa_value`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `arpa_value` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` text,
  `value` text,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `arpa_value`
--

LOCK TABLES `arpa_value` WRITE;
/*!40000 ALTER TABLE `arpa_value` DISABLE KEYS */;
INSERT INTO `arpa_value` VALUES (1,'name','ARPA','1967-06-00 00:00:00','2012-07-19 18:34:19'),(2,'email','leyou.m@gmail.com','1967-06-00 00:00:00','2012-07-19 18:34:19'),(3,'head_script','sdfgh','1967-06-00 00:00:00','2012-07-19 18:34:19');
/*!40000 ALTER TABLE `arpa_value` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-08-24 17:02:23