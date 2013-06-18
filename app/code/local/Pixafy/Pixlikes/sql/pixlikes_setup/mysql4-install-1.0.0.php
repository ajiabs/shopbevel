<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$installer	=	$this;
$installer->startSetup();
$installer->run("
CREATE TABLE IF NOT EXISTS `pixafy_product_likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `product_id` int(11) NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
$installer->endSetup();
?>
