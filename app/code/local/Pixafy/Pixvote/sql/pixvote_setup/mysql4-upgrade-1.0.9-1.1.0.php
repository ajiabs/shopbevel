<?php
	$installer	=	$this;
	$installer->startSetup();
	$installer->addAttribute('catalog_category', 'slider_position',  array(
    'type'     => 'int',
    'label'    => 'Homepage slider position',
    'input'    => 'text',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '0',
	'group'				=> 'General Information'
));
	$installer->endSetup();
?>