<?php
	$installer	=	$this;
	$installer->startSetup();
	$installer->run("ALTER TABLE  `pixafy_designs` ADD  `color` VARCHAR( 500 ) NOT NULL DEFAULT  '0' AFTER  `price`");
	$installer->endSetup();
?>