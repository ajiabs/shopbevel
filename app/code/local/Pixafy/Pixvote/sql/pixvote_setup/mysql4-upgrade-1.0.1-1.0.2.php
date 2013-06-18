<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$installer = $this;

$installer->startSetup();

$installer->addAttribute('customer', 'location', array(
        'type'              => 'varchar',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Location',
        'input'             => 'text',
        'class'             => '',
        'source'            => '',
        'global'            => '0',
        'visible'           => true,
        'required'          => false,
        'user_defined'      => true,
        'default'           => '',
        'visible_on_front'  => true,
    ));
$installer->endSetup();
?>
