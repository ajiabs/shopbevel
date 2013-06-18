<?php
	$installer	=	$this;
	$installer->startSetup();
	$installer->addAttribute('catalog_category', 'appear_in_vote_email',  array(
    'type'     => 'int',
    'label'    => 'Appear in vote email?',
    'input'    => 'select',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '0',
	'group'				=> 'General Information',
	'source'            => 'eav/entity_attribute_source_boolean',
));


	$installer->endSetup();
?>