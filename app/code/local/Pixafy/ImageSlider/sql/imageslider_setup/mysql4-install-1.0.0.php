<?php
$this->startSetup();
$this->run("
CREATE TABLE IF NOT EXISTS `imageslider_sliders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `embed_code` varchar(500) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `seconds` int(11) DEFAULT '5',
  `wysiwyg_code` varchar(1000) DEFAULT NULL,
  `php_code` varchar(1000) DEFAULT NULL,
  `open_new_window` int(11) DEFAULT '1',
  `slider_height` int(11) DEFAULT '0',
  `slider_width` int(11) DEFAULT '0',
  `show_arrows` enum('Yes','No') DEFAULT 'No',
  `show_dots` enum('Yes','No') DEFAULT 'No',
  `is_active` enum('Yes','No') DEFAULT 'Yes',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `imageslider_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image_path` varchar(500) DEFAULT NULL,
  `sort_order` int(11) DEFAULT '9999',
  `is_active` int(11) DEFAULT '1',
  `slider_id` int(11) DEFAULT '0',
  `external_url` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
");

mkdir(Mage::getBaseDir().'/media/imageslider', 0777, true);
$this->endSetup();
?>
