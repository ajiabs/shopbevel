<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$installer = $this;

$installer->startSetup();

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'is_made_by_bevel', array(
        'type'              => 'int',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Made by Bevel?',
        'input'             => 'boolean',
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
        'used_in_product_listing' => true,
        'unique'            => false,
        'wysiwyg_enabled'   => true,
        'apply_to'          => '',
        'is_configurable'   => false,
		'attribute_set'		=> 'Default',
		'group'				=> 'General',
    ));
$installer->endSetup();
?>
