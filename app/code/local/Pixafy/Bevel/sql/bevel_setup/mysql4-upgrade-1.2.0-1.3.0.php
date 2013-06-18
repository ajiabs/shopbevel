<?php

$installer = $this;
$installer->startSetup();
$installer->addAttribute('catalog_category', 'location',  array(
    'type'     => 'varchar',
    'label'    => 'Designer Location',
    'input'    => 'text',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
	'group'				=> 'General Information'
));
?>
