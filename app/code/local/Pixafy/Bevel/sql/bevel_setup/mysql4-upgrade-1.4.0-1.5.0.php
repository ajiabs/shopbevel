<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$installer = $this;

$installer->startSetup();

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'votes', array(
        'type'              => 'varchar',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Votes',
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
