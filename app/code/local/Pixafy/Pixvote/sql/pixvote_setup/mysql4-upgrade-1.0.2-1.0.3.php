<?php
	$installer	=	$this;
	$installer->startSetup();
	$installer->run("
		SET SQL_MODE=\"NO_AUTO_VALUE_ON_ZERO\";
		SET time_zone = \"+00:00\";

		--
		-- Database: `shop_bevel`
		--

		-- --------------------------------------------------------

		--
		-- Table structure for table `pixafy_design_challenges`
		--

		CREATE TABLE IF NOT EXISTS `pixafy_design_challenges` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `name` varchar(250) NOT NULL,
		  `rules` text NOT NULL,
		  `judges` text NOT NULL,
		  `submission_start_time` datetime NOT NULL,
		  `submission_end_time` datetime NOT NULL,
		  `vote_start_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `vote_end_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `is_visible` tinyint(1) NOT NULL DEFAULT '0',
		  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `updated_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

		-- --------------------------------------------------------

		--
		-- Table structure for table `pixafy_design_item_types`
		--

		CREATE TABLE IF NOT EXISTS `pixafy_design_item_types` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `name` varchar(500) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
	");
	
	$installer->run("
			ALTER TABLE  `pixafy_designs` ADD  `challenge_id` INT NOT NULL AFTER  `customer_id`;
			ALTER TABLE  `pixafy_designs` ADD  `item_type_id` INT NOT NULL AFTER  `challenge_id`;

	");
	$installer->endSetup();
?>