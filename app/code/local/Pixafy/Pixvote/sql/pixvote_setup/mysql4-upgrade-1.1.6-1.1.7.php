<?php
	$installer	=	$this;
	$installer->startSetup();
	$installer->addAttribute('catalog_category', 'designer_id',  array(
		'type'     => 'int',
		'label'    => 'Designer ID',
		'input'    => 'text',
		'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
		'visible'           => true,
		'required'          => false,
		'user_defined'      => false,
		'default'           => '0',
		'group'				=> 'General Information',
	));
	
	$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'design_id', array(
        'type'              => 'int',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Design ID',
        'input'             => 'text',
        'class'             => '',
        'source'            => '',
        'global'            => '0',
        'visible'           => true,
        'required'          => false,
        'user_defined'      => true,
        'default'           => '0',
        'searchable'        => false,
        'filterable'        => false,
        'comparable'        => false,
        'visible_on_front'  => false,
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