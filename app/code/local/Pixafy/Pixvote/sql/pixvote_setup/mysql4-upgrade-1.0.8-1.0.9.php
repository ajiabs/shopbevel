<?php
	$installer	=	$this;
	$installer->startSetup();
	$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'is_on_homepage', array(
        'type'              => 'int',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Display on homepage slider?',
        'input'             => 'boolean',
        'class'             => '',
        'source'            => '',
        'global'            => '0',
        'visible'           => true,
        'required'          => false,
        'user_defined'      => false,
        'default'           => '0',
        'searchable'        => false,
        'filterable'        => false,
        'comparable'        => false,
        'used_in_product_listing' => 1,
        'unique'            => false,
        'wysiwyg_enabled'   => true,
        'apply_to'          => '',
        'is_configurable'   => false,
		'attribute_set'		=> 'Default',
		'group'				=> 'General',
    ));
	$installer->addAttribute('catalog_category', 'is_on_homepage',  array(
    'type'     => 'int',
    'label'    => 'Display on homepage slider?',
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