<?php
	$installer	=	$this;
	$installer->startSetup();
	$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'new_arrival', array(
        'type'              => 'int',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'New Arrival?',
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
	$installer->endSetup();
?>