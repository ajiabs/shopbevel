<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$installer	=	$this;
$installer->startSetup();
$installer->run("
CREATE TABLE IF NOT EXISTS `pixafy_designs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `name` varchar(500) NOT NULL,
  `description` varchar(500) NOT NULL,
  `price` double NOT NULL,
  `bevel_price` double NOT NULL DEFAULT '0',
  `status_id` int(11) NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_on` datetime NOT NULL,
  `vote_start_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `vote_end_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `status_id` (`status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pixafy_design_comments`
--

CREATE TABLE IF NOT EXISTS `pixafy_design_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `design_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  KEY `design_id` (`design_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pixafy_design_images`
--

CREATE TABLE IF NOT EXISTS `pixafy_design_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `design_id` int(11) NOT NULL,
  `url` varchar(600) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `design_id` (`design_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pixafy_design_status`
--

CREATE TABLE IF NOT EXISTS `pixafy_design_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pixafy_design_votes`
--

CREATE TABLE IF NOT EXISTS `pixafy_design_votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `design_id` int(11) NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  KEY `design_id` (`design_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pixafy_design_comments`
--
ALTER TABLE `pixafy_design_comments`
  ADD CONSTRAINT `pixafy_design_comments_ibfk_1` FOREIGN KEY (`design_id`) REFERENCES `pixafy_designs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pixafy_design_images`
--
ALTER TABLE `pixafy_design_images`
  ADD CONSTRAINT `pixafy_design_images_ibfk_1` FOREIGN KEY (`design_id`) REFERENCES `pixafy_designs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pixafy_design_votes`
--
ALTER TABLE `pixafy_design_votes`
  ADD CONSTRAINT `pixafy_design_votes_ibfk_1` FOREIGN KEY (`design_id`) REFERENCES `pixafy_designs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `pixafy_designs`  ADD `is_staff_pick` BOOLEAN NOT NULL DEFAULT '0' AFTER `status_id`,  ADD `is_featured` BOOLEAN NOT NULL DEFAULT '0' AFTER `is_staff_pick`;

ALTER TABLE  `pixafy_design_images` ADD  `sort_order` INT NOT NULL DEFAULT  '0';

");

$installer->endSetup();
?>
