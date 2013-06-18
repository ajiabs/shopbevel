<?php
	$installer	=	$this;
	$installer->startSetup();
	$installer->run("
			ALTER TABLE  `pixafy_designs` ADD  `materials` TEXT NOT NULL AFTER  `description`
	");
	$installer->endSetup();
?>