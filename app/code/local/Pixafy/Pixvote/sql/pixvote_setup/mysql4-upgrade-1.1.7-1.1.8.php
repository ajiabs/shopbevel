<?php
	$installer	=	$this;
	$installer->startSetup();
	$installer->addAttribute('catalog_category', 'ship_time',  array(
		'type'     => 'varchar',
		'label'    => 'Ship time',
		'input'    => 'date',
		'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
		'visible'           => true,
		'required'          => false,
		'user_defined'      => false,
		'default'           => '0',
		'group'				=> 'General Information',
	));
	$installer->endSetup();
?>