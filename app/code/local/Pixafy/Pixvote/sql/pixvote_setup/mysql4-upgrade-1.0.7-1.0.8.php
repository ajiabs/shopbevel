<?php
	$installer	=	$this;
	$installer->startSetup();
	$installer->run(
	"ALTER TABLE `pixafy_design_challenges`  ADD `prize` TEXT NOT NULL AFTER `judges`,  ADD `pintrest_url` VARCHAR(500) NOT NULL AFTER `prize`,  ADD `image` VARCHAR(500) NOT NULL AFTER `pintrest_url`;
	ALTER TABLE  `pixafy_design_challenges` ADD INDEX (  `name` );
	ALTER TABLE  `pixafy_design_challenges` ADD  `description` TEXT NOT NULL AFTER  `name`
	"		
	);
	$installer->endSetup();
?>