-- MySQL dump 10.13  Distrib 5.7.17, for Linux (i686)
--
-- Host: localhost    Database: blockchain
-- ------------------------------------------------------
-- Server version	5.7.17-0ubuntu0.16.04.1

--
-- Table structure for table `hashes`
--

DROP TABLE IF EXISTS `hashes`;

CREATE TABLE `hashes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `block_id` bigint(20) DEFAULT NULL,
  `source_key` varchar(255) DEFAULT NULL,
  `hash` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


--
-- Table structure for table `headers`
--

DROP TABLE IF EXISTS `blocks`;

CREATE TABLE `blocks` (
  `id` bigint(20) unsigned zerofill NOT NULL,
  `table_name` varchar(255) DEFAULT NULL,
  `block_number` bigint(20) DEFAULT NULL,
  `version` varchar(45) DEFAULT NULL,
  `hash_previous_block` varchar(255) DEFAULT NULL,
  `hash_merkle_root` varchar(255) DEFAULT NULL,
  `stamp` bigint(20) DEFAULT NULL,
  `difficulty` varchar(255) DEFAULT NULL,
  `nonce` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

